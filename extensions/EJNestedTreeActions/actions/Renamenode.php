<?php

class Renamenode extends CAction
{
    public function run()
	{
		$data = array();
		$data['status'] = 0;

        $id=$_POST['id'];
        $node= $this->getController()->getClass()->findByPk($id);
        if($node->isRoot())
			$siblings = $this->getController()->getClass()->roots()->findall($this->getController()->criteria);
        else {
            $parent = $node->parent();
			$siblings=$parent->children()->findAll($this->getController()->criteria);
        }
   
        $node->setAttribute($this->getController()->names['text'], $_POST['title']);

		$node->validate();
		if($node->saveNode())
			$data['status'] = 1;

		$this->getController()->renderJson($data);
    }
}
