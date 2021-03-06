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
 
class ProductVariants extends Product
{
    private $owner;

    /** safe for massive assignment **/
    public function safeAttr()
    {
        $arr = parent::safeAttr();
        $newStr = $arr[0] . ',name,fallback,description';
        return array($newStr);
    }


    public function rules()
    {
        
        $arrRules = parent::rules();
        
        $objType = $this->getOwner()->getType();
        
        return array(
            $arrRules[0], // sku
            $arrRules[1], // pricetier
            array(
                implode(
                    ",",
                    array_map(
                        function($objAttr) use ($objType) {  
                            if(
                                true === $objType->isVariant($objAttr->field_name)
                                && true === $objAttr->hasOptions()
                            ) {
                                return $objAttr->field_name;  
                            }
                        },
                        $objType->getObjAttributes()
                    )
                ),
                'checkAttributeOption'                
            ),
            
            array(
                implode(
                    ",",
                    array_merge(
                        array_map(
                            function($objAttr) use ($objType) {  
                                if(
                                    true === $objType->isVariant($objAttr->field_name)
                                    && true !== $objAttr->hasOptions()
                                ) {
                                    return $objAttr->field_name;  
                                }
                            },
                            $objType->getObjAttributes()
                        ),
                        $this->safeAttr()
                    )
                ),
                'safe'
            ),
            
            
        );        
    }

    public function relations()
    {
        /** meta product **/
        return array(
            'owner' => array(self::BELONGS_TO, 'Product', array('pid'=>'id')),
        );
        
    }
    
    public function generateAlias()
    {
        return;
    }
    
    public function defaultScope()
	{
        return array();
	}
    
    public function setOwner(Product $objProduct)
    {
        $this->owner = $objProduct;
    }
        
    public function getOwner()
    {
        if (!$this->owner) {
           $this->owner = $this->getRelated('owner'); 
        } 
        
        return $this->owner;
    }

    public function getType()
    {
        return $this->getOwner()->getType();
    }
        
    
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
