<?php
class Controller extends CController
{
    public function actionIndex()
    {
        $strRequestType = Yii::app()->request->getRequestType();
        /** content-type is json: json is now a php-array **/
        $arrPayload = Yii::app()->request->getRestParams();
        
        switch($strRequestType) {
                       
            case 'POST':
            case 'PUT':
                $this->post($arrPayload);
                break;
            case 'DELETE':
                $this->delete($arrPayload);
                break;
            default:
                $this->get();
            
        }
    }
}
