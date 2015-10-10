<?php

class Movenode extends CAction
{
    public function run()
	{
		$data = array();
		$data['status'] = 0;
		$data['error'] = array();

        $id 	= $_POST['id'];
        $refid 	= $_POST['ref'];
        $isroot = $_POST['isroot'];
        $position = $_POST['position'];
		
		// currently moving root nodes isn't supported by the nestedset extension
		if ($isroot == 'true')
		{
			$data['error'][] = 'Moving root nodes isn\' allowed';
			$this->getController()->renderJson($data);
			return;
		}

		$refnode = $this->getController()->getClass()->findByPk($refid);
		$current = $this->getController()->getClass()->findByPk($id);

		// error checking
		if (!$refnode)
			$data['error'][] = 'Target node couldn\'t be found';
		if (!$current)
			$data['error'][] = 'Selected node couldn\'t be found';
// TODO make that check work
//        if ($refnode->isRoot())
//			$data['error'][] = 'No multiple roots allowed';
		if (empty($data['error']))
		{
			$differentparent = $refnode->getAttribute($this->getController()->names['identity'])!=$current->getAttribute($this->getController()->names['identity']);
			if (!$differentparent)
				$data['error'][] = 'Don\'t move a node to itself.';
		}
		if (!empty($data['error']))
		{
			$this->getController()->renderJson($data);
			return;
		}

		// copy is handled special
		if ($_POST['copy'])
		{
        	$this->getController()->copytree($current, $refnode, $position);
			$data['status'] = 1;
			$this->getController()->renderJson($data);
			return;
		}

        if ($this->getController()->moveingNode($current, $refnode, $position))
			$data['status'] = 1;

		$this->getController()->renderJson($data);
    }
}
