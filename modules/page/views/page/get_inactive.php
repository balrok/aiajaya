<div class="warning">
	Diese Veranstaltung/Seite ist leider nicht mehr gültig
	<?php // TODO maybe show alternative things, or log the requests so I can fix some ?>
</div>
<?php
if (!Yii::app()->user->isGuest)
{
	$this->bottomAdmin = CHtml::link('Seite bearbeiten', array('page/update', 'id'=>$model->id));
}
?>
