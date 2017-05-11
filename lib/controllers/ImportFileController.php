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
 
class ImportFileController extends Controller
{
    /** retrieve all Files in ./isotope/import **/
    public function get()
	{   
        $arrFiles = CFileHelper::findFiles(
            TL_ROOT . DIRECTORY_SEPARATOR . 'isotope' . 
            DIRECTORY_SEPARATOR . 'import'
        );
       
        header('Content-Type: application/json');        
        echo CJSON::encode($arrFiles);
        Yii::app()->end(); 
    }
}
