<?php

class GuestbookController extends Controller
{
	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	public function init()
	{
		ShortWidgets::addJsShorten(".ext-comment p", 150);
		parent::init();
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		if (!Yii::app()->getModule('page')->guestbook)
			throw new CHttpException(404,'Die angeforderte Seite existiert nicht');
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('list', 'feed'),
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionList()
	{
		// register rss feed
		Yii::app()->clientScript->registerLinkTag('alternate', 'application/rss+xml', $this->createAbsoluteUrl('/page/guestbook/feed'),
			null, array('title' => 'Gästebuch von '. Yii::app()->name));

		$this->pageTitle = 'Gästebuch '. $this->pageTitle;
		if (isset($_GET['comment']))
		{
			$models = Page::model()->findAllByAttributes(array('key'=>explode(",",$_GET['comment'])));
			$allIds = array();
			foreach ($models as $model)
				$allIds[] = $model->id;
			$criteria = new CDbCriteria(array(
				'condition' => 'pageId IN (:pageId)',
				'params' => array(
					':pageId' => implode(',', $allIds),
				),
				'order' => 'createDate DESC',
			));
			if (count($models) == 1)
				$this->pageTitle = $models[0]->meta_title .' '. $this->pageTitle;
		}
		else
		{
			$models = Page::model()->findAll();
			$criteria = new CDbCriteria(array(
				'order' => 'createDate DESC',
			));
		}

		$dataProvider = new CActiveDataProvider('Comment', array('pagination' => array('pageSize' => 30,),
  			'criteria' => $criteria,));

		$this->render('commentList',
			array(
				'models'=>$models,
				'comments'=>$dataProvider,
			)
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionFilter()
	{
		if (!isset($_POST['filter']))
			$_POST['filter'] = 'startseite'; // might be nicer than a 404
		$model = Page::model()->findByPk($_POST['filter']);
		if (!$model)
			throw new CHttpException(404,'Die angeforderte Seite existiert nicht');
		$criteria = new CDbCriteria(array(
			'condition' => 'pageId IN (:pageId)',
			'params' => array(
				':pageId' => $model->id,
			),
			'order' => 'createDate DESC',
		));
		$dataProvider = new CActiveDataProvider('Comment', array('pagination' => array('pageSize' => 30,),
  			'criteria' => $criteria,));

		$this->renderPartial('commentList', array(
			'models'=>array($model),
			'comments'=>$dataProvider,
		));
	}

	public function actionFeed()
	{
		Yii::import('aiajaya.extensions.efeed.*');
		// RSS 2.0 is the default type
		$feed = new EFeed();

		$feed->title= 'Gästebuch von ' . Yii::app()->name;
		$feed->description = 'Das Gästebuch vom '.Yii::app()->name;

		$feed->addChannelTag('language', 'de-de');
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', $this->createAbsoluteUrl('/page/guestbook/list'));

		// * self reference
		$feed->addChannelTag('atom:link', $this->createAbsoluteUrl('/page/guestbook/feed'));


		$criteria=new CDbCriteria(array(
			'order'=>'`createDate` DESC',
		));

		$dataProvider=new CActiveDataProvider('Comment', array(
			'pagination'=>array('pageSize'=>0,),
			'criteria'=>$criteria,
		));


		foreach ($dataProvider->getData() as $comment)
		{
			$item = $feed->createNewItem();

			$item->title = $comment->getBaseModel()->commentName;

			$item->link = $this->createAbsoluteUrl('/page/page/get', array('key'=>$comment->getBaseModel()->key));
			$item->date = $comment->createDate;
			$item->description = $comment->message;
			$item->addTag('author', $comment->name);
			//$item->addTag('guid', 'http://www.ramirezcobos.com/',array('isPermaLink'=>'true'));

			$feed->addItem($item);
		}

		$feed->generateFeed();
		Yii::app()->end();
	}

	public function actionJsonGet($key)
	{
		$models = Page::model()->findAllByAttributes(array('key'=>explode(",",$key)));
		if ($models)
		{
			$return = array();
			foreach (end($models)->getComments($models) as $comment)
			{
				$page = $comment->baseModel;
				$category = $page->getCategory();
				$url = $page->getUrl();
				$partOne = $url[0];
				unset($url[0]);
				$return[] = array(
					'name' => $comment->name,
					'date' => date('d.m.Y', strtotime($comment->createDate)),
					'message' => $comment->message,
					'pageLink' => $this->createAbsoluteUrl($partOne, $url),
					'pageName' => $page->commentName,
					'categoryImg' => $this->createAbsoluteUrl('/') . $category->getPageImg(),
				);
			}
			$return = array(true, $return);
		}
		else
		{
			$return = array(false, "Konnte die Seite '".$key."' nicht finden.");
		}
		$this->renderJsonp($return);
	}


	protected function isSpam($comment)
	{
		$error = array();
		if (!empty($_POST['website']) || !empty($_POST['website2']) || !empty($_POST['website3']) || !empty($_POST['website5']) || !empty($_POST['website6']))
			$error[] = 'Du hast Felder ausgefüllt, die eigentlich nicht sichtbar sind.. Bitte gib nur deinen Namen+Text ein';
		if (empty($_POST['website4']) || $_POST['website4'] > time())
		{
			$error[] = 'Bitte lass dir mehr Zeit zum Schreiben der Kommentare (mindestens 5 Sekunden)';
		}
		if (strpos($comment->message, 'a href=')!==false || strpos($comment->message, '[url=') !== false)
			$error[] = 'Bitte benutze nicht "a href=" bzw "[url=" im Text (funktioniert eh nicht)';
		if (substr_count($comment->message, 'http') > 0)
			$error[] = 'Bitte benutze das Wort "http" nicht';
		if ($error)
			return '<div class="form"><div class="errorSummary">'.implode('<br/>', $error).'</div></div>';
		return false;
	}

	/**
	 * Creates a new comment.
	 *
	 * On Ajax request:
	 *   on successfull creation comment/_view is rendered
	 *   on error comment/_form is rendered
	 * On POST request:
	 *   If creation is successful, the browser will be redirected to the
	 *   url specified by POST value 'returnUrl'.
	 */
	public function actionCreate()
	{
		$comment = new Comment();

		$cClass=get_class($comment);
		if(isset($_POST[$cClass]))
		{
			$comment->attributes = $_POST[$cClass];

			$output = '';
			$commentOutput = '';
			$spam = $this->isSpam($comment);
			if(!$spam && $this->saveComment($comment))
			{
				Yii::app()->user->setFlash('success', 'Erfolgreich Kommentar im Gästebuch eingetragen.');

				$mail = new Email('admin');
				$mail
					->AddAddress('carl.schoenbach@gmail.com')
					->setSubject('Gaestebuch Kommentar')
					->setMsg(
						$this->renderPartial('email',array(
							'comment'=>$comment,
							'page'=>$comment->getBaseModel(),
						), true)
					)
					// 'Jemand hat im Gästebuch ein Kommentar geschrieben mit folgenden abgesendeten Daten: '.$postData."<br/>\n<br/>\n")
					->setFrom('gaestebuch@balance-dresden.info', 'Gästebuch '.Yii::app()->name);
				$mail->send();
			}
			else
			{
				if ($spam)
				{
					Yii::app()->user->setFlash('error', $spam);
				}
				else
				{
					Yii::app()->user->setFlash('error', 'Problem beim Speichern');
					Yii::app()->user->setFlash('error', CHtml::errorSummary($comment));
				}
			}
		}
		$this->redirect($_POST['returnUrl']);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (Yii::app()->user->isGuest)
			return;
		$comment=$this->loadModel($id);
		$cClass=get_class($comment);

		if(isset($_POST[$cClass]))
		{
			$comment->attributes = $_POST[$cClass];

			if($this->saveComment($comment))
			{
				if(Yii::app()->request->isAjaxRequest) {
					// refresh model to replace CDbExpression for timestamp attribute
					$comment->refresh();

					// render updated comment
					$this->renderPartial('_view',array(
						'data'=>$comment,
					));
					Yii::app()->end();
				} else {
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view','id'=>$comment->id));
				}
			}
		}

		if(Yii::app()->request->isAjaxRequest)
		{
			$output = $this->renderPartial('_form',array(
				'comment'=>$comment,
				'ajaxId'=>time(),
			), true);
			// render javascript functions
			Yii::app()->clientScript->renderBodyEnd($output);
			echo $output;
			Yii::app()->end();
		}
		else
		{
			$this->render('update',array(
				'model'=>$comment,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
		if(Yii::app()->request->isPostRequest)
		{
			$comment = $this->loadModel($id);
			if (!Yii::app()->user->isGuest)
			{
				$comment->delete();

				// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
				if (!Yii::app()->request->isAjaxRequest) {
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
				}
			}
			else {
				throw new CHttpException(403,'Only comment owner can delete his comment.');
			}
		}
		else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * @return Comment
	 */
	public function loadModel($id)
	{
		$model = Comment::model()->findByPk((int) $id);
		if ($model === null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	protected function saveComment($comment)
	{
		if (!Yii::app()->user->isGuest && !empty($_POST['createDate']))
		{
			if ($date = strptime($_POST['createDate'], '%d.%m.%y'))
			{
				$date = mktime(12,0,0,$date['tm_mon']+1, $date['tm_mday'], $date['tm_year']+1900);
				$comment->createDate = date('Y-m-d H:i:s',$date);
			}
		}
		return $comment->save();
	}
}
