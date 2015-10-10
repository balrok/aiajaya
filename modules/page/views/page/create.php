<?php

$this->pageMenu=array(
	array('label'=>'Übersicht', 'url'=>array('index')),
	array('label'=>'Seiten verwalten', 'url'=>array('admin')),
);
?>

<h1>neue Seite</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
