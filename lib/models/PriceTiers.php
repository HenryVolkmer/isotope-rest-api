<?php
class PriceTiers extends Generic
{
    public function tableName()
    {
        return 'tl_iso_product_pricetier';
	}

    public function rules()
    {
        return array(
            array('min,price','numerical','integerOnly'=>false,'allowEmpty'=>false),
        );
    }

    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
