<?php
class jqPrettyPhoto extends CComponent {
	
	const THEME_FACEBOOK 		= "facebook"; 
	const THEME_DARK_ROUNDED	= "dark_rounded";
	const THEME_DARK_SQUARE		= "dark_square";
	const THEME_LIGHT_ROUNDED 	= "light_rounded";
	const THEME_LIGHT_SQUARE	= "liht_square";
	  
	const PRETTY_SINGLE 	= 1; // create pretty for single links?
	const PRETTY_GALLERY 	= 2; // create pretty gallery?
	
	protected static function registerScript(){
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/../assets');
		$cs->registerScriptFile($assets.'/prettyPhoto/js/jquery.prettyPhoto.js');
		$cs->registerCssFile($assets.'/prettyPhoto/css/prettyPhoto.css');
	}
	
	public static function addPretty($jsSelector=".gallery a", $gallery=self::PRETTY_GALLERY, $theme=self::THEME_FACEBOOK){
		self::registerScript();
		
		Yii::app()->clientScript->registerScript(__CLASS__,'
			$("'.$jsSelector.'").attr("rel","prettyPhoto'.($gallery==self::PRETTY_GALLERY?'[pp_gal]':'').'") ;
			$("a[rel^=\'prettyPhoto\']").prettyPhoto({theme:\''.$theme.'\'});
		',CClientScript::POS_READY);
	}
	
	
}
