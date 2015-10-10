<?php

	Yii::app()->clientScript->registerCss('ext-comment', "
	div.ext-comment hr {
		margin: 0;
		padding: 0;
		border: none;
		border-bottom: solid 1px #aaa;
	}
	span.ext-comment-name {
		font-weight: bold;
	}
	span.ext-comment-head {
		color: #aaa;
	}
	span.ext-comment-options {
		float: right;
		color: #aaa;
	}
	");
	if ($type == 'css')
		return;
?>
<?php
	$page = $data->getBaseModel();
	$category = $page->getCategory();
?>
<div class="ext-comment" id="ext-comment-<?php echo $data->id; ?>">
	<div class="row ext-comment-head">
		<div class="col-xs-12">
			<span class="ext-comment-name"><?php echo CHtml::encode($data->name); ?></span>
			<?= Yii::t('comment', 'am')?>
			<?php
				$absDate = date('d.m.Y', strtotime($data->createDate));
			?>
			<span class="hint ext-comment-date"><?php echo $absDate?></span>
			<?php if ($page && $page->commentName && $type != 'page'): ?>
				in
				<?php
					if ($category)	
						echo CHtml::link($category->categoryname, array('/page/page/get', 'key'=>$category->categorykey)).' / ';
					echo CHtml::link($page->commentName, $page->getUrl());
				?>
			<?php endif;?>
			:
		</div>
	</div>

	<hr />

	<div class="row">
		<?php if ($type != 'page') { ?>
		<div class="col-xs-2">
			<?php
				if ($category)
					echo CHtml::link(CHtml::image($category->getPageImg(), '', array('class'=>"thumbnail img-responsive")), $page->getUrl());
			?>
		</div>
		<?php } ?>


		<div class="col-xs-10">
			<span class="ext-comment-options">
			<?php if (!Yii::app()->user->isGuest && $type == 'guestbook'){ 
				echo CHtml::ajaxLink('löschen', array('/page/guestbook/delete', 'id'=>$data->id), array(
					'success'=>'function(){ $("#ext-comment-'.$data->id.'").remove(); }',
					'type'=>'POST',
				), array(
					'id'=>'delete-comment-'.$data->id,
					'confirm'=>'Bist du dir sicher?',
				));
				echo " | ";
				echo CHtml::ajaxLink('bearbeiten', array('/page/guestbook/update', 'id'=>$data->id), array(
					'replace'=>'#ext-comment-'.$data->id,
					'type'=>'GET',
				), array(
					'id'=>'ext-comment-edit-'.$data->id,
				));
			} ?>
			</span>

			<p><?php echo nl2br(CHtml::encode($data->message)); ?></p>
		</div>
	</div>
</div>
<div class="clearfix"> </div>
