<?php

class CategorypageController extends Controller
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
				'actions'=>array('get', 'multi'),
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
	public function actionUpdate($key)
	{
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		$models = Categorypage::model()->findAllbyAttributes(array('categorykey'=>$key), $criteria);
		$new = new Categorypage();
		$new->categorykey = $key;
		$new->categoryname = end($models)->categoryname;
		$new->sort = end($models)->sort + 10;
		$models[] = $new;
		if ($models)
		{
			if(isset($_POST['Categorypage']))
			{
				$hasError = false;
				foreach ($models as $k=>$model)
				{
					$id = (int)$model->id; // to int because of null != '0' but 0 == '0'
					if (!isset($_POST['Categorypage'][$id]))
						continue;
					$models[$k]->attributes = $_POST['Categorypage'][$id];
					if (isset($_POST['Categorypage'][$id]['delete']) && $_POST['Categorypage'][$id]['delete'])
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
					$this->redirect(array('update', 'key'=>$key));
			}

			$this->render('update',array(
				'models'=>$models,
				'allpages'=>$this->getAllPagesForAutocomplete(),
				'allimages'=>$this->getAllImagesForAutocomplete(),
			));
		}
	}

	// for testing purposes it is possible to use files here - filename must be the same as the keyname
	public function actionGet()
	{
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		if ($models = Categorypage::model()->findAllbyAttributes(array('categorykey'=>$_GET['key']), $criteria))
		{

			$this->pageTitle = $models[0]->categoryname .' '. $this->pageTitle;
			$this->render('get', array(
				'models'=>$models,
				));
		}
	}

	public function getAllPagesForAutocomplete()
	{
		$allpages = array();
		foreach (Page::model()->findAll() as $model)
			$allpages[] = array(
				'name'=>$model->meta_title,
				'label'=>$model->meta_title,
				'id'=>$model->key,
			);
		return $allpages;
	}

	public function getAllImagesForAutocomplete()
	{
		$allimages = array();
		$dir = YiiBase::getPathOfAlias('webroot.bilder');
		$baseUrl = Yii::app()->baseUrl;
		if (!is_dir($dir))
		{
			$dir = YiiBase::getPathOfAlias('webroot.themes.'.Yii::app()->theme->name.'.bilder');
			$baseUrl = Yii::app()->theme->baseUrl;
		}
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir),
												  RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iterator as $obj) {

			if ($obj->isFile())
			{
				$file = $obj;
				if (in_array(strtolower($file->getExtension()), array('jpg', 'jpe', 'jpeg', 'gif', 'png')))
				{
					$path = str_replace($dir, '', $file->getPath()).'/'.$file->getFilename();
					if (strpos($path, 'gallery') && strpos($path, 'big'))
						continue;
					$allimages[] = array(
						'name'=>$file->getFilename(),
						'id'=>trim($path,'/'),
						'label'=>'js:\''.CHtml::image($baseUrl.'/bilder'.$path, '', array('width'=>'50')).' '.$file->getFilename().'\'',
					);
				}
			}
		}
		return $allimages;
	}

	public function actionMulti($multi)
	{
		$this->pageTitle = 'Viele Angebote im '. $this->pageTitle;
		foreach (array(' ', '+', ',') as $char)
		{
			if (strpos($multi, $char))
			{
				$multi = explode($char, $multi);
				break;
			}
		}
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		$multiModels = array();
		foreach ($multi as $key)
		{
			$models = Categorypage::model()->findAllbyAttributes(array('categorykey'=>$key), $criteria);
			if ($models != array())
				$multiModels[] = $models;

		}
		$this->render('multi', array(
			'multiModels'=>$multiModels,
			));
	}
}
