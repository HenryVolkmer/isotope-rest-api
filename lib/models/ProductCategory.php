<?php
class ProductCategory extends Generic
{

    public function tableName()
    {
        return 'tl_iso_product_category';
	}

    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
    
}
