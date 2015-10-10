<?php

class TeamController extends Controller
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
			array('allow',  // allow all users to perform 'get' actions
				'actions'=>array('get', 'vcard'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user
				'users'=>array('@'),
			),
			array('deny',  // deny all guests
				'users'=>array('guest'),
			),
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		$models = Team::model()->findAll($criteria);
		$new = new Team();
		$last = end($models);
		$new->sort = $last?$last->sort+10:10;
		$models[] = $new;
		if ($models)
		{
			if(isset($_POST['Team']))
			{
				$hasError = false;
				foreach ($models as $k=>$model)
				{
					$id = (int)$model->id; // to int because of null != '0' but 0 == '0' -> to insert new models
					if (!isset($_POST['Team'][$id]))
						continue;
					$models[$k]->attributes = $_POST['Team'][$id];
					if (isset($_POST['Team'][$id]['delete']) && $_POST['Team'][$id]['delete'])
					{
						if (!$models[$k]->delete())
							$hasError = true;
					}
					else
					{
						if (!$models[$k]->save())
						{
							if ($model->id == 0 && count($model->getErrors()) == 3)
							{
								// this is for new created models, where nothing was entered - so don't display the error
								$models[$k]->clearErrors();
							}
							else
							{
								$hasError = true;
							}
						}
					}
				}
				if (!$hasError)
					$this->redirect(array('update'));
			}

			$this->render('update',array(
				'models'=>$models,
				'allimages'=>$this->getAllImagesForAutocomplete(),
			));
		}
	}

	public function actionGet($key=null)
	{
		$criteria = new CDbCriteria;
		if (!$key)
			$criteria->compare('t.`visible`', 1);
		if ($key)
			$criteria->compare('t.`key`', $key);
		$criteria->order = "sort";
		// not with('events') because i have a condition and order there
		if ($models = Team::model()->with('pages')->findAll($criteria))
		{

			if (count($models) == 1)
				$this->pageTitle = $models[0]->name .' - '. $this->pageTitle;
			else
			{
				$this->pageTitle = 'Team vom ' . $this->pageTitle;
				ShortWidgets::addJsShorten(".text", 350);
			}
			$this->render('get', array(
				'models'=>$models,
				));
		}
	}

	public function actionVcard($key)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('t.`key`', $key);
		// not with('events') because i have a condition and order there
		if ($model = Team::model()->find($criteria))
		{
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/vcard');
			$this->renderPartial('vcard', array(
				'model'=>$model,
				));
		}
	}

	public function getAllImagesForAutocomplete()
	{
		$allimages = array();
		$dir = YiiBase::getPathOfAlias('webroot.bilder');
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir),
												  RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iterator as $obj)
		{
			if ($obj->isFile())
			{
				$file = $obj;
				if (in_array(strtolower($file->getExtension()), array('jpg', 'jpe', 'jpeg', 'gif', 'png')))
				{
					$path = str_replace($dir, '', $file->getPath()).'/'.$file->getFilename();
					if (strpos($path, 'gallery'))
						continue;
					$allimages[] = array(
						'name'=>$file->getFilename(),
						'id'=>trim($path,'/'),
						'label'=>'js:\''.CHtml::image(Yii::app()->baseUrl.'/bilder'.$path, '', array('width'=>'50')).' '.$file->getFilename().'\'',
					);
				}
			}
		}
		return $allimages;
	}
}
