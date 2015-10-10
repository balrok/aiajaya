<?php
class EBehavior extends CBehavior {
	// specify a criteria with which the tree should be searched:
	// for example: array('column1'=>2)
	public $criteria;

	// will set the initial values for the columns
	// array('default'=>array('name'=>'string'), // default is special and will apply to all
	//	array('createroot'=>array('userid'=>1),
	// array('createnode'=>array('name'=>'other string')), // will overwrite default
	public $attributes;

	public $names = array(
		// @var string the name of the model class
		'class' => '',
		// @var string the name of the id attribute of model
		'identity' => '',
		// @var string the name of the rel attribute of model
		'rel' => '',
		// @var string the attribute of model that will displayed as node title
		'text' => '',
	);
	/**
	 * If when creating node should check the future brothers for same name and change the new
	 * node name to old i++
	 * @var boolean
	 */
	public $nodenaming;

	// mapping declares how to map the model to jstree-data
	public $mapping = array();

	public function getMapping()
	{
		// don't know where to set this problem is that in construct this->text doesnt exist
		if (empty($this->mapping))
		{
			$this->mapping = array('data'=>$this->names['text'],
				'attr'=>array(
					'id'=>$this->names['identity'],
				));
			if ($this->rel)
				$this->mapping['attr']['rel'] = $this->names['rel'];
		}
		return $this->mapping;
	}

	public function __construct()
	{
		if(!$this->criteria)
			$this->criteria = array();
	}

	/**
	 * Used internal function that takes a node and returns it as string.
	 *
	 * @param mixed of CActiveRecord array of $models or single model
	 * @return string In jstree format.
	 */
	public function formatNode($models)
	{
		if(is_array($models))
		{
			$nodesarray = array();
			foreach($models as $i => $node)
				$nodesarray[] = $this->formatSingleNode($node);
			return $nodesarray;
		}
		return $this->formatSingleNode($models);
	}

	// formats a single model to json
	public function formatSingleNode($model)
	{
		$jstreeformat = array();
		$this->_mapToJson($jstreeformat, $this->getMapping(), $model);
		if(!$model->isLeaf())
			$jstreeformat['state']="closed";
		return $jstreeformat;
	}

	private function _mapToJson(&$arr, $mapping, $model)
	{
		foreach ($mapping as $k=>$v)
		{
			if (!is_array($v))
				$arr[$k] = $model->getAttribute($v);
			else
				$this->_mapToJson($arr[$k], $v, $model);
		}
	}

	/**
	 * This method search for brother nodes with same name.
	 * Used internally when trying to rename,create or move
	 * @param CActiveRecord $parent the parent of the node if null then $node is root
	 * @param CActiveRecord $node the node that' been created/moved/renamed
	 * @return CActiveRecord $node the node with the proper name
	 */
	public function nodeNaming($parent=null,$node) {
		if($parent!=null) {
			$brothers=$parent->children()->findAll(array(
						'condition'=>'`'.$this->names['text'].'` LIKE :dfname',
						'params'=>array(':dfname'=>$node->getAttribute($this->names['text']).'%'),
						)
					);
		} else {
			$brothers = CActiveRecord::model($this->names['class'])->roots()->findall();
		}

		$name = $node->getAttribute($this->names['text']);
		$i=1;
		$namenotfound=true;
		do {
			$namenotfound = $this->nameExist($brothers,$node);
			if ($namenotfound)
			{
				$node->setAttribute($this->names['text'], $name." ".$i);
				$i++;
			}
		} while($namenotfound);
		return $node;
	}
	/**
	 * This method is used internally. It takes the an array of the nodes and
	 * second node as well.
	 * At create node the array of nodes is the possible brothers of the new node.
	 * Because new node has not an id yet the second part will return true.     
	 * At rename the array of nodes contains the new node as well this is why the 
	 * second part of if checks if the $bro id is different from the $new id.
	 * The second part of if is needed for the move node action too.
	 * @param CActiveRecord $bro
	 * @param CActiveRecord $new
	 * @return boolean true if name already exist false if not exist
	 */
	public function nameExist($bro,$new)
	{
		foreach($bro as $i=>$bro )
		{
			if ($bro->getAttribute($this->names['text']) == $new->getAttribute($this->names['text'])
				&& $bro->getAttribute($this->names['identity']) != $new->getAttribute($this->names['identity']))
				return true;
		}
		return false;
	}

	public function insertingnode($newnode=null, $refnode=null, $position=0, $nodenaming=true)
	{
		if ($nodenaming)
			$this->nodeNaming($refnode, $newnode);

		if ($position == 0)
			return $refnode->prepend($newnode, false);
		else
		{
			$childs = $refnode->children()->findAll();
			if (count($childs) < $position)
				$position = count($childs);

			if ($position == count($childs))
				return $refnode->append($newnode,false);
			else
				return ($childs[$position-1] && $newnode->insertAfter($childs[$position-1], false));
		}
	}

	public function moveingNode($node=null, $refnode=null, $position=0)
	{
		if ($position == 0)
			return $node->moveAsFirst($refnode, false);
		else
		{
			$childs = $refnode->children()->findAll(); // TODO select just the required child (limit?)
			if (count($childs) < $position)
				$position = count($childs);

			// distincting those scenarios might or might not give speed
			if ($position == count($childs))
				return $node->moveAsLast($refnode,false);
			else
				return ($childs[$position-1] && $node->moveAfter($childs[$position-1], false));
		}
	}

	/**
	 * It copies a node and his childs.
	 * @param string $id The id of the node to be copied
	 * @param string $ref The id of a reference node that is used with type to determine the new position
	 * @param string $type Where the new node will be copied
	 */
	public function copytree($node, $refnode, $position)
	{
		$classname=$this->classname;

		$copy = new $classname();
		$copy->attributes = $node->attributes;
		$this->insertingnode($copy, $refnode, $position, true);

		if(!$node->isLeaf())
		{
			$childs = $node->children()->findall();
			foreach( $childs as $i => $chnode ) {
				$this->copytree($chnode, $copy, 99999, false);
			}
		}
	}

	public function getAttributes($class)
	{
		$attributes = $this->attributes;
		$default_att = (isset($attributes['default']))?$attributes['default']:array();
		$this_att = (isset($attributes[$class]))?$attributes[$class]:array();
		return CMap::mergeArray($default_att, $this_att);
	}

	// will render an array to json
	public function renderJson($arr)
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo CJSON::encode($arr);
		Yii::app()->end();
	}

	// dummy methods - can be overriden
	// returns an array which gets merged with the json data

	public function afterModelSave($model, $data){
		return $data;
	}
	public function beforeModelDelete($model, $data){
		return $data;
	}

	// method which will load the tree
	// supposed to be overriden
	public function loadTreeModels($id)
	{
		if ($id == 0)
			return $this->getClass()->roots()->findall($this->criteria);
		else
		{
			$node = $this->getClass()->findByPk($id);
			if (!$node)
				return array();
			return $node->children()->findall($this->criteria);
		}
	}

	public function getClass()
	{
		return CActiveRecord::model($this->names['class']);
	}

}
