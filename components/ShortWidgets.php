<?php

class ShortWidgets
{
	// $model = model object
	// $attribute = attribute which should be edited
	static function ckEditor($model, $attribute, $options=array(), $htmlOptions=array())
	{
		$options = array();

		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/../assets');
		$options['filebrowserBrowseUrl'] 		= $assets.'/kcfinder/browse.php?opener=ckeditor&type=files';
		$options['filebrowserImageBrowseUrl'] = $assets.'/kcfinder/browse.php?opener=ckeditor&type=images';
		$options['filebrowserFlashBrowseUrl'] = $assets.'/kcfinder/browse.php?opener=ckeditor&type=flash';
		$options['filebrowserUploadUrl'] 		= $assets.'/kcfinder/upload.php?opener=ckeditor&type=files';
		$options['filebrowserImageUploadUrl'] = $assets.'/kcfinder/upload.php?opener=ckeditor&type=images';
		$options['filebrowserFlashUploadUrl'] = $assets.'/kcfinder/upload.php?opener=ckeditor&type=flash';

		$options['allowedContent'] = true; // otherwise it might remove <figure> tag or other

		Yii::app()->getController()->widget('aiajaya.extensions.ckeditor.CKEditor', array(
			'model'=>$model,
			'attribute'=>$attribute,
			'options'=>
				CMap::mergeArray(
					array(
						'language'=>Yii::app()->language,
						'toolbar'=>array(
						   array('Source', '-', 'Undo','Redo','-','Find','Replace','-','RemoveFormat'),
						   array('Bold','Italic','Underline','Strike'),
						   array('NumberedList','BulletedList','-','Outdent','Indent'),
						   array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'),
						   array('Link','Unlink'),
						   array('Image','Table','HorizontalRule'),
						   array('Format','Font','FontSize'),
						   array('TextColor','BGColor'),
						   array('Maximize')
						),
						'extraPlugins'=>'htmlwriter,image2,autogrow',
						'autogrow_on_startup'=>true,
						'resize_enabled'=>true,
						'autogrow_minHeight'=>400,
						'removePlugins'=>'scayt,entities',
						'height'=>'600px',
						'widht'=>'100%',
						//'resize_maxWidth'=>400,
						'skin'=>'office2013',
						'contensCss'=>array(Yii::app()->theme->baseUrl.'/bootstrap/css/bootstrap.min.css', Yii::app()->theme->baseUrl.'css/style.css'),
					),
					$options
				),
			'htmlOptions'=>$htmlOptions,
		));
	}

	static function addJsShorten($selector, $height=100)
	{
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/../assets');
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile($assets.'/jquery.dotdotdot-1.6.16.min.js');
		$cs->registerCoreScript('jquery');
        $js =<<<EOP
			jQuery("$selector").each(function(){
				jQuery(this).append(
					jQuery("<a>").attr("href", "javascript:void(0)").addClass("read-more").html("weiterlesen").hide()
				);
			});

			jQuery('$selector').dotdotdot({
				height: $height,
				after: "a.read-more",
				callback : function(isTruncated, orgContent ) {
					if (isTruncated) {
						jQuery(this).find("a.read-more").show();
					}
				}
			});

		    jQuery("$selector a.read-more").click(function() {
				var textEl = jQuery(this).closest("$selector");
				var content = textEl.triggerHandler("originalContent.dot");
				textEl.html("");
				textEl.append(content);
				//textEl.append(
				//	jQuery("<a>").attr("href", "javascript:void(0)").addClass("read-less").html("Text einklappen")
				//);
			});
EOP;
		    //jQuery("$selector a.read-less").live("click", function() {
			//	var textEl = jQuery(this).parent("$selector");
			//	textEl.trigger("update.dot");
			//	//jQuery(this).remove();
			//});
        $cs->registerScript('Yii.Shortwidgets.addjsshorten', $js, CClientScript::POS_LOAD);
	}

	static function addThisEvent($theme, $link, $start, $end, $summary, $description, $organizer='', $organizer_email='')
	{
		$location = Yii::app()->params['address']['street'] . ' ' .  Yii::app()->params['address']['zip'] . ' ' .  Yii::app()->params['address']['city'];
		if (!$organizer)
			$organizer = 'Balance - Zentrum für Energie und Körperarbeit';
		if ($theme == 'calendar')
		{
			echo '<div class="addthisevent">'. //href="'.$link.'" title="Zu Terminkalender hinzufügen" class="addthisevent">
			//'Zu Terminkalender hinzufügen'.
			'<div class="date">
				<span class="mon">'.date('M', $start).'</span>
				<span class="day">'.date('d', $start).'</span>
				<div class="bdr1"></div>
				<div class="bdr2"></div>
			</div>'.

			'<div class="desc">
				<p>
					<strong class="hed">'.$summary.'</strong>
					<span class="des">'.$description.'</span>
				</p>
			</div>';
		}
		else if ($theme == 'icon')
		{
			echo '<a class="moreInfos addthisevent addthisevent-drop icon" title="Speichern" href="'.$link.'"
			data-track="'."_gaq.push(['_trackEvent','AddThisEvent','click','ate-calendar'])".'" >';
			echo '<i class="glyphicon glyphicon-plus"> </i>';
			echo '<span class="info"> Speichern</span>';
		}

		echo '<span class="_start">' . date('d-m-Y H:i:s', $start) . '</span>'.
			'<span class="_end">' . date('d-m-Y H:i:s', $end) . '</span>
			<span class="_zonecode">38</span>
			<span class="_summary">'.$summary.'</span>
			<span class="_description">'.$description.'</span>
			<span class="_location">'.$location.'</span>'.
			'<span class="_organizer">'.$organizer.'</span>'.
			'<span class="_organizer_email">'.$organizer_email.'</span>'.
			//'<span class="_facebook_event">http://www.facebook.com/events/160427380695693</span>'.
			'<span class="_all_day_event">false</span>'.//'.((($end-$start)>60*60*24)?'true':'false').'</span>'.
			'<span class="_date_format" style="display:none">DD.MM.YYYY</span>';
		if ($theme == 'calendar')
			echo '</div>';
		else
			echo '</a>';

		
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('https://addthisevent.com/libs/ate-latest.min.js');


		$settings = array(
			'license' => Yii::app()->params['addthisevent'],
			'mouse' => false,
			'css' => $theme != 'calendar' && $theme != 'icon',
    		'dropdown' => array('order'=>'google,ical,outlook'),
			);
		$js = 'addthisevent.settings('.CJSON::encode($settings).');';

        $cs->registerScript('Yii.Shortwidgets.addthisevent', $js, CClientScript::POS_LOAD);

		if ($theme == 'calendar')
		{
			$cs->registerCss('addthisevent', '
			.addthisevent-drop
			{width:280px;display:inline-block;position:relative;z-index:999998;cursor:pointer;background:#f7f7f7;font-family:"Segoe
			UI",Frutiger,"Frutiger Linotype","Dejavu Sans","Helvetica Neue",Arial,sans-serif;color:#333!important;font-size:15px;border:1px
			solid #cfcfcf;-webkit-box-shadow:1px 1px 3px rgba(0,0,0,0.15);-moz-box-shadow:1px 1px 3px rgba(0,0,0,0.15);box-shadow:1px 1px 3px
			rgba(0,0,0,0.15);-webkit-border-radius:2px;border-radius:2px;}
			.addthisevent-drop:hover 				{background-color:#f4f4f4;}
			.addthisevent-drop:active 				{top:1px;}
			.addthisevent-drop .date 				{width:60px;height:60px;float:left;position:relative;}
			.addthisevent-drop .date .mon 			{display:block;text-align:center;padding:9px 0px 0px
			0px;font-size:11px;color:#bf5549;font-weight:bold;line-height:110%;text-transform:uppercase;}
			.addthisevent-drop .date .day 			{display:block;text-align:center;padding:0px 0px 8px
			0px;font-size:30px;font-weight:bold;color:#333;line-height:100%;}
			.addthisevent-drop .date .bdr1 			{width:1px;height:50px;background:#eaeaea;position:absolute;z-index:100;top:5px;right:-3px;}
			.addthisevent-drop .date .bdr2 			{width:1px;height:50px;background:#fff;position:absolute;z-index:100;top:5px;right:-4px;}
			.addthisevent-drop .desc 				{width:210px;height:60px;float:left;position:relative;}
			.addthisevent-drop .desc p 				{margin:0;display:block;text-align:left;padding:7px 0px 0px
			18px;font-size:12px;color:#666;line-height:110%;}
			.addthisevent-drop .desc .hed
			{height:15px;display:block;overflow:hidden;margin-bottom:3px;font-size:14px;line-height:110%;color:#333;text-transform:uppercase;}
			.addthisevent-drop .desc .des 			{height:28px;display:block;overflow:hidden;}
			.addthisevent-selected 					{background-color:#f4f4f4;}
			.addthisevent_dropdown 					{width:280px;position:absolute;z-index:99999;padding:6px 0px 0px
			0px;background:#fff;text-align:left;display:none;margin-top:-2px;margin-left:-1px;border:1px solid #cfcfcf;-webkit-box-shadow:1px
			3px 6px rgba(0,0,0,0.15);-moz-box-shadow:1px 3px 6px rgba(0,0,0,0.15);box-shadow:1px 3px 6px rgba(0,0,0,0.15);}
			.addthisevent_dropdown span
			{display:block;line-height:110%;background:#fff;text-decoration:none;font-size:14px;color:#6d84b4;padding:8px 10px 9px 15px;}
			.addthisevent_dropdown span:hover 		{background:#f4f4f4;color:#6d84b4;text-decoration:none;font-size:14px;}
			.addthisevent span 						{display:none!important;}
			.addthisevent-drop ._url,.addthisevent-drop ._start,.addthisevent-drop ._end,.addthisevent-drop ._summary,.addthisevent-drop
			._description,.addthisevent-drop ._location,.addthisevent-drop ._organizer,.addthisevent-drop ._organizer_email,.addthisevent-drop
			._facebook_event,.addthisevent-drop ._all_day_event {display:none!important;}
			.addthisevent_dropdown .copyx 			{height:21px;display:block;position:relative;cursor:default;}
			.addthisevent_dropdown .brx
			{width:180px;height:1px;overflow:hidden;background:#e0e0e0;position:absolute;z-index:100;left:10px;top:9px;}
			.addthisevent_dropdown .frs
			{position:absolute;top:3px;cursor:pointer;right:10px;padding-left:10px;font-style:normal;
				font-weight:normal;text-align:right;z-index:101;line-height:110%;background:#fff;text-decoration:none;font-size:10px;color:#cacaca;}
			.addthisevent_dropdown .frs:hover 		{color:#6d84b4;}
			.addthisevent 							{visibility:hidden;}
			');
		}
		else if ($theme == 'icon')
		{
			$cs->registerCss('addthisevent', '
.copyx { display:none}
.addthisevent-drop 						{display:inline-block;position:relative;z-index:999998;}
.addthisevent-selected 					{}
.addthisevent_dropdown 					{position:absolute;z-index:99999;padding:6px 0px 0px 0px;background:#fff;text-align:left;display:none;margin-top:4px;
margin-left:-1px;border-top:1px solid #c8c8c8;border-right:1px solid #bebebe;border-bottom:1px solid #a8a8a8;border-left:1px solid #bebebe;-moz-border-radius:2px;-webkit-border-radius:2px;
-webkit-box-shadow:1px 3px 6px rgba(0,0,0,0.15);-moz-box-shadow:1px 3px 6px rgba(0,0,0,0.15);box-shadow:1px 3px 6px rgba(0,0,0,0.15);}
.addthisevent_dropdown span 			{width:175px;display:block;line-height:110%;background:#fff;text-decoration:none;font-size:12px;color:#6d84b4;padding:8px 10px 9px 15px;}
.addthisevent_dropdown span:hover 		{background:#f4f4f4;color:#6d84b4;text-decoration:none;font-size:12px;}
.addthisevent span 						{display:none!important;}
.addthisevent-drop ._url,.addthisevent-drop ._start,.addthisevent-drop ._end,.addthisevent-drop ._summary,.addthisevent-drop ._description,.addthisevent-drop ._location,.addthisevent-drop ._organizer,
.addthisevent-drop ._organizer_email,.addthisevent-drop ._facebook_event,.addthisevent-drop ._all_day_event {display:none!important;}
.addthisevent_dropdown .brx 			{height:1px;overflow:hidden;background:#e0e0e0;position:absolute;z-index:100;left:10px;top:9px;}
.addthisevent_dropdown .frs 			{position:absolute;top:5px;cursor:pointer;right:10px;padding-left:10px;font-style:normal;font-weight:normal;text-align:right;z-index:101;line-height:110%;background:#fff;
text-decoration:none;font-size:9px;color:#cacaca;}
.addthisevent_dropdown .frs:hover 		{color:#6d84b4;}
.addthisevent 							{visibility:hidden;}



			');
		}
	}
}
