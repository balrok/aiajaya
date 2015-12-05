<?php

class Menu extends CActiveRecord
{
	// can be set to true by a form input - but delete it then manually
	public $delete = false;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{menu}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sort, page_id, parent_id', 'numerical', 'integerOnly'=>true),
			array('name, img', 'length', 'max'=>255),
		);
	}

	public function relations()
	{
		return array(
			'parentMenu' => array(self::BELONGS_TO, 'Menu', 'parent_id'),
			'childMenus' => array(self::HAS_MANY, 'Menu', 'parent_id'),
			'page' => array(self::HAS_ONE, 'Page', 'page_id'),
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
			'img' => 'Bild',
			'page_id' => 'Seite',
			'parent_id' => 'Übergeordnetes Menü',
			'sort' => 'Sortierung',
			'delete' => 'Löschen',
		);
	}

	public function getImg()
	{
		if (substr($this->img, 0, 4) == 'http')
			return $this->img;
		else
			return Yii::app()->baseUrl.'/bilder/'.$this->img;
	}
}
