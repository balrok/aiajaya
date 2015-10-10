<h1>Termin #<?php echo $model->id; ?> ansehen</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => 'css/detailview/styles.css',
	'attributes'=>array(
		'id',
		'datum',
		'titel',
		'untertitel',
		'zeit',
	),
)); ?>
