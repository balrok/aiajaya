<?php

$this->pageMenu=array(
	array('label'=>'Übersicht', 'url'=>array('index')),
	array('label'=>'neue Seite', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('page-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Seiten verwalten</h1>

<?php echo CHtml::link('erweiterte Suche','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search',array(
		'model'=>$model,
	)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'page-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'cssFile'=>$this->baseUrl.'css/gridview/styles.css',
	'columns'=>array(
		'id',
		'key',
		'meta_keyword',
		'meta_description',
		'meta_title',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
