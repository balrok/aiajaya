<h2>Termine</h2>
<?php
if ($logged_in){
	if ($needsGCalendarSync)
	{
		echo '<div class="important">';
		echo CHtml::link('Google Kalender Termine müssen aktualisiert werden!', array('/event/calendar/synchronizeEvents')).'<br/>';
		echo '</div>';

	}

	echo CHtml::link('termine für Email exportieren', array('export')).'<br/>';

	if (Yii::app()->params['calendarEnabled']) {
		echo CHtml::link('Kalender Anzeigen', array('/event/calendar/index')).'<br/>';
	}



}


echo $html;


if ($model)
{
	echo $this->renderPartial('_form', array('model'=>$model));
}

echo $this->actionAjaxListing();

if (!Yii::app()->user->isGuest)
{
	$this->bottomAdmin = "<small>".CHtml::link('Alle Termine Speichern', array('termin/saveAll'))."</small>";
}
