<?php

class SubscribeController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users
				'users'=>array('*'),
			),
		);
	}

	public function actionForm($key)
	{
		if (!preg_match("/^[a-z0-9A-z_]+$/", $key))
		{
			$this->render('form/404');
		}

		try {
			$this->render("form/".$key);
		} catch (CException $e)
		{
			diedump($e);
			if (substr($e->getMessage(),0,19) == 'SubscribeController')
			{
				$this->render('form/404');
			}
			else
			{
				throw($e);
			}
		}
	}
}
