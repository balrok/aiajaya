<div class="form">
<?php
	$titleAutoCompleteJs = <<<EOC
	 function(event,ui){
		 $("#Termin_titel").val(ui.item.titel);
		 $("#Termin_untertitel").val(ui.item.untertitel);
		 $("#Termin_zeit").val(ui.item.zeit);
		 $("#Termin_url").val(ui.item.url);
		 $("#Termin_rubric").val(ui.item.rubric);
		 return false;
	 }
EOC;
	$urlAutoCompleteJs = <<<EOC
	 function(event,ui){
		 $("#Termin_url").val(ui.item.key);
		 return false;
	 }
EOC;
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'categorypage-form',
    'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->hiddenField($model, 'id')?>

<?php
$fields = array('titel', 'untertitel', 'zeit', 'datum', 'url');
if (Yii::app()->params['rubrikField']) {
	$fields[] = 'rubric';
}
foreach ($fields as $type)
{
	echo '<div class="row">';
	echo '<label for="Termin_'.$type.'">'.$model->getAttributeLabel($type).'</label>';
	if ($type=='titel')
	{
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'Termin['.$type.']',
			'source'=>$this->getTitleAutocompleteData(),
			'value'=>$model->$type,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'select'=>'js: '.$titleAutoCompleteJs,
			),
			'htmlOptions'=>array(
				'class' => 'form-control',
			),
		));
	}
	elseif ($type=='url')
	{
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'Termin['.$type.']',
			'source'=>$this->getUrlAutocompleteData(),
			'value'=>$model->$type,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'select'=>'js: '.$urlAutoCompleteJs,
			),
			'htmlOptions'=>array(
				'class' => 'form-control',
			),
		));
	}
	else
		echo $form->textField($model, $type, array('class'=>'form-control'));
	echo '</div>';
}
?>
<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Erstellen' : 'Speichern', array('class'=>'btn btn-primary')); ?>
</div>
<?php $this->endWidget(); ?>
</div>
