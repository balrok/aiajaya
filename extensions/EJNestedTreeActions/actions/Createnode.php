<?php

class Createnode extends CAction
{
    public function run()
	{
		$data = array();
		$data['status'] = 0;

        $refid = $_POST['id'];
        $position = $_POST['position'];

		$modelclass = $this->getController()->names['class'];
		$newnode = new $modelclass();
		$newnode->attributes = $this->getController()->getAttributes(strtolower(__CLASS__));
		if (isset($_POST['type']))
			$newnode->rel = $_POST['type'];
		if (isset($_POST['real_id']))
			$newnode->real_id = $_POST['real_id'];
		if (isset($_POST['title']))
			$newnode->setAttribute($this->getController()->names['text'], $_POST['title']);

		// create root
		if ($refid == -1)
		{
			$data['error'][] = 'Multiple root nodes aren\'t allowed';
			$this->getController()->renderJson($data);
			return;
		}
		else
		{
			$refnode = $this->getController()->getClass()->findByPk($refid);

			if ($jsondata = $this->getController()->insertingnode($newnode, $refnode, $position, true))
			{
				$data['status'] = 1;
				$data['id'] = $newnode->getAttribute($this->getController()->names['identity']);
			}
		}
		$data = $this->getController()->afterModelSave($newnode, $data);

		$this->getController()->renderJson($data);
    }
}
