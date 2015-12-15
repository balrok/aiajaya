<?php
class CalendarController extends Controller
{
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index', 'ajaxCalendar'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user
				'users'=>array('@'),
			),
			array('deny',  // deny all guests
				'users'=>array('guest'),
			),
		);
	}

	public function actionOauth2callback()
	{
		$api = Yii::app()->GoogleApis;
		$cal = $api->serviceFactory('Calendar');
		$client = $api->getClient();
		try {
			Yii::app()->session['auth_token'] = $client->authenticate();
		} catch (Exception $e)
		{
			die($e->getMessage());
		}
		if (Yii::app()->session['auth_token'])
			$this->redirect(Yii::app()->session['return_url']);
	}

	public function actionLogin()
	{
		$api = Yii::app()->GoogleApis;
		$cal = $api->serviceFactory('Calendar');
		$client = $api->getClient();
		$client->authenticate();
	}

	public function actionIndex($id=0)
	{
		// register rss feed
		Yii::app()->clientScript->registerLinkTag('alternate', 'application/rss+xml', $this->createAbsoluteUrl('/event/termin/feed'),
			null, array('title' => 'RSS Feed von Balance - Zentrum für Energie- und Körperarbeit') );
		$model = null;
		if ($id)
			$model = Termin::model()->findByPk($id);
		$this->render('index',array(
			'logged_in'=>!Yii::app()->user->isGuest && !isset($_GET['normal']),
			'model'=>$model,
			));
	}

	protected function getCalendar()
	{
		// this is anyway more for debugging so enable it always
		Yii::app()->getModule('event')->calendarEnabled = true;
		return GoogleCalendar::getInstance();
	}

	public function actionListevents()
	{
		$cal = $this->getCalendar();
		$items = $cal->getAllEvents();

		$items = new CArrayDataProvider($items);
		$this->render('listevents', array(
			'items'=>$items,
			));
	}

	public function actionUpdateevents()
	{
		$cal = $this->getCalendar();
		$count = 0;
		foreach (Termin::model()->findAllByAttributes(array(), 'gId<>""') as $termin)
		{
			$count++;
			$cal->insertUpdateTermin($termin);
		}
		Yii::app()->user->setFlash('success', 'Erfolgreich '.$count.' hochgeladen');
		$this->redirect('index');
	}

	/**
	 * synchronizes local events with gcalendar
	 * when the GET-parameter &force=1 is set then it will first remove all events
	 * this might be useful when the formatting has changed
	 * 1. a) marks all events for update which have a gid which isn't found in gcalendar
	 *    b) when force is set will mark all with gId for update
	 * 2. removes all gevents which were locally removed
	 * 3. inserts all events without gId or which were marked in 1.a)
	 */
	public function actionSynchronizeEvents()
	{
		$cal = $this->getCalendar();
		
		$eventCollection = Termin::model()->findAllByAttributes(array(), 'gId<>""');

		$gIds = $cal->getAllEvents();
		if (isset($_GET['force']))
		{
			foreach ($gIds as $gId=>$data)
				$cal->deleteByGId($gId);
			$gIds = array();
		}
		// 1.
		foreach ($eventCollection as $termin)
		{
			// a)
			if (!isset($gIds[$termin->gId]))
			{
				$termin->gId = '';
				$termin->save();
			}
		}

		// 2.
		$gIds = $this->findDeletedEvents($eventCollection);
		foreach ($gIds as $gId)
			$cal->deleteByGId($gId);
		if (count($gIds))
			Yii::app()->user->setFlash('success', Yii::app()->user->getFlash('success').'<br/>'.'Erfolgreich '.count($gIds).' gelöscht');

		// 3.
		$count = 0;
		foreach (Termin::model()->findAllByAttributes(array('gId'=>'')) as $termin)
		{
			$count++;
			$cal->insertUpdateTermin($termin);
		}
		foreach (Termin::model()->findAllByAttributes(array('google_calendar_haschange'=>true)) as $termin)
		{
			$count++;
			$cal->insertUpdateTermin($termin);
		}
		Yii::app()->user->setFlash('success', 'Erfolgreich '.$count.' hochgeladen');
		
		$this->redirect('index');
	}

	// returns a list of gIds of events which are deleted on the balance page
	// but not inside the google calendar
	// TODO might be imperformant when one day many gevents are inside the calendar
	// better idea would be to log deleted events somewhere or make a delete of an event
	// that it won't disappear from the db
	protected function findDeletedEvents($eventCollection)
	{
		$cal = $this->getCalendar();
		$gIds = $cal->getAllEvents();

		foreach ($eventCollection as $termin)
			unset($gIds[$termin->gId]);

		return array_keys($gIds);
	}

	public function actionAjaxCalendar()
	{
        $this->renderPartial('aiajaya.modules.event.views.terminnav', array('disable'=>'pager'));
		return $this->renderPartial('_calendar', array(
			'startDate'=>'',
		));
	}
}
