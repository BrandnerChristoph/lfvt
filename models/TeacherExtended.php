<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "teacher".
 *

 */
class TeacherExtended extends Teacher
{
        
        
    /**
     * provides teachername based on id (initial can be provided in return-string)
     *
     * @param  string $id
     * @param  bool $addInitial (default: false)
     * @return string
     */
    public static function getTeacherFullName($id, $addInitial = false){
        $objTeacher = Teacher::findOne($id);
        if(!is_null($objTeacher)){
            $strReturn = $objTeacher->name . " " . $objTeacher->firstname;
            if($addInitial)
                $strReturn .= " (".$objTeacher->initial.")";

            return $strReturn;
        }
        return null;        
    }

    
    /**
     * provides Teacher Initial based on id
     *
     * @param  mixed $id
     * @return string
     */
    public static function getTeacherInitial($id){
        return Teacher::findOne($id)->initial;
    }
    
    /**
     * getAllTeachersArrayMap
     *
     * @return array
     */
    public static function getAllTeachersArrayMap(){

/*
        SELECT teacher.id, NAME, firstname, titel,
            CASE
            WHEN teacher_fav.id IS NOT null THEN 'Favoriten'
            ELSE 'Lehrer'
            END AS sort_order
        FROM teacher 
            LEFT JOIN teacher_fav ON teacher.id = teacher_fav.value
        ORDER BY sort_order, teacher.id;

*/
        return ArrayHelper::map(Teacher::find()
                                                ->orderBy('name asc, firstname asc')
                                                ->all(), 'id', 
                                            function($model, $defaultValue){
                                                return $model->name . " " . $model->firstname . " (" . $model->initial . ")";
                                            }
                                );
    }
}
