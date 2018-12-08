<?php
namespace humhub\modules\admin\models;

use yii\base\Model;


class CsvForm extends Model{
    public $csv_file;
   
public function rules(){
        return [
            [['csv_file'],'required'],
            [['csv_file'],'file','extensions'=>'csv','maxSize'=>1024 * 1024 * 5],
        ];
    }
    public function attributeLabels(){
        return [
            'csv_file'=>'CSV file',
        ];
    }    
}