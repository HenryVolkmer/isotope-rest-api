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
 
class Controller extends CController
{
    public function actionIndex()
    {
        $strRequestType = Yii::app()->request->getRequestType();
        /** content-type is json: json is now a php-array **/
        $arrPayload = Yii::app()->request->getRestParams();
        
        switch($strRequestType) {
                       
            case 'POST':
            case 'PUT':
                $this->post($arrPayload);
                break;
            case 'DELETE':
                $this->delete($arrPayload);
                break;
            default:
                $this->get();
            
        }
    }
}
