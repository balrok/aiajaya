<?php

class PageSize extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{pagesize}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('category, pagesize', 'required'),
			array('pagesize', 'numerical', 'integerOnly'=>true),
			array('category', 'length', 'max'=>255),
		);
	}

	public function relations()
	{
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'category' => 'Bereich',
			'pagesize' => 'Elemente pro Seite',
		);
	}


	// This is the core of that class
	static function getSetOrDefault($category, $set, $default = 15)
	{
		if ($default <= 0)
			die("Default Pagesize must be larger than 0");

		$model = PageSize::model()->findByAttributes(array('category'=>$category));
		if (!$model)
		{
			$model = new PageSize;
			$model->category = $category;
		}

		if ($set > 0)
		{
			$model->pagesize = $set;
			$model->save();
		}
		if ($model->pagesize <= 0)
		{
			$model->pagesize = $default;
			$model->save();
		}

		return $model->pagesize;
	}
}
