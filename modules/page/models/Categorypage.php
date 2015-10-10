<?php

/**
 * This is the model class for table "{{categorypage}}".
 *
 * The followings are the available columns in table '{{categorypage}}':
 * @property string $id
 * @property string $categoryname
 * @property string $categorykey
 * @property string $pagename
 * @property string $pagekey
 * @property string $pageimg
 * @property integer $sort
 */
class Categorypage extends CActiveRecord
{
	public $delete = false;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Categorypage the static model class
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
		return '{{categorypage}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryname, categorykey, pagename, pagekey, pageimg, sort', 'required'),
			array('sort', 'numerical', 'integerOnly'=>true),
			array('categoryname, categorykey, pagename, pagekey, pageimg', 'length', 'max'=>255),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'categoryname' => 'Kategoriename',
			'categorykey' => 'Kategorieschlüssel',
			'pagename' => 'Name',
			'pagekey' => 'Schlüssel',
			'pageimg' => 'Bild',
			'sort' => 'Sortierung',
			'delete' => 'Löschen',
		);
	}

	public function getPageImg()
	{
		if (substr($this->pageimg, 0, 4) == 'http')
			return $this->pageimg;
		else
			return Yii::app()->baseUrl.'/bilder/'.$this->pageimg;
	}
}
