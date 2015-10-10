<?php
$this->widget('zii.widgets.CListView', array(
       'dataProvider'=>$dataProvider,
       'itemView'=>'_view',
	   'viewData'=>array('type'=>'event'),
       'enablePagination'=>true,
	   'pager'=>'EventPager',
	   'cssFile'=>false,
       'summaryText'=>'',
	   'template'=>'{pager}{summary}{sorter}{items}{pager}',
	   'id'=>'eventlisting',
));

?>
