<?php

class DefaultController extends Controller
{
	public $defaultAction = 'index';

	public function actionIndex($cat='')
	{
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/../../assets');
		Yii::app()->clientScript->registerScriptFile($assets.'/flowplayer/flowplayer-3.2.12.min.js');
		Yii::app()->clientScript->registerCssFile($assets.'/css/galerie.css');

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
