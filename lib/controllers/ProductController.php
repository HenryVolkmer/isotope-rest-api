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
 
class ProductController extends Controller
{
    /** retrieve a single product or whole product collection **/
    public function get()
	{   
        $arrData=array();
        $c = new CDbCriteria;
        $c->compare('id',Yii::app()->request->getParam('id',null));
        $arrProducts = Product::model()->findAll($c);
        
        if ($arrProducts) {
            foreach($arrProducts as $objProduct) {
                
                $arrData[] = array_merge(
                    $objProduct->getCompiledAttributes(),
                    array(
                        'variants' =>
                        array_map(function($obj){ return $obj->getCompiledAttributes();}, $objProduct->getVariants())
                    ),
                    array(
                        'pricetier' => $objProduct->getCompiledPrice(),
                    )
                );
            }
        }
        
        header('Content-Type: application/json');        
        echo CJSON::encode($arrData);
        Yii::app()->end(); 
    }
    
    public function post($arrPayload=array())
    {
        if (!$arrPayload) {
            header("HTTP/1.0 400 Bad Request");
            echo CJSON::encode(
                array(
                    'error' => 'No data submitted. Nothing to do ...'
                )
            );
            Yii::app()->end();
        }

        $arrErrors = array();

        foreach($arrPayload as $key => $arrData)
        {
            if(!$arrData || empty($arrData['type'])) {
                continue;
            }

            if (isset($arrData['id']) && $arrData['id']) {
                $objProduct = Product::model()->findByPk($arrData['id']);
                if (!$objProduct) {
                    $objProduct = new Product;
                }
            } elseif(isset($arrData['sku']) && $arrData['sku']) {
				$objProduct = Product::model()->findBySku($arrData['sku'])->find();
                if (!$objProduct) {
                    $objProduct = new Product;
                } 
            } else {
                $objProduct = new Product;
            }

            $objProduct->setType($arrData['type']);

      
            $objProduct->attributes = $arrData;
            
                        
            if (!$objProduct->save()) {
				Yii::log(print_r($objProduct->getErrors() ,true),'info',CHtml::modelName($this));
                $arrErrors[] = array_merge(
                    array('line'=>$key),
                    $objProduct->getErrors()
                );
            }
            
        }
        
        if ($arrErrors) {
            header("HTTP/1.0 400 Bad Request");        
            echo CJSON::encode($arrErrors);
            Yii::app()->end();
        }
       
    }   
    
}
