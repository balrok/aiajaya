<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to 'aiajaya.views.layouts.column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	#public $layout='aiajaya.views.layouts.leftbar';
	public $canonicalUrl 	= false;

	public $pageMenu = array();
	public $terminMenu = array();

	public $prePageBreakHtml = '';
	public $postPageBreakHtml = '';

	public $bottomAdmin = ''; // html which can be set to the bottom of the page for adminpurpose

	public $baseUrl = '';
	public $imageUrl= '';

	public function void() {
	}

    private function _counter()
    {
		$counter = new Counter;
		$counter->count();
    }

	public function init()
	{
		// for google: redirecting
		$host = $_SERVER['HTTP_HOST'];

		$this->pageTitle = CHtml::encode(Yii::app()->name);
		$this->_counter();

		if (!defined('YII_DEBUG') && Yii::app()->params['googleAnalytics'])
			Yii::app()->getClientScript()->registerScriptFile('http://www.google-analytics.com/ga.js');

		$this->baseUrl = Yii::app()->theme->baseUrl.'/';
		$this->imageUrl = Yii::app()->baseUrl.'/bilder/';

		// style
		foreach (Yii::app()->params['cssFiles'] as $f)
		{
			$file = $f[0];
			if (isset($f[1]))
			{
				if ($f[1] == '/')
					Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.$file);
				if ($f[1] == 'themes')
					Yii::app()->clientScript->registerCssFile($this->baseUrl.$file);
			}
			else
				Yii::app()->clientScript->registerCssFile($this->baseUrl.$file);
		}

		parent::init();
	}

	protected function beforeRender($view)
	{
		static $calledBeforeRender = false;
		if (!$calledBeforeRender)
		{
			$calledBeforeRender = true;
			// I want this code here because ajax requests shouldn't get this header and meta tags
			// so beforerender might be the best place
			header('Content-type: text/html; charset=utf-8');
			Yii::app()->clientScript->registerMetaTag(Yii::app()->language, 'language');

			if (!$this->canonicalUrl)
			{
				if ($this->route == 'page/categorypage/multi' && isset($_GET['multi']))
				{
					$_GET['multi'] = str_replace(',', ' ', $_GET['multi']);
				}
				$this->canonicalUrl = $this->createAbsoluteUrl('/' . $this->route, $_GET);
			}
			$this->canonicalUrl = false;
		}
		return parent::beforeRender($view);
	}

	public function getPerformanceData()
	{
		list($count, $time) = Yii::app()->db->getStats();
		return "Execution Time: " . sprintf("%dms", Yii::getLogger()->getExecutionTime()*1000)."\n".
			"Memory Usage: " . sprintf("%.2f", Yii::getLogger()->getMemoryUsage()/1024)."kB\n".
			"DB Query count: " . sprintf("%d", $count)."\n".
			"DB Query time: " . sprintf("%dms", $time*1000);
	}

	public function setPageBreak()
	{
		$pageBreak = $this->prePageBreakHtml.'
					<br/>
					<br/>
			</div>
		</div>
		<div class="stopper"> </div>
		<div class="mainbox" style="padding-top:10px;">
			<div class="main">
		'.$this->postPageBreakHtml;
		return $pageBreak;
	}



	// cause deletion mostly looks the same a helper for that:
	// ajaxrequests will only redirect in case of errors
	// obj is an array which's first element is a constant and it's
	// second argument a list of attributes from that model
	// it will then write this in the messages
	// DEPRECATED: $id should always be the integer.. accessing get is not that
	// good - sometimes _POST or other things wants to be used
	public function deleteHelper($class, $id, $redirect, $obj_=array())
	{
		if (!is_numeric($id) && !isset($_GET[$id]))
			Yii::app()->user->setFlash('error',"Fehlender Parameter: ".$id.'<br/>'.Yii::app()->user->getFlash('error'));
		else
		{
			$id = (is_numeric($id))?$id:$_GET[$id];
			if (strpos($id, ';') !== false)
			{
				$ids = explode(';', $id);
				foreach ($ids as $id)
					if ((int)$id != 0)
						$this->deleteHelper($class, (int)$id, '', $obj_);
				if (Yii::app()->request->isAjaxRequest)
				{
					if(Yii::app()->user->hasFlash('error'))
						$this->redirect($redirect);
				}
				else if (!empty($redirect))
					$this->redirect($redirect);
				return;
			}
			if (is_object($class))
				$model = $class->findbyPk($id);
			else
				$model = CActiveRecord::model($class)->findbyPk($id);

			if (empty($obj_))
				$obj = 'Objekt';
			else
				$obj = $obj_[0];

			if (!$model)
				Yii::app()->user->setFlash('error',$obj." nicht gefunden!".'<br/>'.Yii::app()->user->getFlash('error'));
			else
			{
				$model->scenario = 'delete';
				if (!empty($obj_))
				{
					if (isset($obj_[1]))
					{
						$delimiter = (isset($obj_[2]))?$obj_[2]:' ';
						$vals = array();
						foreach ($obj_[1] as $v)
							$vals[] = $delimiter.$model->$v;
						$obj.=' <b>'.implode($delimiter, $vals).'</b>';
					}
				}

				if (!$model->hasAccess())
					Yii::app()->user->setFlash('error',$obj." darf von Ihnen nicht gelöscht werden!".'<br/>'.Yii::app()->user->getFlash('error'));
				else
				{
					$model->delete();
					if (!$model->hasErrors())
					{
						if(!Yii::app()->request->isAjaxRequest)
							Yii::app()->user->setFlash('success',$obj." wurde erfolgreich gelöscht!".'<br/>'.Yii::app()->user->getFlash('success'));
					}
					else // i guess this won't ever be called
						Yii::app()->user->setFlash('error',"Fehler beim Löschen von ".$obj."!".'<br/>'.Yii::app()->user->getFlash('error'));
				}
			}
		}
		if (Yii::app()->request->isAjaxRequest)
		{
			if(Yii::app()->user->hasFlash('error'))
			{
				if (!empty($redirect))
					$this->redirect($redirect);
			}
		}
		else if (!empty($redirect))
			$this->redirect($redirect);
	}

	// will render an array to json
	public function renderJson($arr)
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: text/javascript');
		echo CJSON::encode($arr);
		die();
		Yii::app()->end();
	}

	// will render an array to jsonp or fall back to json
	public function renderJsonp($arr)
	{
		if (!isset($_GET['callback']))
		{
			$this->renderJson($arr);
		}
		else
		{
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: text/javascript');
			echo $_GET['callback'].'(';
			echo CJSON::encode($arr);
			echo ')';
			die();
			Yii::app()->end();
		}
	}

	public function render404()
	{
		$referer =  (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'';
		$agent = (isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:'';
		Yii::log(sprintf('404 for "%s" referer "%s" and agent "%s"', $_SERVER['REQUEST_URI'], $referer, $agent));

		$this->render('aiajaya.views.site.404');
	}

	// there is a mistake on the live server where those params are inside $_GET
	// it causes problems with guestbook pagination and creates ugly links
    public function createUrl($route, $params=array(), $ampersand = '&')
    {   
        unset($params['a']);
        unset($params['url']);
        return parent::createUrl($route, $params);
    }

	/**
		@ deprecated
	*/
	public function endFirstSection()
	{
		// TODO deprecated
	}
}
