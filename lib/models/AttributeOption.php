<?php
class AttributeOption extends Generic
{
    public function tableName()
    {
        return 'tl_iso_attribute_option';
	}

   
    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
