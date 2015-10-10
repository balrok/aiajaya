<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'page-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'key'); ?>
		<?php echo $form->textField($model,'key',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'key'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta_keyword'); ?>
		<?php echo $form->textField($model,'meta_keyword',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'meta_keyword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta_description'); ?>
		<?php echo $form->textField($model,'meta_description',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'meta_description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta_title'); ?>
		<?php echo $form->textField($model,'meta_title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'meta_title'); ?>
	</div>

	<?php if (Yii::app()->params['enableGuestBook']) {?>
	<div class="row">
		<?php echo $form->labelEx($model,'commentable'); ?>
		<?php echo $form->checkbox($model,'commentable')?>
		<?php echo $form->error($model,'commentable'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'commentName'); ?>
		<?php echo $form->textField($model,'commentName',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'commentName'); ?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->checkbox($model,'active')?>
		<?php echo $form->error($model,'active'); ?>
	</div>
	<?php if (Yii::app()->params['enableTags']) { ?>
	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php $this->widget('CAutoComplete', array(
			'name' => 'tags',
			'value' => $model->tags->toString(),
			'url'=>array('/page/page/autotags'), //path to autocomplete URL
			'multiple'=>true,
			'mustMatch'=>false,
			'matchCase'=>false,
		)); ?>
	</div>
	<?php } ?>
	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<p>
			Ein normaler Zeilenumbruch geht mit Shift+Enter<br/>
			<?php ShortWidgets::ckEditor($model, 'text') ?>
		</p>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
