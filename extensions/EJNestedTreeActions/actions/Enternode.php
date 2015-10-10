<?php

class Enternode extends CAction
{
	public function run()
	{
		$id = $_GET['id'];
		if ($id = (int)$id)
			$data = array('status'=>1,'url'=>$this->getController()->createUrl('view',array('id'=>$id)));
		else
			$data = array('status'=>0);
		$this->getController()->renderJson($data);
	}
}
