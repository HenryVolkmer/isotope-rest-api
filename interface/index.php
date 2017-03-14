<?php
/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2017 terminal42 gmbh & Isotope eCommerce Workgroup
 * 
 * RESTful API for Isotope eCommerce
 * 
 * Copyright (C) 2017 Henry Lamorski
 * 
 * @author Henry Lamorski <henry.lamorski@mailbox.org>
 *
 * @link       https://isotopeecommerce.org
 * @link       https://github.com/HenryLamorski/isotope-rest-api
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */
 
require_once(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . 
    DIRECTORY_SEPARATOR . 'system' .DIRECTORY_SEPARATOR . 
    'config' . DIRECTORY_SEPARATOR . 'localconfig.php'
);

$yii=dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . 
DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'vendor' . 
DIRECTORY_SEPARATOR . 'yiisoft' . DIRECTORY_SEPARATOR . 'yii' . 
DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'yii.php';

$config = array(
    'basePath'=>dirname(__FILE__). DIRECTORY_SEPARATOR . '..' . 
        DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 
        'modules' . DIRECTORY_SEPARATOR . 'isotope_rest_api',
    'defaultController'=>'rest', 
    'name'=>'RESTful API for Isotope eCommerce',
    'preload'=>array('log'),
    'import'=>array(
        'application.models.*',
        'application.components.*',
    ),
    'components'=>array(
        // logging
        'log'=>array(
            'class'=>'CLogRouter', 
            'routes'=>array( 
                array(
                    'class'=>'CFileLogRoute', 
                    'levels'=>'error, warning, info', 
                ),
            ),
        ),
        
        // database
        'db'=>array(
            'connectionString'=>'mysql:host='.$GLOBALS['TL_CONFIG']['dbHost'].';dbname=' . $GLOBALS['TL_CONFIG']['dbDatabase'],
            'username'=>$GLOBALS['TL_CONFIG']['dbUser'],
            'password'=>$GLOBALS['TL_CONFIG']['dbPass'],
            'charset'=>$GLOBALS['TL_CONFIG']['dbCharset'],
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName' => false,
            'rules'=>array(
                'product/<id:\d+>'=>'product/index',
            ),
        ),
    ),
);

require_once($yii);
Yii::createWebApplication($config)->run();
