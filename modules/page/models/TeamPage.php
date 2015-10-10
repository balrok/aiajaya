<?php

class TeamPage extends TeamLink
{
	protected $type = 'page';
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
