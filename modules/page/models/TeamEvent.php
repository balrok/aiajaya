<?php

Yii::import('aiajaya.modules.page.models.TeamLink');
Yii::import('aiajaya.modules.event.models.Event');

class TeamEvent extends TeamLink
{
	protected $type = 'event';
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
