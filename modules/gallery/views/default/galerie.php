<?php
	Yii::import('aiajaya.extensions.jqPrettyPhoto');
	$all_galleries = Yii::app()->getModule('gallery')->galleries;
?>

<div class="gallery">
<?php
	if (count($galleries) > 0)
	{
		$gallery = $galleries[0];
		foreach ($all_galleries as $g)
		{
			$categories = explode(',', $g[1]);
			if (count($categories) == 1)
				continue;

			if (in_array($gallery->cat, $categories))
			{
				echo '<strong>'.$g[0].'</strong> ';
				foreach ($categories as $c)
				{
					$gg = new Gallery($c);
					echo '<a href="'.Yii::app()->createUrl('/gallery/default/index', array('cat'=>$c)).'">'.$gg->title.'</a> ';
				}
			}
		}
    }
    else
    {
?>

<div class="row categorypage">
<?php

	foreach ($all_galleries as $c=>$gallery)
	{
		$caption = $gallery[0];
		$link = array('/gallery/default/index', 'cat'=>$gallery[1]);
		$imgUrl = $gallery[2];
?>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
		<div class="thumbnail">
			<?php echo CHtml::link('<img class="categoryimg" style="height:auto" src="'.$this->baseUrl.$imgUrl.'"/><div class="caption">'.$caption.'</div>', $link) ?>
		</div>
	</div>
<?php if (($c+1)%4==0) echo '<div class="visible-lg clearfix"> </div>'; ?>
<?php if (($c+1)%3==0) echo '<div class="visible-md clearfix"> </div>'; ?>
<?php if (($c+1)%3==0) echo '<div class="visible-sm clearfix"> </div>'; ?>
<?php if (($c+1)%2==0) echo '<div class="visible-xs clearfix"> </div>'; ?>
	<?php } ?>
</div>

<?php
    }
?>
<br/>
<br/>
<?php

	foreach ($galleries as $g)
		$g->print_all();
	jqPrettyPhoto::addPretty('.gallery a.boxpopup', jqPrettyPhoto::PRETTY_GALLERY, jqPrettyPhoto::THEME_FACEBOOK);

?>
</div>
