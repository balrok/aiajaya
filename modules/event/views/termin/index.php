<h2>Termine</h2>
<?php
if ($logged_in)
{
	if (Yii::app()->getModule('event')->calendarEnabled) {
		//echo CHtml::link('Kalender Anzeigen', array('/event/calendar/index')).'<br/>';
		if ($needsGCalendarSync)
		{
			echo '<div class="important">';
			echo CHtml::link('Google Kalender Termine müssen aktualisiert werden!', array('/event/calendar/synchronizeEvents')).'<br/>';
			echo '</div>';

		}
	}

	if (Yii::app()->getModule('event')->emailExportEnabled) {
		echo CHtml::link('termine für Email exportieren', array('export')).'<br/>';
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
