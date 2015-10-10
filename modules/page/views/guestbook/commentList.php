<h2>Gästebuch</h2>
<?php

$this->renderPartial('_view', array('type'=>'css'));

/** @var CArrayDataProvider $comments */
//$comments = new CArrayDataProvider(reset($models)->getComments($models));
//$comments->setPagination(true);

if (!isset($_POST['filter'])) {
$this->renderPartial('commentForm', array(
    'model'=>reset($models),
    'models'=>$models,
));
}

$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$comments,
	'itemView'=>'_view',
	'id'=>'commentlist',
	'viewData'=>array(
		'type'=>'guestbook'
	),
));

