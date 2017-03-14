<?php
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'system' .DIRECTORY_SEPARATOR. 'config' .DIRECTORY_SEPARATOR. 'localconfig.php');
$yii=dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'yiisoft' . DIRECTORY_SEPARATOR . 'yii' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'yii.php';

$config = array(
    'basePath'=>dirname(__FILE__). DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'isotope_rest_api' . DIRECTORY_SEPARATOR . 'lib',
    'defaultController'=>'rest', 
    'name'=>'Isotope eCommerce REST-Api',
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

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 'On');
Yii::createWebApplication($config)->run();
