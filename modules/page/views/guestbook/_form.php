<div id="ext-comment-form-<?php echo $comment->isNewRecord ? 'new' : 'edit-'.$comment->id; ?>" class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id'=>'ext-comment-form',
    'action'=>array('/page/guestbook/create'),
	'enableAjaxValidation'=>false
)); ?>

	<?php /** @var CActiveForm $form */
	echo $form->errorSummary($comment); ?>
	<div class="row">
		<?php
			$select = null;
			$allSelect = array();

			foreach (Page::model()->with('commentCount')->findAll() as $page)
			{
				if ($page->commentable && $page->active)
				{
					$allSelect[$page->id] = $page->commentName. ' ('. $page->commentCount.')';
					if (isset($_GET['comment']) && $page->key == $_GET['comment'])
						$select = $page->id;
				}
			}
			if (isset($_POST['Comment']) && isset($_POST['Comment']['pageId']))
				$select = $_POST['Comment']['pageId'];
			if (!$select)
			{
				if ($comment->pageId)
					$select = $comment->pageId;
				else
					$select = Page::model()->findByAttributes(array('key'=>'startseite'))->id;
			}

			$comment->pageId = $select;
			if (!$comment->name)
				$comment->name = 'Gast';
			asort($allSelect);
		?>

		<?php

		$ajaxData = array(
			'ajax' => array(
			'type'=>'POST', //request type
			'url'=>CController::createUrl('/page/guestbook/filter'), //url to call.
			//Style: CController::createUrl('currentController/methodToCall')
			'update'=>'#commentlist', //selector to update
			'data'=>array('filter'=>'js:this.value'),
			//'data'=>'js:javascript statement' 
			//leave out the data key to pass all form values through
		),'class'=>'form-control'); 
		if ($comment->id)
			$ajaxData = array();

		$ajaxData['id'] = 'dropdown_'.$comment->id;

		echo $form->labelEx($comment,'pageId');
		echo CHtml::dropDownList('Comment[pageId]', $select, $allSelect, $ajaxData);
		?>
		<?php echo $form->error($comment,'pageId'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($comment,'name'); ?>
		<?php echo CHtml::activeTextField($comment,'name', array('class'=>'form-control'))?>
		<?php echo $form->error($comment,'message'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($comment,'message'); ?>
		<?php echo $form->textArea($comment,'message',array('class'=>'form-control', 'rows'=>6)); ?>
		<?php echo $form->error($comment,'message'); ?>
	</div>
	<?php if (!Yii::app()->user->isGuest) { ?>
	<div class="row">
		<?php echo $form->labelEx($comment,'createDate'); ?>
		<?php
			$createDate = date('d.m.y', time());
			if ($comment->createDate) // convert from mysql-date to d.m.y
				$createDate = date('d.m.y', strtotime($comment->createDate));
			$this->widget('zii.widgets.jui.CJuiDatePicker', array('name' => 'createDate',
																   'value' => $createDate,
																   'language' => Yii::app()->language,
																   'options' => array('showAnim' => 'fold',),
																   'htmlOptions' => array('class'=>'form-control',),));

		?>
	</div>
	<?php } ?>
	<div class="row buttons">
	    <?php
			// honeypots
			echo '<input type="hidden" name="website" value=""/>';
			//echo '<!-- <input type="text" name="website2" value=""/>-->';
			echo '<input style="display:none" type="text" name="website3" value=""/>';
			$time = time()+5;
			echo '<input type="hidden" name="website4" value="'.$time.'"/>';
			echo '<style type="text/css">.specialstyling {display:none;}</style>';
			echo '<div class="specialstyling">';
			echo 'Hier nichts ausfüllen:<br/>';
			echo 'Ich bin ein Spammer: <input type="radio" value="1" name="website5"><br/>';
			echo 'Ich auch: <input type="radio" value="2" name="website5"><br/>';
			echo '<input type="text" name="website6" value=""/>';
			echo '</div>';

            echo CHtml::hiddenField('returnUrl', $this->createUrl(''));
		    echo CHtml::submitButton('Absenden', array('class'=>'btn btn-primary'));
		?>
	</div>

<?php $this->endWidget() ?>
</div><!-- form -->
