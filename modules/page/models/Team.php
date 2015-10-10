<?php
Yii::import('application.modules.event.models.Event');

/**
 * more attributes
 * webpage
 * email
 * mobile phone
 * private phone
 * enabled
 */

/**
 * This is the model class for table "{{team}}".
 */
class Team extends CActiveRecord
{
	// TODO move this attribtute into the DB
	public $visible = 1;

	public $infoFields = array(
			'name' => '',
			'web' => 'Webseite',
			'email' => 'Email',
			'mobile' => 'Mobil',
			'phone' => 'Telefon',
		);

	public function getContact()
	{
		$html = ''; // CHtml::tag('strong', array(), 'Kontakt');
		$info = $this->infoFields;
		foreach ($info as $k=>$v) {
			$value = '';
			$value = $this->$k;
			if ($value)
			{
				if ($k == 'name')
					$value = $this->getTeamLink() . $value;
				if ($k == 'web')
					$value = CHtml::link(str_replace(['http://','https://'], '', $value), $value, array('target'=>'_blank'));
				if ($k == 'email')
					$value = CHtml::mailto($value, $value.'?subject=Kontakt%20vom%20Balance&body=Hallo%20'.str_replace('
					','%20',$this->name).',', array('target'=>'_blank'));
			}

			$info[$k] = array($v, $value);
		}

		foreach ($info as $k=>$v) {
			if (!$v[1])
				continue;
			if ($v[0])
				$html .= $v[0].': ';
			$html .= $v[1];
			$html .= CHtml::tag('br');
		}
		return $html;
	}

	public $delete = false;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Team the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{team}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('key, name, image, text, sort', 'required'),
			array('sort', 'numerical', 'integerOnly'=>true),
			array('name, key, web, mobile, phone, email, subheader, image', 'length', 'max'=>255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'pages'  => array(self::MANY_MANY, 'Page', Yii::app()->db->tablePrefix .'team_page(team_id,page_id)' ),
			'events'  => array(self::MANY_MANY, 'Event', Yii::app()->db->tablePrefix .'team_event(team_id,event_id)',
				'condition'=>'`to`>'.(time()-60*30), 'order'=>'`from` ASC'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'key' => 'Eindeutiger Schlüssel',
			'subheader' => 'Untertitel bei Überschrift',
			'image' => 'Bild',
			'sort' => 'Sortierung',
			'text' => 'Text',
			'delete' => 'Löschen',
		);
	}

	protected static $teamCacheByKey;
	public function findCachedByKey($key)
	{
		if (!self::$teamCacheByKey)
		{
			self::$teamCacheByKey = array();
			foreach ($this->findAll() as $m)
			{
				self::$teamCacheByKey[$m->key] = $m;
			}
		}
		if (!isset(self::$teamCacheByKey[$key]))
		{
			diedump(array($key, self::$teamCacheByKey), 2);
		}
		return self::$teamCacheByKey[$key];
	}

	public function getTeamLink()
	{
		return CHtml::link('<i class="glyphicon glyphicon-user"></i>', array('/page/team/get', 'key'=>$this->key), array('rel'=>'nofollow'));
	}
}
