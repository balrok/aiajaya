<?php

class Copynode extends CAction
{
    public function run()
	{
        $id=explode("_",$_POST['id']);
        $ref=$_POST['ref_id'];
        $type=$_POST['type'];

        $this->getController()->copytree($id[0],$ref,$type);
    }
}
