<?php $namePrefix = 'Categorypage['.((int)$model->id).']'; ?>
<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'pagekey'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[pagekey]',
			'source'=>$allpages,
			'value'=>$model->pagekey,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#pagekey'.$model->id.'").val(ui.item.id);$("#pagename'.$model->id.'").val(ui.item.label);return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'pagekey'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'pagekey'); ?>
	</div>
	<div>
		<?php echo $form->labelEx($model,'pagename'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[pagename]',
			'source'=>$allpages,
			'value'=>$model->pagename,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#pagekey'.$model->id.'").val(ui.item.id);$("#pagename'.$model->id.'").val(ui.item.label);return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'pagename'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'pagename'); ?>
	</div>
</div>

<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'pageimg'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[pageimg]',
			'source'=>$allimages,
			'value'=>$model->pageimg,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#pageimg'.$model->id.'").val(ui.item.id);return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'pageimg'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'pageimg'); ?>
	</div>
	<div>
		<?php echo $form->labelEx($model,'sort'); ?>
		<?php echo $form->textField($model,'sort',array('size'=>20,'maxlength'=>20,'name'=>$namePrefix.'[sort]')); ?>
		<?php echo $form->error($model,'sort'); ?>
	</div>
</div>

<?php if ($model->id) { ?>
<div class="row">
	<?php echo $form->labelEx($model,'delete'); ?>
	<?php echo $form->checkbox($model, 'delete', array('name'=>$namePrefix.'[delete]'));?>
	<?php echo $form->error($model,'delete'); ?>
</div>
<?php } ?>

<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Erstellen' : 'Speichern'); ?>
</div>
