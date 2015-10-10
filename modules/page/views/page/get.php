<?php

echo  $model->getReplacedTemplate(array('controller'=>$this));





$dontDisplay = array('startseite', 'meditation', 'massage');
if (!in_array($model->key, $dontDisplay))
{
	echo $this->renderPartial('display_additional', array('model'=>$model));
}

if (!Yii::app()->user->isGuest)
{
	$this->bottomAdmin = CHtml::link('Seite bearbeiten', array('page/update', 'id'=>$model->id));
	if ($model->key == 'neuigkeiten')
	{
		$this->bottomAdmin .= ", ";
		$this->bottomAdmin .= "<small>".CHtml::link('Alle Seiten Speichern', array('page/saveAll'))."</small>";
	}
}
