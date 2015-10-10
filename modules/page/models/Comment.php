<?php

class Comment extends CActiveRecord
{
	public $baseModel = null;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{comments}}';
	}

	public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'createDate',
				'updateAttribute' => null,
				// need special DbExpression when db is sqlite
				'timestampExpression' => (strncasecmp('sqlite', $this->dbConnection->driverName, 6)===0) ?
					new CDbExpression("datetime('now')") : null,
			),
		);
	}

	public function rules()
	{
		return array(
			array('pageId',  'validatePageId'),
			array('message', 'length', 'min'=>5),
			array('message', 'required'),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, message', 'safe', 'on'=>'search'),
		);
	}

	public function validatePageId()
	{
		$commentableModel = Page::model();
		if ($commentableModel->findByPk($this->pageId) === null) {
			throw new CException('comment related record does not exist!');
		}
	}

	public function relations()
	{
		return array(
			'page' => array(self::BELONGS_TO, "Page", 'pageId'),
		);
	}

	public function getBaseModel()
	{
		if ($this->baseModel)
			return $this->baseModel;
		$commentedModel = Page::model();
		return $commentedModel->findByPk($this->pageId);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'message' => Yii::t('comment', 'Nachricht'),
			'createDate' => Yii::t('comment', 'Datum'),
			'pageId' => Yii::t('comment', 'Seite'),
		);
	}
}
