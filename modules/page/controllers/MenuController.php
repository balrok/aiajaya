<?php

class MenuController extends Controller
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
		if ($key == 'mainmenu')
			$id = 0;
		else
		{
			$m = Menu::model()->findbyAttributes(array('key'=>$key));
			if (!$m)
			{
				$this->render404();
			}
			$id = $m->id;
		}
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		$models = Menu::model()->findAllbyAttributes(array('parent_id'=>$id), $criteria);

		$new = Menu::model();
		$new->parent_id = $id;
		if ($models)
			$new->sort = end($models)->sort + 10;
		else
			$new->sort = 10;

		$models[] = $new;

		if(isset($_POST['Menu']))
		{
			$hasError = false;
			foreach ($models as $k=>$model)
			{
				$mid = (int)$model->id; // to int because of null != '0' but 0 == '0'
				if (!isset($_POST['Menu'][$mid]))
					continue;
				$models[$k]->attributes = $_POST['Menu'][$mid];
				if (isset($_POST['Menu'][$mid]['delete']) && $_POST['Menu'][$mid]['delete'])
				{
					if (!$models[$k]->delete())
						$hasError = true;
				}
				else
				{
					if (!$models[$k]->save())
					{
						// TODO how to distinct between nothing entered
						// if ($model->id == 0 && count($model->getErrors()) == 3)
						// {
						// 	// this is for new created models, where nothing was entered - so don't display the error
						// 	$models[$k]->clearErrors();
						// }
						// else
						// {
						$hasError = true;
						// }
					}
				}
				diedump($models);
			}
			if (!$hasError)
				$this->redirect(array('update', 'key'=>$key));
		}
		$this->render('edit',array(
			'models'=>$models,
			'allpages'=>$this->getAllPagesForAutocomplete(),
			'allimages'=>$this->getAllImagesForAutocomplete(),
		));
	}

	public function actionGet($key)
	{
		$parent = Menu::model()->findbyAttributes(array('key'=>$key));
		if (!$parent)
			$this->render404();
		$id = $parent->id;
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		if ($models = Menu::model()->findAllbyAttributes(array('parent_id'=>$id, $criteria)))
		{
			$this->pageTitle = $parent->name.' '. $this->pageTitle;
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
		// TODO make this extendable
		$allpages[] = array(
			'name' => 'termine',
			'label' => 'Termine',
			'id'=>0,
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
		$id = $model->id;

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
			$parent = Menu::model()->findbyAttributes(array('key'=>$key));
			if (!$parent)
			{
				// TODO error handling?
				continue;
			}
			$id = $parent->id;
			$models = Menu::model()->findAllbyAttributes(array('parent_id'=>$id), $criteria);
			if ($models != array())
				$multiModels[] = $models;

		}
		$this->render('multi', array(
			'multiModels'=>$multiModels,
			));
	}

	public function actionList()
	{
		$criteria = new CDbCriteria;
		$criteria->order = "sort";
		$models = Menu::model()->findAllbyAttributes(array('parent_id'=>0), $criteria);

		$this->render('edit', array(
			'models'=>$models,
			'allpages'=>$this->getAllPagesForAutocomplete(),
			'allimages'=>$this->getAllImagesForAutocomplete(),
			));
	}
}
