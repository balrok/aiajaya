<?php

Yii::setPathOfAlias('aiajaya',dirname(dirname(__FILE__)));

return array(
	// autoloading model and component classes
	'import'=>array(
        'aiajaya.extensions.pagesize.PageSize',
        'aiajaya.extensions.EDataTables.*',
        'aiajaya.extensions.EInfotext',
        'aiajaya.components.*',
        'aiajaya.models.*',

        'aiajaya.modules.page.models.*',
        'aiajaya.modules.event.models.*',
        'aiajaya.modules.user.models.*',
        'aiajaya.modules.user.components.*',
        'aiajaya.modules.counter.components.*',
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
	),

    'modules'=>array(
        'user' => array(
            'class' => 'aiajaya.modules.user.UserModule',
        ),
        'event' => array(
            'class' => 'aiajaya.modules.event.EventModule',
        ),
        'page' => array(
            'class' => 'aiajaya.modules.page.PageModule',
        ),
        'counter' => array(
            'class' => 'aiajaya.modules.counter.CounterModule',
        ),
        'subscribe' => array(
            'class' => 'aiajaya.modules.subscribe.SubscribeModule',
        ),
        'gallery' => array(
            'class' => 'aiajaya.modules.gallery.GalleryModule',
			'galleries' => array(
				// url_key is also the key inside bilder/gallery
				array('Name', 'url_key', "example_image"),
			),
        ),
    ), 

	'params'=>array(
		'defaultPageSize'=>15,
		'googleAnalytics'=>array(
			'mainPage'=>array(
				'code'=>'UA-123-1',
				'enabled'=>false,
			),
		),
		'indexKey'=>'startseite',
		'enableGuestBook'=>false,
		'adminEmail'=>'test@test.com',
		'calendarId'=>'asdfgh@group.calendar.google.com',
		'calendarEnabled'=>false,
		'emailExportEnabled'=>false,
		'rubrikField'=>false,
		'breadcrumbs' => true,
		'addthisevent' => 'asdf',
		'google_plus_id'=>"12345",
		'address' => array(
			'street' => 'Abcstraße 17',
			'zip' => '01309',
			'city' => 'Dresden',
			'state' => 'Sachsen',
			'country' => 'Deutschland',
			),
		'termine_first' => true,
	),
);
