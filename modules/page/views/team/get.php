<style type="text/css">
#team h3, h5 {
    margin-top:5px;
    margin-bottom:5px;
}

.oben {
    background-image:url("../bilder/team/top.gif");
    background-position:right top;
    background-repeat:no-repeat;
    color:#000000;
    float:right;
    font-weight:bold;
    padding-right:15px;
    margin-bottom:4px;
    font-size:9px;
}

.topLinks a{
    font-size:14px;
}

</style>




<div id="team">

<!-- put the rich snippet here so it doesn't repeat -->
<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" id="team_addr">
	<!-- assume everyone is from saxony -->
	<meta itemprop="addressRegion" content="Sachsen" />
	<meta itemprop="addressCountry" content="DE" />
</span>
<meta itemprop="memberOf" itemscope itemtype="http://schema.org/Organization" itemref="the_name the_url the_logo the_addr" id="team_memberof"/>

<?php
	if (count($models) == 1)
	{
		echo '<h2 style="text-align:center;margin-top:0px">'.$models[0]->name.' aus unserem Team</h2>';
	}
	else
	{
		echo '<h2 style="text-align:center;margin-top:0px">Unser Team stellt sich vor</h2>';
	}
?>
<div class="topLinks">
<?php
	if (count($models) == 1)
	{
		echo CHtml::link('Das gesamte Team ansehen', array('/page/team/get'));
	}
	else
	{
		$last = end($models);
		foreach ($models as $c=>$model)
		{
			echo '<a href="#'.$model->key.'">'.str_replace(' ', '&nbsp;', $model->name).'</a>';
			if ($model->id != $last->id)
				echo ', ';
		}
	}
?>
</div>
<?php
foreach ($models as $c=>$model)
{
?>
	<div class="row" style="margin-top:20px" itemscope itemtype="http://schema.org/Person" itemref="team_addr team_memberof">
		<div class="col-xs-3 img">
			<?= CHtml::image(Yii::app()->baseUrl.'/bilder/'.$model->image, $model->name, array('class'=>'img-responsive',
				'itemprop'=>'image')) ?>
		</div>
		<div class="col-xs-9">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#team_<?= $model->id?>_text" role="tab" data-toggle="tab">Zur Person</a></li>
				<?php if ($model->pages) { ?>
					<li><a href="#team_<?= $model->id?>_pages" role="tab" data-toggle="tab">Angebote (<?= count($model->pages)?>)</a></li>
				<?php } ?>
				<?php if ($model->events) { ?>
					<li><a href="#team_<?= $model->id?>_events" role="tab" data-toggle="tab">Termine (<?= count($model->events)?>)</a></li>
			  	<?php } ?>
				<li><a href="#team_<?= $model->id?>_contact" role="tab" data-toggle="tab">Kontakt</a></li>
			</ul>

		<div class="tab-content">
			<div class="tab-pane active text" id="team_<?= $model->id?>_text">
				<?= CHtml::link('', array('/page/team/get', 'key'=>$model->key), array('itemprop'=>'url')) ?>
				<h3 id="<?= $model->key?>"><span itemprop="name"><?= $model->name?></span></h3>
				<?php if ($model->subheader) { ?>
					<em><?= str_replace('--', '<br/>', $model->subheader)?></em>
				<?php } ?>
				<div itemprop="description">
					<?= $model->text ?>
				</div>

				<!-- google requires some attributes, which I don't really have  and don't want to show-->
				<div style="display:none">

					<?= $model->email?'<span itemprop="email">'.$model->email.'</span>':''?>
					<?= $model->mobile?'<span itemprop="telephone">'.$model->mobile.'</span>':''?>
					<?= $model->web?'<span itemprop="url">'.$model->web.'</span>':''?>
					<?php /* if ($model->name == 'Katarina Heidenreich'){ ?>
						<span itemprop="jobTitle">Leiterin</span>
					<?php }else{ ?>
						<span itemprop="jobTitle">Mitarbeiter</span>
					<?php } */ ?>
				</div>
			</div>
			<?php if ($model->pages) { ?>
				<div class="tab-pane" id="team_<?= $model->id?>_pages">
					<ul class="hyphen">
					<?php
						$pages = array();
						foreach ($model->pages as $page)
						{
							$url = $page->getUrl();
							if (isset($page['preKey']) && $page['preKey'])
							{
								if (!isset($pages[$page['preKey']]))
									$pages[$page['preKey']] = array();
								$pages[$page['preKey']][] = $page;
							}
							else
							{
								if (!isset($pages['zzz']))
									$pages['zzz'] = array();
								$pages['zzz'][] = $page;
							}
						}
						ksort($pages);

					foreach ($pages as $type=>$page2) {
//						echo '<li><strong>'.$type.'</strong></li>';
						foreach ($page2 as $page)
						{
					?>
						<li><?php echo CHtml::link($page->meta_title, $page->getUrl())?></li>
					<?php }} ?>
					</ul>
				</div>
			<?php } ?>

			<?php if ($model->events) { ?>
				<div class="tab-pane" id="team_<?= $model->id?>_events">
					<ul class="hyphen">
					<?php
					foreach ($model->events as $event)
					{
						echo '<li><div style="float:left;width:100px">'.strftime("%a., %d. %b.", $event->from). "</div> ".
							CHtml::link($event->titel, array('/event/termin/index', '#'=>'termin'.$event->id)).'</li>';
					}
					?>
					</ul>
				</div>
			<?php } ?>
			<div class="tab-pane" id="team_<?= $model->id?>_contact">
				<?= $model->getContact(); // TODO use microformats, other layout ?>
				<?= CHtml::link("Kontakt speichern (vcard)", array('/page/team/vcard', 'key'=>$model->key)) ?>
			</div>
		</div>
		</div>
	</div>
	<a class="oben" href="#top">nach oben</a>
	<div class="clearfix"> </div>
<?php
}
?>
</div>

<?php
	if (!Yii::app()->user->isGuest)
		$this->bottomAdmin = CHtml::link('Seite bearbeiten', array('team/update'));
?>
<script type="text/javascript">
$('.nav-tabs a').click(function (e) {
	  e.preventDefault()
	    $(this).tab('show')
})
</script>
