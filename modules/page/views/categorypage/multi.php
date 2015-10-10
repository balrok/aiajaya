<?php
	foreach ($multiModels as $models)
	{
		$this->renderPartial('get', array('models'=>$models));
	}
?>
