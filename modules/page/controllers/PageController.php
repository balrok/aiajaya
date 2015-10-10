<?php

class PageController extends Controller
{
	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

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
				'actions'=>array('get', 'search'),
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
	 * Displays a particular model.
	 */
	public function actionView()
	{
		if (!($model = $this->loadModel()))
			return;
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Page;

		if(isset($_POST['Page']))
		{
			$model->attributes=$_POST['Page'];
			if (isset($_POST['tags']))
				$model->setTags($_POST['tags']);
			if($model->save())
				$this->redirectModel($model);
		}
		else if(isset($_GET['key']))
			$model->key = $_GET['key'];

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function redirectModel($model, $statusCode = 302)
	{
		$this->redirect(array('get','key'=>$model->key), true, $statusCode);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		if (!($model = $this->loadModel()))
			return;

		if(isset($_POST['Page']))
		{
			$model->attributes=$_POST['Page'];
			if (isset($_POST['tags']))
				$model->setTags($_POST['tags']);
			if($model->save())
				$this->redirectModel($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			if (!($model = $this->loadModel()))
				return;
			$model->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		$this->redirect(array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Page('search');
		if(isset($_GET['Page']))
			$model->attributes=$_GET['Page'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($noRender=false)
	{
		if($this->_model===null)
		{
			if(isset($_GET['key']))
				$this->_model=Page::model()->findbyAttributes(array('key'=>$_GET['key']));
			elseif(isset($_GET['id']))
				$this->_model=Page::model()->findbyPk($_GET['id']);
			if($this->_model===null && !$noRender)
			{
				if (!Yii::app()->user->isGuest)
					$this->bottomAdmin = CHtml::link('Seite anlegen', array('page/create', 'key'=>$_GET['key']));
				$this->render404();
				return false;
			}
			if ($noRender && $this->_model===null)
				return false;
		}
		return $this->_model;
	}

	public function getImageUrl($prefix='')
	{
		return $prefix.$this->imageUrl;
	}

	// for testing purposes it is possible to use files here - filename must be the same as the keyname
	public function actionGet()
	{
		if (!($model = $this->loadModel(true)))
		{
			$forwardArray = array(
				//'massage_0'=>array('monika_mueller'),
			);
			if (isset($forwardArray[$_GET['key']]))
			{
				$tmp = Page::model();
				$tmp->key = $forwardArray[$_GET['key']][0];
				$this->redirectModel($tmp, 301);
				return;
			}

			if ($model = Categorypage::model()->findbyAttributes(array('categorykey'=>$_GET['key'])))
			{
				$this->forward('/page/categorypage/get', array('key'=>$_GET['key']));
				return;
			}

			if (Yii::app()->params['enableTags'] && Page::model()->taggedWith($_GET['key'])->count())
			{
				$this->forward('/page/page/tag', array('key'=>$_GET['key']));
				return;
			}

			if (!Yii::app()->user->isGuest)
				$this->bottomAdmin = CHtml::link('Seite anlegen', array('page/create', 'key'=>$_GET['key']));
			$this->render404();
			return;
		}
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/../assets');
		if ($model->key == 'mantrasingen')
		{
			Yii::app()->clientScript->registerCssFile($this->baseUrl.'css/mantra.css');
			Yii::app()->clientScript->registerScriptFile($assets.'/flowplayer/flowplayer-3.2.12.min.js');

		}
		// TODO make this more generic so you can turn it on or off
		if ($model->key == 'weihnachtsseminar' || $model->key == 'gutscheinverkauf_weihnachten')
		{
			Yii::app()->clientScript->registerScriptFile($assets.'/jquery.snow.min.js');
			Yii::app()->clientScript->registerScript(__CLASS__.'weihnachtsseminar','$.fn.snow({ maxSize: 50, newOn: 1000 });');
			Yii::app()->clientScript->registerCss(__CLASS__.'weihnachtsseminar','#flake {color:#09f}');
			Yii::app()->clientScript->registerCoreScript('jquery');
		}

		if ($model->active) {
			if (ltrim($model->meta_title) != '')
				$this->pageTitle = $model->meta_title .' im '. $this->pageTitle;
			if (ltrim($model->meta_description) != '')
				Yii::app()->clientScript->registerMetaTag($model->meta_description, 'description');
			if (ltrim($model->meta_keyword) != '')
				Yii::app()->clientScript->registerMetaTag($model->meta_keyword, 'keywords');
		}

		if ($viewFile=$this->getViewFile($model->key)!==false)
		{
			$this->render($model->key);
		}
		else
		{
			$this->generateCanonicalUrl($model);
			$view = $model->active?'get':'get_inactive';
			$this->render($view, array(
				'model'=>$model,
			));
		}
	}

	protected function generateCanonicalUrl(Page $model)
	{
		$url = $model->getUrl();
		$urlOne = $url[0];
		unset($url[0]);
		if (isset($_GET['Comment_page']))
			$url['Comment_page'] = $_GET['Comment_page'];
		if (isset($_GET['ajax']))
			$url['ajax'] = $_GET['ajax'];
		$this->canonicalUrl = $this->createAbsoluteUrl($urlOne, $url);
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='page-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionSearch()
	{
		$models = array();
		if (isset($_POST['kw']) && strlen($_POST['kw']) > 2)
		{
			$keywords = explode(' ', $_POST['kw']);
			$search = '';
			foreach ($keywords as $keyword)
			{
				if (empty($keyword) or strlen($keyword) < 3)
					continue;
				if (substr($keyword, 0, 1) != '+' && substr($keyword, 0, 1) != '-')
					$keyword = '+'.$keyword;
				$keyword.='*';
				$search.=' '.substr_replace($keyword, '(', 1, 0);

				$replace = array(
					'ä'=>array('ae', 'auml'),
					'ö'=>array('oe', 'ouml'),
					'ü'=>array('ue', 'uuml'),
					'ß'=>array('ss', 'sz', 'szlig'),
					);
				foreach (array('ä','ö','ü','ß') as $umlaut)
				{
					if (strpos($keyword, $umlaut) !== false)
					{
						foreach ($replace[$umlaut] as $to)
							$search.=' '.str_replace($umlaut, $to, substr($keyword, 1));
					}
				}
				$search .= ')';
			}

			$criteria = new CDbCriteria();
			//$criteria->select = "*, MATCH (`key`, `meta_title`, `text`) AGAINST (:search IN BOOLEAN MODE) AS score";
			$criteria->condition = 'MATCH (`key`, `meta_title`, `text`) AGAINST (:search IN BOOLEAN MODE) AND active=1';
			$criteria->params = array(':search'=>$search);
			$models = Page::model()->findAll($criteria);
		}

		$this->renderPartial('searchresults', array('models'=>$models));
	}

	public function actionTag($key)
	{
		$models = Page::model()->taggedWith($key)->findAll();
		$this->render('searchresults', array('models'=>$models));
	}

	public function actionSaveAll()
	{
		foreach (Page::model()->findAllByAttributes(array('active'=>true)) as $model)
			$model->save();
	}
}
