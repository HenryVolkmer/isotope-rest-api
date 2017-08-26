<?php
class Related extends Generic
{
	
	public function tableName()
    {
        return 'tl_iso_related_product';
	}
	
	public function rules()
	{
		return array(
			array('category','numerical','integerOnly'=>true),
			array('products', 'skuToIds'),
		);
	}
	
	/** SKU => id **/
	public function skuToIds()
	{
		
		/** remove empty **/
		$arrSku=array();
		$arr = explode(",",$this->products);
		foreach($arr as $sku) {
			if($sku == '') {
				continue;
			}
			
			$arrSku[] = $sku;
		}
		
		$c = new CDbCriteria;
		$c->addInCondition('sku',$arrSku);
		
		$products = ProductVariants::model()->findAll($c);
		if(!$products) {
			return;
		}
		$arrIds=array();
		foreach($products as $objProduct) {
			$arrIds[] = $objProduct->id;
		}
		$this->products = implode(",",$arrIds);
		return;		
	}	
	
	public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
