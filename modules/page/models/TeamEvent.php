<?php

Yii::import('application.modules.page.models.TeamLink');
Yii::import('application.modules.event.models.Event');

class TeamEvent extends TeamLink
{
	protected $type = 'event';
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
