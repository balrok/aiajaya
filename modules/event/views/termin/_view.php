<div class="termin" itemscope itemtype="http://schema.org/Event">
	<a name="termin<?= $data->id?>"> </a>
	<p class="terminHead">
		<?= CHtml::link('<span class="terminTitle" itemprop="name">'. CHtml::encode($data->titel) .'</span>',
			array('/event/termin/index', '#'=>'termin'.$data->id), array('itemprop'=>'url')) ?>
		<time itemprop="startDate" datetime="<?= date("c", $data->from)?>">
			<?= $data->getDate() ?>
		</time>
	</p>
	<div class="terminDescr"> 
		<span class="terminTime">
			<?= $data->getTime() ?>
			<meta itemprop="location" itemscope itemtype="http://schema.org/Place" itemref="the_addr the_name the_url the_geo" />
			<?php /* only data-vocabulary org if ($data->rubric) { ?>
				<span itemprop="eventType" content="<?= $data->rubric?>"></span>
			<?php } 
			TODO: also list the organizer here - in most cases we have him
			*/ ?>
		</span>
		<span class="terminRight">
			<?= $data->getUntertitel(); ?>
			<br/>
			<span class="icons">
			<?php if ($data->url && $type == 'event'){
				$options = array('class'=>'moreInfos', 'title'=>'Informationen');
				if ($data->isExternUrl())
					$options['target'] = '_blank';
				echo Chtml::link('<i class="glyphicon glyphicon-globe"></i><span class="info"> Informationen</span>',
						$data->getUrl(), $options);
				}
			?>
			<?php if (Yii::app()->params['calendarEnabled']) { ?>
				<?php if ($data->gId){ ?>
					<?= Chtml::link('<i class="glyphicon glyphicon-calendar"></i><span class="info"> Kalender</span>',
						array('/event/calendar/index', 'id'=>$data->id), array('target'=>'_blank', 'title'=>'Kalender', 'class'=>'moreInfos', 'rel'=>'nofollow'))?>
				<?php } ?>
			<?php } ?>
			<?php if (Yii::app()->params['addthisevent']) { ?>
				<?php
					ShortWidgets::addThisEvent('icon',
						$this->createAbsoluteUrl('/event/termin/index', array('#'=>'termin'.$data->id)),
						$data->from,
						$data->to,
						$data->titel,
						$data->getUntertitel(true, false)
					);
				?>
			<?php } ?>
			</span>
		</span>
		<div style="clear:both"> </div>
		&nbsp;
		<?php if (!Yii::app()->user->isGuest && $type == 'event') { ?>
			<hr/>
			<?php
             	echo CHtml::link('bearbeiten', array('edit', 'id'=>$data->id)).' ';
				echo CHtml::link('verdoppeln', array('copy', 'id'=>$data->id), array('class'=>'copy')).' ';
				echo CHtml::link(Yii::t('global', 'Löschen'), array('delete', 'id'=>$data->id),
     			array('class'=>'delete askDel','alt'=>'Möchten Sie diesen Termin wirklich löschen?'));
			?>
		<?php } ?>
	</div>
</div>
