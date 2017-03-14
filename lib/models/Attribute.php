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
 
class Attribute extends Generic
{
    /** array contain AttributeOptions-Objects from tl_iso_attribute_option **/
    public $options;

    public function tableName()
    {
        return 'tl_iso_attribute';
	}

    public function relations()
    {
        /** attribute options **/
        return array(
            'options' => array(self::HAS_MANY, 'AttributeOption', array('pid'=>'id')),
        );
        
    }
    
    public function hasOptions()
    {
        if ($this->optionsSource) {
            return true;
        } else {
            return null;
        }           
    }
    
    public function getOptions($strIds=null)
    {
        if($this->optionsSource === 'table') {
            if (null === $strIds && !$this->options) {
                $this->options = $this->getRelated('options');       
            } 
            
            if ($strIds) {
                $c = new CDbCriteria;
                $c->addInCondition("id",explode(",",$strIds));
                $this->options = AttributeOption::model()->findAll($c);                
            }
           
            return $this->options;            
        }
        
        if ($this->optionsSource === 'foreignKey') {
            
            $arrSplit = explode(".",$this->foreignKey);
            $tbl = $arrSplit[0];
            $field = 'id,'.$arrSplit[1].' as label';
            
            $objOptions = Yii::app()->db->createCommand()
                ->select($field)
                ->from($tbl);
            
            if (null === $strIds && !$this->options) {
                
                $this->options = $objOptions->queryAll();
                                     
            } 
            
            if ($strIds) {
                $this->options = $objOptions->where(
                    array('in','id',explode(",",$strIds))
                )->queryAll();
            }
           
            return $this->options;
            
        }               
        
    }
    
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
