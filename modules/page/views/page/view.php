<?php

$this->pageMenu=array(
	array('label'=>'neue Seite', 'url'=>array('create')),
	array('label'=>'Seite ändern', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Seite löschen', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Bist du dir sicher?')),
);
?>

<h1>View Page #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile'=>'css/detailview/styles.css',
	'attributes'=>array(
		'id',
		'key',
		'meta_keyword',
		'meta_description',
		'meta_title',
		'text',
	),
)); ?>
