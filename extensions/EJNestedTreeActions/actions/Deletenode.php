<?php

class Deletenode extends CAction
{
    public function run()
	{
		$data = array();
		$data['status'] = 0;

        $id = $_POST['id'];
        $nodetodelete = $this->getController()->getClass()->findByPk($id);               
        
		if ($nodetodelete)
		{
			if ($data = $this->getController()->beforeModelDelete($nodetodelete, $data))
				if ($nodetodelete->deleteNode())
					$data['status'] = 1;
		}

		$this->getController()->renderJson($data);
    }
}
