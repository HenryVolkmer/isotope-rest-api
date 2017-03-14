<?php
class ProductController extends Controller
{
    /** retrieve a single product or whole product collection **/
    public function get()
	{   
        $arrData=array();
        $c = new CDbCriteria;
        $c->compare('id',Yii::app()->request->getParam('id',null));
        $arrProducts = Product::model()->findAll($c);
        
        if ($arrProducts) {
            foreach($arrProducts as $objProduct) {
                
                /*
                if(!$objProduct->getType()) {
                    Yii::log(print_r($objProduct,true),"info",CHtml::modelName($this));
                    continue;
                }
                */
                
                $arrData[] = array_merge(
                    $objProduct->getCompiledAttributes(),
                    array(
                        'variants' =>
                        array_map(function($obj){ return $obj->getCompiledAttributes();}, $objProduct->getVariants())
                    ),
                    array(
                        'pricetier' => $objProduct->getCompiledPrice(),
                    )
                );
            }
        }
        
        header('Content-Type: application/json');        
        echo CJSON::encode($arrData);
        Yii::app()->end(); 
    }
    
    public function post($arrPayload=array())
    {
        if (!$arrPayload) {
            Yii::app()->end(); 
        }

        $arrErrors = array();

        foreach($arrPayload as $key => $arrData)
        {
            if(!$arrData || empty($arrData['type'])) {
                continue;
            }

            if (isset($arrData['id']) && $arrData['id']) {
                $objProduct = Product::model()->findByPk($arrData['id']);
                if (!$objProduct) {
                    $objProduct = new Product;
                }
            } else {
                $objProduct = new Product;
            }

            $objProduct->setType($arrData['type']);

       
            $objProduct->attributes = $arrData;
            
            Yii::log(print_r($objProduct->rules(),true),"info",CHtml::modelName($this));
            
            if (!$objProduct->save()) {
                $arrErrors[] = array_merge(
                    array('line'=>$key),
                    $objProduct->getErrors()
                );
            } else {
                echo "<pre>"; print_r("saved!"); echo "</pre>"; 
            }
        }
        
        if ($arrErrors) {
            header('Content-Type: application/json');        
            echo CJSON::encode($arrErrors);
            Yii::app()->end();
        }
       
    }   
    
}
