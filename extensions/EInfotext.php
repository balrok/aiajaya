<?php

/**
 * This is the model class for table "{{infotext}}".h
 */
class EInfotext extends CActiveRecord
{
	public $textarea = false;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{info_text}}';
	}

	public function rules()
	{
		return array(
			array('info', 'safe'),
		);
	}

	protected function beforeSave()
	{
		if (!$this->textarea)
			$this->info = $this->parse($this->info);
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		parent::afterSave();
		if (!$this->textarea)
			$this->info = $this->unparse($this->info);
	}

	protected function afterFind()
	{
		if (!$this->textarea)
			$this->info = $this->unparse($this->info);
		parent::afterFind();
	}


    /*  
    input: $str: plain text
    returns: string with html information
    */
    protected static function parse($str)
    {   
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8');

        $str = str_replace('--','<br/>',$str);
        $str = str_replace("\n",'<br/>',$str);
        $str = str_replace('  ','&nbsp; ',$str);
        // valid
        $str = str_replace("<br>",'<br/>',$str);
        return $str;
    }   

    /*  
    input: $str with html-information
    returns: string without information
    */
    protected static function unparse($str, $textarea=false)
    {   
        $str = html_entity_decode($str,ENT_QUOTES, 'UTF-8');
        if (!$textarea)
        {   
            $str = str_replace('<br/>','--',$str);
            $str = str_replace('<br>','--',$str);
        }   
        else
        {   
            $str = str_replace('<br/>','',$str);
            $str = str_replace('<br>','',$str);
        }   
        $str = str_replace('&nbsp;',' ',$str);
        $str = preg_replace("<a target=\"_blank\" href=\"[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]\">", '', $str);
        $str = str_replace('</a>','',$str);
        return $str;
    }

	public function getInfotext()
	{
		return $this->parse($this->info);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'info' => 'Text',
		);
	}

	public function get($id)
	{
		$info = EInfoText::model()->findByPk($id);
		if (!$info)
		{
			$info = EInfotext::model();
			$info->attributes = array('id'=>$id);
			$info->save(false);
		}
		return $info;
	}
}
