<?php $namePrefix = 'Menu['.((int)$model->id).']'; ?>
<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'key'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[key]',
			'source'=>$allpages,
			'value'=>$model->page_id,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#key'.$model->id.'").val(ui.item.id);$("#name'.$model->id.'").val(ui.item.label);
				$("#page_id'.$model->id.'").val(ui.item.id);
				return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'page_id'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'key'); ?>
	</div>
	<input type="hidden" name="<?=$namePrefix?>[page_id]" id="page_id<?=$model->id?>" value="<?=$model->page_id?>" />

	<div>
		<?php echo $form->labelEx($model,'name'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[name]',
			'source'=>$allpages,
			'value'=>$model->name,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#key'.$model->id.'").val(ui.item.id);$("#name'.$model->id.'").val(ui.item.label);return false;
				$("#page_id'.$model->id.'").val(ui.item.id);
				}
				',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'name'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'name'); ?>
	</div>
</div>

<div class="row">
	<div style="float:left;padding-right:40px;">
		<?php echo $form->labelEx($model,'img'); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>$namePrefix.'[img]',
			'source'=>$allimages,
			'value'=>$model->img,
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'0',
				'html'=>true,
				'select'=>'js: function(event,ui){$("#img'.$model->id.'").val(ui.item.id);return false;}',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;',
				'id'=>'img'.$model->id,
			),
		));
		?>
		<?php echo $form->error($model,'img'); ?>
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
