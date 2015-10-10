<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableAjaxValidation'=>true,
)); ?>

	<div class="row">
		<?= $form->labelEx($model,'username'); ?>
		<?= $form->textField($model,'username', array('class'=>'form-control')); ?>
		<?= $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'password'); ?>
		<?= $form->passwordField($model,'password', array('class'=>'form-control')); ?>
		<?= $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?= $form->checkBox($model,'rememberMe', array('style'=>'float:left;margin-right:10px;')); ?>
		<?= $form->label($model,'rememberMe'); ?>
		<?= $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Login'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
