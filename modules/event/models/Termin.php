<?php

/**
 * This is the model class for table "{{termin}}".
 */
class Termin extends CActiveRecord
{
	// initialize it with true @see afterFind
	public $google_calendar_haschange = true;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array('class' => 'zii.behaviors.CTimestampBehavior',), //create_time, update_time
		);
	}

	public function tableName()
	{
		return '{{termin}}';
	}

	public function rules()
	{
		return array(
			array('datum, zeit', 'length', 'max'=>100),
			array('titel', 'length', 'max'=>150),
			array('untertitel, rubric', 'length', 'max'=>255),
			array('url', 'safe'),
			array('datum', 'required'),
		);
	}

	protected function afterValidate()
	{
		if (!$this->getDateRegMatch())
			$this->addError('datum', 'Datum muss das Format: TT.MM.JJJJ oder TT-TT.MM.JJJJ oder TT/TT.MM.JJJJ oder TT.MM.JJJJ-TT.MM.JJJJ haben.');
		return parent::afterValidate();
	}

	public function relations()
	{
		return array(
			'teamEvents' => array(self::HAS_MANY, 'TeamEvent', 'event_id'),
			'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'datum' => 'Datum',
			'titel' => 'Titel',
			'untertitel' => 'Untertitel',
			'zeit' => 'Zeit',
			'from' => 'Von',
			'to' => 'Bis',
			'url' => 'Webseite',
			'rubric' => 'Rubrik',
		);
	}

	protected function getDateRegMatch()
	{
		$stuff = '[A-Za-z \/\.\-]+'; // stuff which is no number and can be in a date
		$twoDigits = '(([0-9]{2})'.$stuff.')';
       	if (!preg_match('/^'.$twoDigits.$twoDigits.$twoDigits.'?'.'([0-9]{4})('.$stuff.$twoDigits.$twoDigits.$twoDigits.'?'.'([0-9]{4}))?/', $this->datum, $reg))
			return false;
		/*
			'0' => '20.-21.11.2010'
			'1' => '20.-'
			'2' => '20'
			'3' => '21.'
			'4' => '21'
			'5' => '11.'
			'6' => '11'
			'7' => '2010'
			'8' => '2010'
			----
			'0' => '03.11.2013-04.11.2013',
			'1' => '03.',
			'2' => '03',
			'3' => '11.',
			'4' => '11',
			'5' => '',
			'6' => '',
			'7' => '2013',
			'8' => '-04.11.2013',
			'9' => '04.',
			'10' => '04',
			'11' => '11.',
			'12' => '11',
			'13' => '',
			'14' => '',
			'15' => '2013',
		*/
		return $reg;
	}

	protected function getUnixTimes()
	{
		/* examples of nonstandard dates:
		20.-21.11.2010
		30.06.2011
		02.-05.07.2009 
		ab 13.09.2010 
		11./12.06.2011 
		14.-16-09.2011
		03.11.2013-04.11.2013
		*/
		$reg = $this->getDateRegMatch();
		if (!$reg)
		{
			$this->addError('datum', 'Datum muss das Format: TT.MM.JJJJ haben.');
			return array();
		}
		$day = $reg[2];
		$day2 = $day;
		if ($reg[6])
		{
			$day2 = $reg[4];
			$month = $reg[6];
		}
		else
			$month = $reg[4];
		$year = $reg[7];

		$month2 = $month;
		$year2 = $year;

		if (isset($reg[15]) && $reg[15])
		{
			$day2 = $reg[10];
			$month2 = $reg[12];
			$year2 = $reg[15];
		}

       	if (!checkdate($month, $day, $year))
	   	{
			$this->addError('datum', 'Datum: '.$day.'.'.$month.'.'.$year.' ist ungültig');
		   	return array();
		}
       	if ($day2 != $day || $month2 != $month || $year2 != $year)
			if (!checkdate($month2, $day2, $year2))
			{
				$this->addError('datum', 'Datum: '.$day2.'.'.$month2.'.'.$year2.' ist ungültig');
		  		return array();
			}

		/*
			ab 22:00 Uhr 
			19:00-21:00 Uhr 
			19:00-ca.21:00 Uhr 
			06:00-07:00 Uhr 
		*/
		$start_hour = 0;
		$start_minute = 0;
		$end_hour = 0;
		$end_minute = 0;
		// time is not so important - if no match could be found just set it to 0
       	if (preg_match('/([0-9]{1,2}):([0-9]{2})( *- *c?a?\.? *([0-9]{1,2}):([0-9]{2}))?/', $this->zeit, $reg))
		{
			/*
				'1' => '19'
				'2' => '00'
				'3' => ' -22:00'
				'4' => '22'
				'5' => '00'
			*/
			$start_hour = $reg[1];
			$start_minute= $reg[2];
			if (isset($reg[4]))
			{
				$end_hour = $reg[4];
				$end_minute = $reg[5];
			}
		}

       	//hour,minute,second
	   	return array(
	   		'from'=>mktime($start_hour, $start_minute, 0, $month, $day, $year),
			'to'=>mktime($end_hour, $end_minute, 0, $month2, $day2, $year2));
	}

 	protected function beforeSave()
    {
		$arr = $this->getUnixTimes();
		// dont save on error
		if ($this->hasErrors())
			return;
		$this->from = $arr['from'];
		$this->to = $arr['to'];

		if ($this->to <= $this->from)
			$this->to = $this->from+1;

		$this->page_id = 0;
		if (strpos($this->url, 'balance-dresden.info') || strpos($this->url, 'http') === false)
		{
			$key = false;
			if (strpos($this->url, 'balance-dresden.info'))
			{
				// look if we can create a /page/page/get out of this
				$parts = explode('/', trim($this->url, '/'));
				if (count($parts) > 2)
					$key = $parts[count($parts)-1];
			}
			else
				$key = $this->url;
			if ($key)
			{
				$page = Page::model()->findByAttributes(array('key'=>$key, 'active'=>true));
				if ($page && $page->id)
					$this->page_id = $page->id;
			}
		}
       	return parent::beforeSave();
	}

	public function getDate()
	{
		if ($this->datum == '')
			return date('d.m.y', $this->from);
		return $this->datum;
	}

	public function getTime()
	{
		if ($this->zeit == '')
			return date('h:m', $this->from);
		return str_replace('--', '<br/>', CHtml::encode($this->zeit));
	}

	public static $teamModels;
	public function getUntertitel($export=false, $links = true, $htmlentities=true)
	{
		$text = str_replace('--', '<br/>', CHtml::encode($this->untertitel));
		if (!self::$teamModels)
		{
			self::$teamModels = Team::model()->findAll();
		}

		foreach (self::$teamModels as $model)
		{
			$alias = array($model->name);
			if ($htmlentities)
				$alias[] = htmlentities($model->name);
			else
				$alias[] = $model->name;
			if ($model->name == 'Milam M. Horn')
			{
				$alias[] = 'Milam Horn';
				$alias[] = 'Milam';
			}
			if ($model->name == 'Katarina Heidenreich')
			{
				$alias[] = 'Katarina';
				$alias[] = 'K.Heidenreich';
			}
			foreach ($alias as $name)
			{
				if (strpos($text, $name) !== false)
				{
					$this->teamLinks[] = $model->key;
					if ($links)
					{
						if ($export)
							$text = str_replace($name,
								CHtml::link(substr($name, 0, 1),
									Yii::app()->controller->createAbsoluteUrl('//page/team/get', array('key'=>$model->key)),
									array('style'=>'#placeholderpersonstyle#', 'title'=>'Teamseite'))
								.substr($name, 1),
								$text);
						else
							$text = str_replace($name,
								CHtml::link('<i class="glyphicon glyphicon-user"></i>', array('/page/team/get', 'key'=>$model->key), array('rel'=>'nofollow'))
								." ".$name,
								$text);
					}
					break;
				}
			}
		}
		return $text;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('datum',$this->datum,true);

		$criteria->compare('titel',$this->titel,true);

		$criteria->compare('untertitel',$this->untertitel,true);

		$criteria->compare('zeit',$this->zeit,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			 'sort'=>array(
			 	'defaultOrder'=>'from DESC',
			)
		));
	}

	public function hasAccess($type=null)
	{
		if (is_null($type))
			$type = $this->scenario;
		switch ($type)
		{
			case 'delete':
				return !Yii::app()->user->isGuest;
		}
		return false;
	}

	public function getUrl()
	{
		if (strpos($this->url, 'balance-dresden.info'))
		{
			if ($this->page_id && $this->page)
				return $this->page->getUrl();
		}

		if ($this->isExternUrl())
			return $this->url;
		else if (strpos($this->url, '.de') || strpos($this->url, '.com') || strpos($this->url, '.info'))
			return 'http://'.$this->url;
		return array('/page/page/get', 'key'=>$this->url);
	}

	public function isExternUrl()
	{
		if ($this->page_id)
			return false;
		return (substr($this->url, 0, 7) == 'http://' || substr($this->url, 0, 8) == 'https://');
	}

	protected function afterFind()
	{
		// set the change-variable to true so when we save this without explicitely
		// setting it to false, it will trigger an update
		$this->google_calendar_haschange = true;
		return parent::afterFind();
	}

	public $teamLinks = array();
	public function afterSave()
	{
		parent::afterSave();

		$this->getUntertitel();
		$links = array_unique($this->teamLinks);
		foreach ($this->teamEvents as $t)
			$t->delete();
		foreach ($links as $key)
		{
			$tm = Team::model()->findCachedByKey($key);
			$tId = $tm->id;
			$t = new TeamEvent();
			$t->attributes = array('event_id'=>$this->id, 'team_id'=>$tId);
			$t->save();
		}
	}
}
