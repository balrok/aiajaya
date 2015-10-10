<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('termin-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Termine verwalten</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'termin-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'cssFile'=>$this->baseUrl.'css/gridview/styles.css',
	'columns'=>array(
		'datum',
		'titel',
		'untertitel',
		'zeit',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
