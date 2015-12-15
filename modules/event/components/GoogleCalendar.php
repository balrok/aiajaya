<?php

class GoogleCalendarVoid
{
	public function getAccess() {}
	public static function getInstance() {}
	public function insertUpdateTermin($termin) {}
	public function deleteTermin($termin) {}
	public function getAllEvents() {}
	public function deleteByGId($gId) {}
}

class GoogleCalendar
{
	static $instance;
	protected $calendar;

	protected $calendarId;

	protected function __construct()
	{
		$this->calendarId = Yii::app()->getModule('event')->calendarId;
	}

	public static function getInstance()
	{
		if (!self::$instance)
		{
			if (Yii::app()->user->isGuest || !Yii::app()->getModule('event')->calendarEnabled)
				self::$instance = new GoogleCalendarVoid();
			else
				self::$instance = new GoogleCalendar();
		}
		return self::$instance;
	}

	public function getAccess()
	{
		$this->getCalendar();
	}

	protected function getCalendar()
	{
		if (!$this->calendar)
		{
			Yii::app()->session['return_url'] = Yii::app()->request->requestUri;
			if (!isset(Yii::app()->session['auth_token']))
				Yii::app()->controller->redirect(array('/event/calendar/login'));
			elseif(Yii::app()->session['auth_token'] == 'access_denied')
			{
				Yii::app()->getModule('event')->calendarEnabled = false;
				return;
			}
			$api = Yii::app()->GoogleApis;
			$this->calendar = $api->serviceFactory('Calendar');
			$client = $api->getClient();
			try {
				$client->setAccessToken(Yii::app()->session['auth_token']);
			} catch (Google_AuthException $e) {
				Yii::app()->controller->redirect(array('/event/calendar/login'));
			}
		}
		return $this->calendar;
	}

	protected function createGoogleEventFromTermin($termin, $event=null)
	{
		// 'id' => '761',
		// 'datum' => '10.10.2012',
		// 'titel' => 'Dynamische Meditation',
		// 'untertitel' => 'mit Milam Horn --Anm: 0175-86 57 233',
		// 'zeit' => '06:00 Uhr',
		// 'url' => '',
		// 'from' => '1349848800',
		// 'to' => '1349848801',
		// 'gId' => '',
		if (!$event)
			$event = new Google_Event();
		$event->setSummary($termin->titel);
		$untertitel = str_replace('--', '<br/>', $termin->untertitel);
		$untertitel .= '<hr/>'.str_replace('--', '<br/>', $termin->datum).' '.str_replace('--', '<br/>', $termin->zeit);
		if ($termin->url)
			$untertitel .= '<hr/><a href="'.$termin->url.'">Weitere Informationen</a>';
		$event->setDescription($untertitel);

		$to = $termin->to;
		$from = $termin->from;
		$days = 1;
		while ($to - $from > 60*60*24)
		{
			$to -= 60*60*24;
			$days++;
		}

		$start = new Google_EventDateTime();
		$start->setDateTime(date('Y-m-d\TH:i:s', $from));
		$start->setTimezone('Europe/Berlin');
		$event->setStart($start);

		$end = new Google_EventDateTime();
		$end->setDateTime(date('Y-m-d\TH:i:s', $to));
		$end->setTimezone('Europe/Berlin');
		$event->setEnd($end);

		if ($days > 1)
		{
			$freq = "RRULE:FREQ=DAILY;COUNT=".$days;
			$event->setRecurrence(array($freq));
		}
		return $event;
	}

	protected function getEventByTermin($termin)
	{
		$event = null;
		try {
			if ($termin->gId)
				$event = new Google_Event($this->getCalendar()->events->get($this->calendarId, $termin->gId));
		} catch (Google_ServiceException $e) {
			if ($e->getCode() == 404)
			{
				$termin->gId = 0;
				$termin->save();
			}
			else
			{
				throw $e;
			}
		}
		return $event;
	}

	public function insertUpdateTermin($termin)
	{
		$cal = $this->getCalendar();

		$event = $this->getEventByTermin($termin);
		$event = $this->createGoogleEventFromTermin($termin, $event);

		try {
			if ($termin->gId)
				$ret = $cal->events->update($this->calendarId, $termin->gId, $event);
			else
				$ret = $cal->events->insert($this->calendarId, $event);
		} catch (Google_ServiceException $e)
		{
			if ($e->getCode() == 403)
			{
				// set gId to 0 to reflect that this one needs still an update
				if ($termin->gId)
				{
					$termin->gId = 0;
					$termin->save();
				}
				// now try to regain access
				Yii::app()->session['auth_token'] = '';
				$this->calendar = null;
				$this->getCalendar();
				return;
			}
			throw $e;
		}

		$termin->gId = $ret['id'];
		$termin->google_calendar_haschange = false;
		$termin->save();
	}

	public function deleteTermin($termin)
	{
		if ($termin->gId)
			$this->deleteByGId($termin->gId);
	}

	public function getAllEvents()
	{
		static $items = array();

		if (empty($items))
		{
			$cal = $this->getCalendar();
			$calendarData = $cal->events->listEvents($this->calendarId, array('maxResults'=>9999, 'showDeleted'=>false));

			$items = array();
			if (isset($calendarData['items']))
				foreach ($calendarData['items'] as $k=>$data)
				{
					// some events (maybe it has to do with recurring show up more than once
					// and the second time the id will be like this: 
					//  "id": "1tjg5s1jq8dcdttumnjkka7e88_20130911T080000Z",
					//  "status": "cancelled",
					//  "recurringEventId": "1tjg5s1jq8dcdttumnjkka7e88",
					// just filter those out..
					if (strpos($data['id'], '_'))
						continue;
					$items[$data['id']] = true;
				}
		}

		return $items;
	}

	public function deleteByGId($gId)
	{
		try {
			$this->getCalendar()->events->delete($this->calendarId, $gId);
		} catch (Exception $e)
		{
			if ($e->getCode() == 404)
			{
				throw new Exception("GId $gId doesn't exist");
			}
			throw $e;
		}
	}
}
