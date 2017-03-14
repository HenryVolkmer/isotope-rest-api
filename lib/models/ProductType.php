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
 
class ProductType extends Generic
{
    /** store Attributemodels here **/
    private $_arrObjAttributes;

    public function tableName()
    {
        return 'tl_iso_producttype';
	}
    
    public function afterFind()
    {
        if ($this->attributes) {
            $this->attributes = unserialize($this->attributes);
        } else {
            $this->attributes = array();            
        }
        
        if ($this->variant_attributes) {
            $this->variant_attributes = unserialize($this->variant_attributes);
        } else {
            $this->variant_attributes = array();
        }
            
        return parent::afterFind();
    }
    
    public function getProductAttributes()
    {
        if ($this->isNewRecord) {
            return array();
        }  
        
        return array_merge($this->variant_attributes,$this->attributes);
    }
    
    public function getObjAttributes($strName=null)
    {
       
        if (!$this->_arrObjAttributes) {
            
            $c = new CDbCriteria;
            $c->addInCondition('field_name',array_keys($this->getProductAttributes()));
            $arrZeroBasedIndex = Attribute::model()->findAll($c);
            
            foreach($arrZeroBasedIndex as $objAttribute) {
                $this->_arrObjAttributes[$objAttribute->field_name] = $objAttribute;
            }            
        }
      
        if($strName) {
            return $this->_arrObjAttributes[$strname];
        } else {
            return $this->_arrObjAttributes;
        }
    }
    
    
    public function getObjAttribute($strFieldName)
    {
        $arrObj = $this->getObjAttributes();
        
        if(isset($arrObj[$strFieldName]))
            return $arrObj[$strFieldName];
        else
            return null;
    }
    
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
