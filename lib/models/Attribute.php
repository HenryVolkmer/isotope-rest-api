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
    public $objProduct;

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
        if (in_array($this->optionsSource,array('table','product','foreignKey'))) {
            return true;
        } else {
            return null;
        }           
    }
    
    public function setOptions($source='table')
    {
        $this->refresh();
        if($source === 'table') {
            $this->options = $this->getRelated('options');
            return;
        }
        
        if($source === 'product') {
            $c=new CDbCriteria;
            $c->compare("pid",$this->objProduct->id);
            $c->compare("ptable",$this->objProduct->tableName());
            $c->compare("field_name",$this->field_name);
            $this->options = AttributeOption::model()->findAll($c);
            return;
        }
        
        
    }
    
    public function getOptions($strIds=null)
    {
        if(in_array($this->optionsSource, array('table','product'))) {
            if (null === $strIds && !$this->options) {
                $this->setOptions($this->optionsSource);
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
    
    public function addOptions($arrOptions)
    {
        /** we can't create options on a new Instance **/
        if($this->isNewRecord) {
            return null;
        }

        if($this->optionsSource === 'table') {
            foreach($arrOptions as $strLabel)
            {
                $objOption = new AttributeOption;
                $objOption->pid = $this->id;
                $objOption->ptable = $this->tableName();
                $objOption->type = 'option';
                $objOption->published = 1;
                $objOption->label = $strLabel;
                $objOption->save();
            }
        }
        
        if($this->optionsSource === 'product') {
            
            /** dont save without valid pid **/
            if($this->objProduct->isNewRecord) {
                return;
            }
            
            foreach($arrOptions as $strLabel)
            {
                $objOption = new AttributeOption;
                $objOption->pid = $this->objProduct->id;
                $objOption->ptable = $this->objProduct->tableName();
                $objOption->field_name = $this->field_name;
                $objOption->type = 'option';
                $objOption->published = 1;
                $objOption->label = $strLabel;
                $objOption->save();
                
            }
        }
        
        $this->setOptions($this->optionsSource);
        
        
        
        
    }
    
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
