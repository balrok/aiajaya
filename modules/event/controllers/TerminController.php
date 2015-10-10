<?php
class TerminController extends Controller
{
	private $_model;

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index', 'view', 'calendar', 'ajaxListing', 'feed'),
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

	public function actionView()
	{
		$model = $this->loadModel();

		$this->render('view',array(
			'model'=>$model,
		));
	}

	public function actionCopy($id)
	{
		$this->redirect(array('index', 'copyId'=>$id));
	}

	public function actionEdit($id)
	{
		$this->redirect(array('index', 'editId'=>$id));
	}

	public function actionDelete()
	{
		$model = Termin::model()->findbyPk($_GET['id']);

     	$this->deleteHelper('Termin', 'id',
     		array('/event/termin/index'),
     		array(Yii::t('catalog', 'Termin'), array('titel')));
	}

	public function actionExport()
	{
		$curMonth = date('n');
		$curYear  = date('Y');
		if ($curMonth == 11)
			$firstDay2NextMonth = mktime(0, 0, 0, 0, 0, $curYear+1);
		else if ($curMonth == 11)
			$firstDay2NextMonth = mktime(0, 0, 0, 0, 1, $curYear+1);
		else
			$firstDay2NextMonth = mktime(0, 0, 0, $curMonth+2, 1);

		$criteria=new CDbCriteria(array(
			'condition'=>'`to`>'.(time()-60*30), // .' AND `from` < ' . $firstDay2NextMonth,
			'order'=>'`from` ASC',
		));

		$dataProviderAll=new CActiveDataProvider('Termin', array(
			'pagination'=>array('pageSize'=>9999,),
			'criteria'=>$criteria,
		));
		$all_events = array();
		foreach($dataProviderAll->getData() as $m){
			$all_events[$m->id] = date('d.m.y',$m->from).' '.$m->titel;
		}

		// now only get selected
		if (isset($_POST['inc_events']))
		{
			$inc_events = array();
			foreach ($_POST['inc_events'] as $v) {
				$inc_events[] = (int)$v;
			}
			$criteria=new CDbCriteria(array(
				'condition'=>'`to`>'.(time()-60*30) .' AND `id` IN ('.implode(',', $inc_events).')',
				'order'=>'`from` ASC',
			));
		}
		else
		{
			$criteria=new CDbCriteria(array(
				'condition'=>'`to`>'.(time()-60*30) .' AND `from` < ' . $firstDay2NextMonth,
				'order'=>'`from` ASC',
			));
		}
		$dataProvider=new CActiveDataProvider('Termin', array(
			'pagination'=>array('pageSize'=>9999,),
			'criteria'=>$criteria,
		));
		$inc_events = array();
		foreach($dataProvider->getData() as $m) {
			$inc_events[] = $m->id;
		}
		$textModel = new TextModel();
		if (isset($_POST['TextModel']) && isset($_POST['TextModel']['text']))
			$textModel->text = $_POST['TextModel']['text'];


		$subject = 'Newsletter vom Balance Zentrum - '.strftime("%B", time());
		$from = array('newsletter@balance-dresden.info', 'Newsletter Balance Zentrum');
		$html = $this->renderPartial('export_mail', array(
						'dataProvider'=>$dataProvider,
						'textModel'=>$textModel,
					), true, false);
		$txt = $this->renderPartial('export_mail_txt', array(
						'dataProvider'=>$dataProvider,
						'textModel'=>$textModel,
					), true, false);
		if(isset($_POST['dowhat']) && $_POST['dowhat'] == 1)
		{
            $mail = new Email('customer');
            $mail
				->AddAddress('balrok.1787569@gmail.com')
                ->setSubject($subject)
                ->setMsg($html)
				->setTxtMsg($txt)
				->setFrom($from[0], $from[1])
                ->send();

			Yii::app()->user->setFlash('success', 'Erfolgreich an '.$email.' gesendet.');
			$this->redirect(array('export'));
		}
		if(isset($_POST['dowhat']) && ($_POST['dowhat'] == 2 || $_POST['dowhat'] == 3))
		{
			$mail = new Email('customer');
			$mail->setSubject($subject);
			$mail->setMsg($html);
			$html = $mail->getMsgHtml();
			$from[0] = 'ddkatarina@hotmail.com';
			$campaign = Yii::app()->mailchimp->createCampaign($subject, $from[0],$from[1], 'Balance Newsletter', $html, $txt);
			if ($campaign['status'] != "save")
			{
				diedump($campaign);
			}
			$id = $campaign['id'];
			if ($_POST['dowhat'] == 2)
			{
				$result = Yii::app()->mailchimp->sendtestCampaign($id, array('balrok.1787569@gmail.com', 'ddkatarina@hotmail.com'));
				dump($result);
			}
			if ($_POST['dowhat'] == 3)
			{
				$result = Yii::app()->mailchimp->sendCampaign($id);
				dump($result);
			}
		}
		$this->render('export', array(
			'dataProvider'=>$dataProvider,
			'all_events'=>$all_events,
			'inc_events'=>$inc_events,
			'textModel'=>$textModel,
		));
	}

	protected function getUrlAutocompleteData()
	{
		$criteria=new CDbCriteria(array(
			'order'=>'`meta_title` ASC',
		));
		$criteria->addInCondition('active', array(1));
		$dataProvider=new CActiveDataProvider('Page', array(
			'pagination'=>array('pageSize'=>9999,),
			'criteria'=>$criteria,
		));
		$autocompleteData = array();
		foreach ($dataProvider->getData() as $model)
		{
			$autocompleteData[] = array(
				'key'=>$model->key,
				'label'=>$model->meta_title.' ('.$model->key.')',
			);
		}
		return $autocompleteData;
	}

	protected function getTitleAutocompleteData()
	{
		$criteria=new CDbCriteria(array(
			'order'=>'`from` DESC',
		));
		$dataProvider=new CActiveDataProvider('Termin', array(
			'pagination'=>array('pageSize'=>9999,),
			'criteria'=>$criteria,
		));
		$autocompleteData = array();
		// to do distinct in sql it requires a sub-query (sorting) and a query group-by
		$distinct = array();
		foreach ($dataProvider->getData() as $termin)
		{
			if (in_array($termin->titel, $distinct))
				continue;
			$distinct[] = $termin->titel;
			$autocompleteData[$termin->titel] = array(
				'untertitel'=>$termin->untertitel,
				'titel'=>$termin->titel,
				'zeit'=>$termin->zeit,
				'label'=>$termin->titel,
				'rubric'=>$termin->rubric,
				'url'=>$termin->url,
			);
		}
		$new = array();
		foreach ($autocompleteData as $d)
			$new[] = $d;
		return $new;
	}

	protected function insertUpdateTermin($copyId)
	{
		$model = new Termin;
		if ($copyId)
		{
			$copy = Termin::model()->findbyPk($copyId);
			if ($copy)
			{
				$data = $copy->attributes;
				unset($data['id']);
				$model->attributes = $data;
			}
		}
		elseif (isset($_GET['editId']))
		{
			$model = Termin::model()->findbyPk($_GET['editId']);
		}
		if(isset($_POST['Termin']))
		{
			if (isset($_POST['Termin']['id']) && $_POST['Termin']['id'])
			{
				$updateModel = Termin::model()->findbyPk($_POST['Termin']['id']);
				if ($updateModel)
				{
					$updateModel->attributes = $_POST['Termin'];
					if ($updateModel->save()) {
						Yii::app()->user->setFlash('success', 'Erfolgreich aktualisiert');
					}
					else
						Yii::app()->user->setFlash('error', 'Fehler beim Aktualisieren');
				}
				else
					Yii::app()->user->setFlash('error', 'Konnte Termin nicht finden');
			}
			else
			{
				$model->attributes=$_POST['Termin'];
				if($model->save())
				{
					Yii::app()->user->setFlash('success', 'Erfolgreich gespeichert');
					$model = new Termin;
				}
				else
					Yii::app()->user->setFlash('error', 'Fehler beim Hinzufügen');
			}
		}
		return $model;
	}

	public function actionIndex($copyId = null)
	{
		// register rss feed
		Yii::app()->clientScript->registerLinkTag('alternate', 'application/rss+xml', $this->createAbsoluteUrl('/event/termin/feed'),
			null, array('title' => 'Termine vom Balance - Zentrum für Energie- und Körperarbeit') );

		$model = null;
		$needsGCalendarSync = false;

		$logged_in = !Yii::app()->user->isGuest && !isset($_GET['normal']);
		if ($logged_in)
		{
			$model = $this->insertUpdateTermin($copyId);
			// if at least one model needs a change display the link
			$termin = Termin::model()->findByAttributes(array('google_calendar_haschange'=>1));
			$needsGCalendarSync = (bool) $termin;
		}

		$html = $this->renderFirstBlock();

		$this->render('index',array(
			'model'=>$model,
			'html'=>$html,
			'logged_in'=>$logged_in,
			'needsGCalendarSync'=>$needsGCalendarSync,
		));
	}

	protected function renderFirstBlock()
	{
		$logged_in = !Yii::app()->user->isGuest && !isset($_GET['normal']);
		$text = EInfotext::model()->get(2);;
		$text->textarea = true;
		$date = EInfotext::model()->get(3);;
		$titel = EInfotext::model()->get(4);;
		$active = EInfotext::model()->get(5);;

		if($logged_in)
		{
			if (isset($_POST['stop']))
			{
				$active->info = $_POST['infoactive'];
				$_POST['infotext'] = $_POST['Page']['text'];
				$date->info = $_POST['infodate'];
				$titel->info = $_POST['infotitel'];
				$text->info = $_POST['infotext'];
				$active->save();
				$date->save();
				$titel->save();
				$text->save();
			}
		}

		return $this->renderPartial('index_first', array(
			'logged_in'=>$logged_in,
			'text'=>$text,
			'date'=>$date,
			'titel'=>$titel,
			'active'=>$active,
		), true);
	}

	public function actionAjaxListing()
	{
		$criteria=new CDbCriteria(array(
			'condition'=>'`to`>'.(time()-60*30) . ((Yii::app()->user->isGuest)?' AND `to`<'.(time()+60*60*24*60):''),
			'order'=>'`from` ASC',
		));

		$dataProvider=new CActiveDataProvider('Termin', array(
			'pagination'=>array('pageSize'=>99999,),
			'criteria'=>$criteria,
		));

		return $this->renderPartial('_eventlisting', array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=Termin::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	public function actionFeed()
	{
		Yii::import('aiajaya.extensions.efeed.*');
		// RSS 2.0 is the default type
		$feed = new EFeed();

		$feed->title= 'Termine vom Balance - Zentrum für Energie- und Körperarbeit';
		$feed->description = ''; // Alle Termine live im BlickDies ist ein Test die Termine als RSS 2.0 Feed anzubieten - Es wird keine Garantie auf Korrektheit und Aktualität gegeben.';

		//$feed->setImage('Testing RSS 2.0 EFeed class','http://www.ramirezcobos.com/rss',
		//'http://www.yiiframework.com/forum/uploads/profile/photo-7106.jpg');

		$feed->addChannelTag('language', 'de-de');
		$feed->addChannelTag('pubDate', date(DATE_RSS, time()));
		$feed->addChannelTag('link', $this->createAbsoluteUrl('/event/termin'));

		// * self reference
		$feed->addChannelTag('atom:link', $this->createAbsoluteUrl('/event/termin/feed'));


		$criteria=new CDbCriteria(array(
			'condition'=>'`to`>'.(time()-60*30),
			'order'=>'`from` ASC',
		));

		$dataProvider=new CActiveDataProvider('Termin', array(
			'pagination'=>array('pageSize'=>0,),
			'criteria'=>$criteria,
		));

		foreach ($dataProvider->getData() as $termin)
		{
			$item = $feed->createNewItem();

			$item->title = $termin->titel;

			$url = $termin->getUrl();
			if (is_array($url))
			{
				$part1 = $url[0];
				unset($url[0]);
				$part2 = $url;
				$item->link = $this->createAbsoluteUrl($part1, $part2);
			}
			else
			{
				$item->link = $url;
			}
			$item->date = $termin->from;

			$untertitel = str_replace('--', '<br/>', $termin->untertitel);
			$untertitel .= '<hr/>'.str_replace('--', '<br/>', $termin->datum).' '.str_replace('--', '<br/>', $termin->zeit);
			if ($termin->url)
				$untertitel .= '<hr/><a href="'.$termin->url.'">Weitere Informationen</a>';
			$item->description = $untertitel;

			//$item->addTag('author', 'thisisnot@myemail.com (Antonio Ramirez)');
			//$item->addTag('guid', 'http://www.ramirezcobos.com/',array('isPermaLink'=>'true'));

			$feed->addItem($item);
		}

		$feed->generateFeed();
		Yii::app()->end();

	}

	public function actionSaveAll()
	{
		$criteria=new CDbCriteria(array(
			'condition'=>'`to`>'.(time()-60*30),
			'order'=>'`from` ASC',
		));
		foreach (Termin::model()->findAll($criteria) as $m)
			$m->save();
	}
}
