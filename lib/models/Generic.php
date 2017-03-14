<?php
class Generic extends CActiveRecord
{

    public function beforeSave()
    {
        $this->tstamp = time();
        return parent::beforeSave();
    }

    public static function model($className=__CLASS__)
	{
        return parent::model($className);
	}
}
