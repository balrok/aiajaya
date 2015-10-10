<?php

class Render extends CAction
{
	public function run()
	{
		$id = $_GET['id'];

		$nodestoformat = $this->getController()->loadTreeModels($id);
		if ($id == 0 &&!$nodestoformat)
		{
			$modelclass = $this->getController()->names['class'];
			$newnode = new $modelclass();
			$newnode->setAttribute($this->getController()->names['text'], 'root');
			$newnode->setAttribute($this->getController()->names['rel'], 'root');
			$newnode->attributes = $this->getController()->getAttributes('createroot');
			$newnode->saveNode(false, null);
			$nodestoformat = $this->getController()->loadTreeModels($id);
		}

		$rootsdata = $this->getController()->formatNode($nodestoformat);
		$data = $rootsdata;
		$this->getController()->renderJson($data);
	}
}
