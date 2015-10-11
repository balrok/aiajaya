<?php

class DefaultController extends Controller
{
	public $defaultAction = 'index';

	public function actionIndex($cat='')
	{
		Yii::app()->clientScript->registerCssFile($this->baseUrl.'css/galerie.css');

		$galleries = array();
		if ($cat)
		{
			$categories = explode(',', $cat);
			foreach ($categories as $c)
				$galleries[] = new Gallery($c);
		}

		$this->render('galerie', array(
			'galleries' => $galleries,
		));
	}
}
