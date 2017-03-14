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
 
class Price extends Generic
{
    /** Product model **/
    public $owner;
    /** store object PriceTiers (array) **/
    private $objTiers = null;

    /** array Price tiers **/
    public $tiers=array();

    public function tableName()
    {
        return 'tl_iso_product_price';
	}

    public function rules()
    {
        return array(
            array('tax_class,config_id,member_group,start,stop','numerical','integerOnly'=>true),
            array('tiers','required'),
            array('tiers','checkTiers'),
        );
    }

    public function checkTiers($attribute,$params)
    {
        if(!is_array($this->tiers)) {
            $this->addError($attribute,$attribute . " must be type array!");
            return;
        }
        
        foreach($this->tiers as $arrTier) {
            $objTiers = new PriceTiers();
            $objTiers->attributes = $arrTier;
            
            if(!$objTiers->validate()) {
                $this->addError($attribute,$objTiers->getErrors());
            }
        }
        
        return;
    }
    
    public function beforeSave()
    {
        $this->pid = $this->getOwner()->id;
        return parent::beforeSave();
    }
    
    public function afterFind()
    {
        $this->setObjTiers();
        return parent::afterFind();
    }

    public function getCompiledTiers()
    {
        $arrOut = array();
       
        foreach($this->getObjTiers() as $objTier) {
            $arrOut[] = $objTier->getAttributes();
        }
        return $arrOut;        
    }
    
    public function setObjTiers()
    {
        $c = new CDbCriteria;
        $c->compare("pid",$this->id);
        $this->objTiers = PriceTiers::model()->findAll($c);
        if($this->isNewRecord || !$this->objTiers) {
            $this->objTiers = array(new PriceTiers);
        }
    }
    
    public function getObjTiers()
    {
        if(!$this->objTiers) {
            $this->setObjTiers();
        }
        return $this->objTiers;
    }

    public function afterSave()
    {

        foreach($this->tiers as $arrTier) {
            
            if(isset($arrTier['id'])) {
                $objTiers = PriceTiers::model()->findByPk($arrTier['id']);
                if(!$objTiers) {
                    $objTiers = new PriceTiers();
                }
            } else {
                $objTiers = new PriceTiers();                
            }

            $objTiers->attributes = $arrTier;
            $objTiers->pid = $this->id;
            $objTiers->save();
            
        }
        
        return parent::afterSave();
        
    }

    public function setOwner(Product $objProduct)
    {
        $this->owner = $objProduct;        
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
       
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
