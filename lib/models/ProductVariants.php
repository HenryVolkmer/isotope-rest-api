<?php
class ProductVariants extends Product
{
    private $owner;

    public function rules()
    {
        return array(
            array(
                implode(
                    ",",
                    array_map(
                        function($objAttr) {  
                            if(
                                $objAttr->variant_option
                                && true === $objAttr->hasOptions()
                            ) {
                                return $objAttr->field_name;  
                            }
                        },
                        $this->getOwner()->getType()->getObjAttributes()
                    )
                ),
                'checkAttributeOption'                
            ),
            
            array(
                implode(
                    ",",
                    array_merge(
                        array_map(
                            function($objAttr) {  
                                if(
                                    $objAttr->variant_option
                                    && true !== $objAttr->hasOptions()
                                ) {
                                    return $objAttr->field_name;  
                                }
                            },
                            $this->getOwner()->getType()->getObjAttributes()
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
