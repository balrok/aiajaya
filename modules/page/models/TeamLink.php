<?php

class TeamLink extends CActiveRecord
{
	public function tableName()
	{
		return '{{team_'.$this->type.'}}';
	}

	public function rules()
	{
		return array(
			array('team_id, '.$this->type.'_id', 'required'),
			array('id, team_id, '.$this->type.'_id', 'numerical', 'integerOnly'=>true),
		);
	}

	public function relations()
	{
		return array(
			'team' => array(self::BELONGS_TO, 'Team', 'team_id'),
			$this->type => array(self::BELONGS_TO, ucwords($this->type), $this->type.'_id'),
		);
	}
}
