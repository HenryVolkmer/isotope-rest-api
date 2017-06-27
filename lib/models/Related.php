<?php
class Related extends Generic
{
	
	public function tableName()
    {
        return 'tl_iso_related_product';
	}
	
	public function rules()
	{
		array(
			array('category','numerical','integerOnly'=>true),
			array('products', 'safe'),
		);
	}
	
	public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
