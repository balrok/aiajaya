<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CKEditor
 *
 * @author Ekstazi
 * v 0.2
 */
class CKEditor extends CInputWidget
{
    
    public $options=array();

    public $fontFamilies=array(
        'Arial'=>'Arial, Helvetica, sans-serif', 
        'Comic Sans MS'=>'Comic Sans MS, cursive',
        'Courier New'=>'Courier New, Courier, monospace',
        'Georgia'=>'Georgia, serif',
        'Lucida Sans Unicode'=>'Lucida Sans Unicode, Lucida Grande, sans-serif',
        'Tahoma'=>'Tahoma, Geneva, sans-serif',
        'Times New Roman'=>'Times New Roman, Times, serif',
        'Trebuchet MS'=>'Trebuchet MS, Helvetica, sans-serif',
        'Verdana'=>'Verdana, Geneva, sans-serif',
    );

    public $fontSizes = array(
        '8'=>'8px',
        '9'=>'9px',
        '10'=>'10px',
        '11'=>'11px',
        '12'=>'12px',
        '14'=>'14px',
        '16'=>'16px',
        '18'=>'18px',
        '20'=>'20px',
        '22'=>'22px',
        '24'=>'24px',
        '26'=>'26px',
        '28'=>'28px',
        '36'=>'36px',
        '48'=>'48px',
        '72'=>'72px'
    );

    public $toolbar=array();
    
    public $skin='kama';
    public $theme='default';


    public function  __construct($owner=null) {
        parent::__construct($owner);
        $this->options['language'] = Yii::app()->language;
    }

    protected function makeOptions()
    {
		$options['font_names']='';
        foreach($this->fontFamilies as $k=>$v)
            $options['font_names'].=$k.'/'.$v.';';

        $options['fontSize_sizes']='';
        foreach($this->fontSizes as $k=>$v)
            $options['fontSize_sizes'].=$k.'/'.$v.';';
        
		$options = array_merge($options, $this->options);
        return CJavaScript::encode($options);
   }

    public function run(){

        parent::run();

        list($name, $id) = $this->resolveNameID();

        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');

        $options = $this->makeOptions();

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($assets.'/ckeditor.js');
        $this->htmlOptions['id'] = $id;

        $js =<<<EOP
CKEDITOR.replace('{$name}',{$options});
EOP;
        $cs->registerScript('Yii.'.get_class($this).'#'.$id, $js, CClientScript::POS_LOAD);

        if($this->hasModel())
            $html = CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
		else
            $html = CHtml::textArea($name, $this->value, $this->htmlOptions);

        echo $html;
    }
}
?>
