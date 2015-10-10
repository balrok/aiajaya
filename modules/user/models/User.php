<?php

/**
 * This is the model class for table "{{user}}".
 */
class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{user}}':
	 * @var integer $id
	 * @var string $name
	 * @var string $password
	 * @var integer $active
	 */

	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}

	public function tableName()	{
		return '{{user}}';
	}

	public function rules()
	{
		return array(
			array('name, password, active', 'required'),
			array('active', 'numerical', 'integerOnly'=>true),
			array('name, password', 'length', 'max'=>255),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'password' => 'Password',
			'active' => 'Active',
		);
	}

	public function validatePassword($pw)
	{
		return $this->password == $this->hashPassword($pw);
	}

	public function hashPassword($pw)
	{
		return sha1($pw);
	}
}
