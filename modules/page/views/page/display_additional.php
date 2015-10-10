<?php
$commentable = Yii::app()->params['enableGuestBook'] && $model->commentable;
if ($commentable)
	$comments = $model->getComments();
?>

<br/>
<br/>
<div class="clearfix"> </div>
<ul class="nav nav-tabs" role="tablist">
	<?php if ($model->events) { ?>
		<li><a href="#events" class="active" role="tab" data-toggle="tab">Termine (<?= count($model->events)?>)</a></li>
	<?php } ?>
	<?php if ($commentable) { ?>
		<li><a href="#comments" role="tab" data-toggle="tab">Gästebuch Kommentare (<?= count($comments)?>)</a></li>
	<?php } ?>
</ul>

<div class="tab-content">
	<?php
	if ($commentable)
	{
		// TODO - this doesn't work with hidden elements - maybe is there some update trigger?
		// I reduced the pagesize so it is no big problem..
		ShortWidgets::addJsShorten(".ext-comment p", 60);
	?>
	<div class="tab-pane text<?php if (!$model->events){?> active<?php } ?>" id="comments">
		<?php if (count($comments)) { ?>
		<br/>
		<?= CHtml::link('<i class="glyphicon glyphicon-comment"></i> ins Gästebuch eintragen', array('/page/guestbook/list/', 'comment'=>$model->key), array('rel'=>'nofollow')) ?>
		<br/>
		<br/>
		<?php
			$criteria = new CDbCriteria(array(
				'condition' => 'pageId IN (:pageId)',
				'params' => array(
					':pageId' => $model->id,
				),
				'order' => 'createDate DESC',
			));
			$dataProvider = new CActiveDataProvider('Comment', array('pagination' => array('pageSize' => 5,),
				'criteria' => $criteria,));

			$this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$dataProvider,
				'itemView'=>'application.modules.page.views.guestbook._view',
				'id'=>'commentlist',
				'viewData'=>array(
					'type'=>'page'
				),
			));
		}
		?>
		<br/>
		<?= CHtml::link('<i class="glyphicon glyphicon-comment"></i> einen Kommentar im Gästebuch schreiben', array('/page/guestbook/list/',
			'comment'=>$model->key), array('rel'=>'nofollow')) ?>
	</div>
	<?php } ?>

	<?php if ($model->events) { ?>
	<div class="tab-pane active text" id="events">
		<br/>
		<?php
		foreach ($model->events as $event)
		{
			echo $this->renderPartial('application.modules.event.views.termin._view', array('data'=>$event, 'type'=>'page'), true);
		}
		?>
	</div>
	<?php } ?>
</div>
