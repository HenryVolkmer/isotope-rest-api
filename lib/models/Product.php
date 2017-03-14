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

class Product extends Generic
{
    private $objType = null;
    
    /** store Price Object **/
    private $objPrice = null;
    
    /** store json-input-array **/
    public $pricetier;
    
    /** store variants object here **/
    private $_arrChilds = array();
    
    /** store variants array (json input) **/
    public $variants;
        
    public function sysCols()
    {
        return array('id','gid','pid','inherit','fallback','tstamp','language','type','orderPages','images','dateAdded');
    }
    
    /** safe for massive assignment **/
    public function safeAttr()
    {
        return array('images,published,meta_title,meta_description,meta_keywords,shipping_weight,shipping_exempt,protected,protected,groups,guests,cssID,start,stop,orderPages');
    }

    public function tableName()
    {
        return 'tl_iso_product';
	}
    
    public function rules()
    {
        return array(
            array('type,pid','numerical','integerOnly'=>true),
            array('type','exists','className'=>'ProductType','attributeName'=>'id','allowEmpty'=>false),
            array('name,sku,description,pricetier','required'),
            array('sku','unique'),
            array('pricetier','checkPricetier'),
            array('variants','checkVariants'),
    
            /** 
             * validate all Attributes with options 
             * (select, checkboxes ...) against 
             * self::checkAttributeOption()
             */
            array(
                implode(
                    ",",
                    array_map(
                        function($objAttr) {  
                            if(
                                true === $objAttr->hasOptions() 
                                && true !== $objAttr->variant_option) {
                                    return $objAttr->field_name;  
                                }
                        },
                        $this->getType()->getObjAttributes()
                    )
                ),
                'checkAttributeOption'                
            ),
            
            /** 
             * declare all non-option attributes as safe 
             * for massive assignment 
             */
            array(
                implode(
                    ",",
                    array_merge(
                        array_map(
                            function($objAttr) {
                                if(true !== $objAttr->hasOptions()
                                && true !== $objAttr->variant_option) { 
                                    return $objAttr->field_name;  
                                }
                            },
                            $this->getType()->getObjAttributes()
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
        /** product_type **/
        return array(
            'type' => array(self::BELONGS_TO, 'ProductType', array('type'=>'id')),
        );
        
    }
    
    /** only meta-products or non-variants **/
    public function defaultScope()
	{
        return array('condition'=>"pid='0'");
	}
    
    /** invoked in afterSave() **/
    public function saveOrderPages()
    {
        /** beforeSave() has allready serialized, so we unserialize again **/
        $arrOrderPages = unserialize($this->orderPages);

        if(!is_array($arrOrderPages)) {
            return;
        }

        if(!$this->isNewRecord && $this->id) {
            $c = new CDbCriteria;
            $c->compare('pid',$this->id);
            ProductCategory::model()->deleteAll($c);
        }

        foreach($arrOrderPages as $pageId) {
            $objCat = new ProductCategory;
            $objCat->pid = $this->id;
            $objCat->page_id = $pageId;
            $objCat->save();
        }
    }
    
    /**
     * validate each item in given 'variants'-Key
     * declared in $this->rules() and invoked by 
     * $this->validate() and $this->save()
     */
    public function checkVariants($attribute,$params=array())
    {
        
        if(!$this->getType()->variants) {
            return;
        }
        
        if(!is_array($this->variants)) {
            $this->addError($attribute,$attribute. ' must be type array!');
            return;
        }

        foreach($this->variants as $key => $arrVariant) {
            $objVariant = new ProductVariants;
            $objVariant->setOwner($this);
            $objVariant->attributes = $arrVariant;
            if(!$objVariant->validate()) {
                $this->addError($attribute,"index ".$key.": ".$objVariant->getError());
            }
        }
        
    }
    
    /** check given Attributeoptions for existence **/
    public function checkAttributeOption($attribute,$params)
    {
        $objAttr = $this->getType()->getObjAttribute($attribute);
     
        /** todo: if attribute mandatory, add error **/
        if(!$this->{$attribute})
            return;
        
        if(null === $objAttr) {
            $this->addError($attribute,'Attribute '.$attribute.' not found');
            return;
        }

        if(!$objAttr->hasOptions()) {
            return;
        }

        $objAttrOption = $objAttr->getOptions();
        
        if(!$objAttrOption) {
            $this->addError($attribute,'Attribute <'.$attribute.'> has no options!');
            return;
        }

        $arrAvailOptions = CHtml::listData($objAttrOption,"id","label");
        $arrAttributeValToSave = array();

        if(!is_array($this->{$attribute}))
            $this->{$attribute} = array($this->{$attribute});

        

        foreach($this->{$attribute} as $strVal) {
        
            if(!in_array($strVal,$arrAvailOptions)) {
                $this->addError($attribute,'given Option <'.$strVal.'> is not in tl_iso_attribute_option or foreigntable available.');
                return;
            }
            
            $optionId = array_search($strVal,$arrAvailOptions);
            $arrAttributeValToSave[$optionId] = $arrAvailOptions[$optionId];
        }
        
        $this->{$attribute} = implode(",",array_keys($arrAttributeValToSave));
        return;
    }
    
    public function checkPricetier($attribute,$params) 
    {
        $objPrice = new Price;
        $objPrice->attributes = $this->pricetier;
        
        if (!$objPrice->validate()) {        
            $this->addError($attribute,$objPrice->getErrors());
            return;
        }
        return;
    }
    
  
    public function beforeSave()
    {
        $this->orderPages = serialize($this->orderPages);
        $this->processImages();
        $this->images  = serialize($this->images);
        return parent::beforeSave();
    }

    /**
     * check existence for each $this->images and move the file
     * to isotope image folder structure
     */
    public function processImages()
    {
        if(!$this->images || !is_array($this->images)) {
            return;
        }
        $arrFiles=array();
        foreach($this->images as $arrImgData) {
            
            if(!isset($arrImgData['src']) || !isset($arrImgData['filename'])) {
                continue;
            }
            
            $fileSrc = $arrImgData['src'] . DIRECTORY_SEPARATOR . $arrImgData['filename'];
            if(!file_exists($fileSrc)) {
                continue;
            }
                        
            $fileDest = dirname(__FILE__, 5). DIRECTORY_SEPARATOR . 'isotope' . DIRECTORY_SEPARATOR . strtolower(substr($arrImgData['filename'],0,1));
            if(!is_dir($fileDest)) {
                mkdir($fileDest);
            }
            
            if(copy($fileSrc, $fileDest . DIRECTORY_SEPARATOR . $arrImgData['filename'])) {
                $arrFiles[] = array(
                    'src'=>$arrImgData['filename']
                );
            }
        }
        
        $this->images = $arrFiles;
        
    }

    public function generateAlias()
    {
        if (!$this->alias) {
            $this->alias = Helper::standardize($this->name);
        }        
        
        $c=new CDbCriteria;
        $c->compare('alias',$this->alias);
        
        if (Product::model()->count($c) > 1) {
            $this->alias .= '-' . $this->id; 
        }
        
    }
    
    public function afterFind()
    {
        $this->orderPages = unserialize($this->orderPages);
        $this->images = unserialize($this->images);
        /** for GET-REST-call: retrive price-object with tier-stack **/
        $this->setObjPrice();
       
        return parent::afterFind();
    }
    
    public function afterSave()
    {
        /** OrderPages aka Product Categorys **/
        $this->saveOrderPages();
        
        $this->generateAlias();
        /** 
         * set isNewRecord to false, otherwise saveAttributes() wont work
         */
        if($this->isNewRecord) {
            $this->setIsNewRecord(false);
            $setNewRecord = true;
        } else {
            $setNewRecord = false;
        }
        
        $this->saveAttributes(array('alias'=>$this->alias));
        
        if(true === $setNewRecord) {
            $this->setIsNewRecord(true);
        }
        
        /** price **/
        $objPrice = $this->getObjPrice();
        $objPrice->setOwner($this);
        $objPrice->attributes = $this->pricetier;
        $objPrice->save();
        
        /** variants **/
        if($this->getType()->variants && $this->variants) {
            foreach($this->variants as $arrVariant) {
                if(isset($arrVariant['id'])) {
                    $objVariant = ProductVariants::model()->findByPk($arrVariant['id']);
                    if(!$objVariant) {
                        $objVariant = new ProductVariants();
                    }
                } else {
                    $objVariant = new ProductVariants();               
                }
                $objVariant->setOwner($this);
                $objVariant->pid = $this->id;
                $objVariant->attributes = $arrVariant;
                $objVariant->save();
            }
        }

        return parent::afterSave();
    }
    
    
    public function getVariants()
    {
        if(!$this->_arrChilds) {
            $c = new CDbCriteria;
            $c->compare('t.pid',$this->getPrimaryKey());
            $this->_arrChilds = ProductVariants::model()->findAll($c);
        }
        return $this->_arrChilds;
    }
    
        
    public function setType($intTypeId)
    {
        $this->objType = ProductType::model()->findByPk($intTypeId);
    }
    
    public function getType()
    {
        if (!$this->objType) {
           $this->objType = $this->getRelated('type'); 
        } 
        
        return $this->objType;
    }
    
    public function setObjPrice()
    {
        $c = new CDbCriteria;
        $c->compare("pid",$this->id);
        $this->objPrice = Price::model()->find($c);
        if($this->isNewRecord || !$this->objPrice) {
            $this->objPrice = new Price;
        }
    }
    
    public function getObjPrice()
    {
        if(!$this->objPrice) {
            $this->setObjPrice();
        }
        return $this->objPrice;
    }

    
    /**
     * return:
     * option attributes: show value from foreign table
     * non-option-attributes: show db-value
     * attribute not in ProductType: return null
     */
    public function getCompiledAttribute($name)
    {
        $strAttr = $this->getAttribute($name);
      
        /** attribute not in type? return null **/
        if (
                (
                    !array_key_exists(
                    $name,$this->getType()->getProductAttributes())
                    || !$this->getType()->getProductAttributes()[$name]['enabled']
                )
                && !in_array($name,$this->sysCols())
            ) 
            {
            return null;
        }

        /** load the attribute model **/
        $objAttr = Attribute::model()->findByAttributes(
            array('name'=>$name)
        );
        
                
        /** attribute has no options: return value untouched **/
        if (!$objAttr || !$objAttr->hasOptions()) {
            return $this->{$name};
        } elseif($objAttr->hasOptions()) {
            /** attribute has options: get values from foreign tables **/
            $objAttrOption = $objAttr->getOptions($this->{$name});
    
            if(!$objAttrOption)
                return null;
            
            /** get a array from array-objects **/
            return CHtml::listData($objAttrOption,"id","label");
        }           
    }
    
    public function getCompiledAttributes($names=true)
    {
        $attributes = $this->getAttributes($names);
        $attr = array();
       
        foreach ($attributes as $attr_code => $attr_val) {
            $attr[$attr_code] = $this->getCompiledAttribute($attr_code);
        }
        
        return $attr;
    }
    
    public function getCompiledPrice()
    {
        return array_merge(
            $this->getObjPrice()->getAttributes(),
            array(
                'tiers' => $this->getObjPrice()->getCompiledTiers()
            )
        );        
    }
    
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
