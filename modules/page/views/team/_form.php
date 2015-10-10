<?php $namePrefix = 'Team['.((int)$model->id).']'; ?>
<div class="row">
	<div>
		<?php echo $form->labelEx($model,'key'); ?>
		<?php echo $form->error($model,'key'); ?>
		<?php echo $form->textField($model,'key',array('size'=>25,'maxlength'=>255,'name'=>$namePrefix.'[key]')); ?>
	</div>
</div>

<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'image'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[image]',
			'source'=>$allimages,
			'value'=>$model->image,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#image'.$model->id.'").val(ui.item.id);return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'image'.$model->id,
				'size'=>25,
			),
		));
		?>
		<?php echo $form->error($model,'image'); ?>
	</div>
	<div>
		<?php echo $form->labelEx($model,'subheader'); ?>
		<?php echo $form->error($model,'subheader'); ?>
		<?php echo $form->textField($model,'subheader',array('size'=>25,'maxlength'=>255,'name'=>$namePrefix.'[subheader]')); ?>
	</div>
</div>

<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'sort'); ?>
		<?php echo $form->textField($model,'sort',array('size'=>25,'maxlength'=>20,'name'=>$namePrefix.'[sort]')); ?>
		<?php echo $form->error($model,'sort'); ?>
	</div>
	<?php if ($model->id) { ?>
	<div>
		<?php echo $form->labelEx($model,'delete'); ?>
		<?php echo $form->checkbox($model, 'delete', array('name'=>$namePrefix.'[delete]'));?>
		<?php echo $form->error($model,'delete'); ?>
	</div>
	<?php } ?>
</div>

<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php ShortWidgets::ckEditor($model, 'text', array(), array('name'=>$namePrefix.'[text]')) ?>
		<?php echo $form->error($model,'text'); ?>
	</div>
</div>

<?php foreach ($model->infoFields as $k=>$v) { ?>
	<div class="row">
		<div style="float:left;padding-right:40px;">
			<?php echo $form->labelEx($model,$k); ?>
			<?php echo $form->textField($model,$k,array('size'=>25,'maxlength'=>255,'name'=>$namePrefix.'['.$k.']')); ?>
			<?php echo $form->error($model,$k); ?>
		</div>
	</div>
<?php } ?>

<div class="row buttons">
	<div style="float:left;padding-right:40px;">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Erstellen' : 'Speichern'); ?>
	</div>
</div>

<div style="clear:left"> </div>
