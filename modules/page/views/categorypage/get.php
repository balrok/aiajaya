<?php
	$example = end($models);
?>
<h2 align="center"><u><?= $example->categoryname?></u></h2>

<div class="row categorypage">
<?php
	foreach ($models as $c=>$model)
	{
		$link = array('page/get', 'key'=>$model->pagekey, 'preKey'=>$model->categorykey);
		if ($model->pagekey == 'team')
			$link = array('/page/team/get');

		if (substr($model->pageimg, 0, 4) == 'http')
			$imgUrl = $model->pageimg;
		else
			$imgUrl = $this->baseUrl.'bilder/'.$model->pageimg;
		$caption = $model->pagename;
?>
<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
	<div class="thumbnail">
		<?php echo CHtml::link('<img class="img-responsive categoryimg" src="'.$imgUrl.'"/><div class="caption">'.$caption.'</div>', $link) ?>
	</div>
</div>
<?php if (($c+1)%4==0) echo '<div class="visible-lg clearfix"> </div>'; ?>
<?php if (($c+1)%3==0) echo '<div class="visible-md clearfix"> </div>'; ?>
<?php if (($c+1)%3==0) echo '<div class="visible-sm clearfix"> </div>'; ?>
<?php if (($c+1)%2==0) echo '<div class="visible-xs clearfix"> </div>'; ?>

<?php
	}
?>
</div>

<?php
	if (!Yii::app()->user->isGuest)
		$this->bottomAdmin = CHtml::link('Seite bearbeiten', array('categorypage/update', 'key'=>$example->categorykey));
?>
