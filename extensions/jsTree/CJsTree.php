<?php
/**
 * CJsTree displays a tree view of hierarchical data.
 *
 * It encapsulates the excellent jsTree component based on CTreeView widget.
 * ({@link http://www.jstree.com/}).
 *
 * To use CJsTree, simply sets {@link data} to the data that you want
 * to present and you are there.
 * @link http://www.yiiframework.com/extension/jstree/#doc Documentation

 *
 * @author Shocky Han <shockyhan@gmail.com>
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 *
 * @version 1.1
 * @package aiajaya.extensions

 * @license http://www.yiiframework.com/license/
 */
/**
 * Revision history.
 * 2010-05-14: Dimitrios Meggidis <tydeas.dr@gmail.com>
 * * Add jsTree data property
 * 2009-06-06: Shocky Han <shokyhan@gmail.com>
 * * initial release
*/
class CJsTree extends CWidget
{
	/**
	 * type = json
	 * url = ajaxurl -- or data = [datastuff]
	 */
	public $data;
	/**
	 * @var mixed the CSS file used for the widget. Defaults to null, meaning
	 * using the default CSS file included together with the widget.
	 * If false, no CSS file will be used. Otherwise, the specified CSS file
	 * will be included when using this widget.
	 */

	// javascript bind to add
	public $bind = array();

	public $cssFile;
	/**
	 * @var array additional options that can be passed to the constructor of the treeview js object.
	 */
	public $options = array();
	/**
	 * @var array additional HTML attributes that will be rendered in the UL tag.
	 * The default tree view CSS has defined the following CSS classes which can be enabled
	 * by specifying the 'class' option here:
	 * <ul>
	 * <li>treeview-black</li>
	 * <li>treeview-gray</li>
	 * <li>treeview-red</li>
	 * <li>treeview-famfamfam</li>
	 * <li>filetree</li>
	 * </ul>
	 */
	public $htmlOptions;

	/*
	 * internal data for jsTree
	 */
	public $baseUrl;	// jsTree install folder. registering scripts & css's under this folder.   
	public $body;		// jsTree Html data source. 

	/**
	 * Initializes the widget.
	 * This method registers all needed client scripts and renders
	 * the tree view content.
	 */
   public function init()
    {
        if(isset($this->htmlOptions['id']))
            $id = $this->htmlOptions['id'];
        else
            $id = $this->htmlOptions['id'] = $this->getId();

        $dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'source';
        $this->baseUrl = Yii::app()->getAssetManager()->publish($dir);

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($this->baseUrl.'/jquery.jstree.js');

        $options = $this->getClientOptions();
        $options = ($options == array())? '{}' : CJavaScript::encode($options);

		$js = '$(function () { $("#'.$id.'").jstree('.$options.')';
		foreach ($this->bind as $k => $v)
			$js.="\n".'.bind("'.$k.'", '.$v.')';

        $cs->registerScript('Yii.CJsTree#'.$id, $js.'; });');
        if($this->cssFile !== null && $this->cssFile !== false)
            $cs->registerCssFile($this->cssFile);
    }

	/**
	 * Ends running the widget.
	 */
	public function run()
	{ 
		echo CHtml::tag('div', $this->htmlOptions, $this->body)."\n";
	}
	/**
	 * @return array the javascript options
	 */
	protected function getClientOptions()
	{
		$options = $this->options;
		if (!isset($options['plugins']))
			$options['plugins'] = array();

		$options['plugins'][] = 'themes';

		return $options;
	}
}
