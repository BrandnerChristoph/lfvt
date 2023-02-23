<?php

namespace app\models;

use mdm\admin\models\searchs\User as SearchsUser;
use mdm\admin\models\User as ModelsUser;
use Yii;
use yii\helpers\ArrayHelper;
use mdm\admin\models\User;

/**
 * This is the model class for table "teacher".
 *

 */
class TeacherExtended extends Teacher
{
    public $sort_order;
        
        
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
    public static function getAllTeachersArrayMap($showInactive = false){

/*
        SELECT distinct(teacher.id), `NAME`, `firstname`, `titel`, `teacher_fav`.`sort_helper` AS `sort_order` 
        FROM `teacher` LEFT JOIN `teacher_fav` ON `teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "BN" 
        ORDER BY `sort_order` DESC, `teacher`.`id`

*/

//        $objUser = ModelsUser::find(Yii::$app->user->id);

        $objUser = User::findOne(Yii::$app->user->id);
        
        if($showInactive){
            $arrReturn =  ArrayHelper::map(TeacherExtended::find()
                                        ->select('distinct(teacher.id) as id, name, firstname, initial as initial,  titel,  teacher_fav.sort_helper AS sort_order')
                                        //->select(['CASE WHEN teacher_fav.id IS NOT null THEN "Favoriten" ELSE "Lehrer" END AS sort_order'])
                                        //->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "'.$objUser->username.'"')
                                        ->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "'.strtoupper($objUser->username).'"')
                                        ->orderBy('sort_order desc, teacher.id asc')
                                        ->all(), 'id', 
                                    function($model, $defaultValue){
                                        $additionalInfo = "";
                                        
                                        if ($model->sort_order > 0)
                                            $additionalInfo = " *";
                                        
                                        return trim($model->name . " " . $model->firstname . " (" . $model->initial . ") " . $additionalInfo );
                                    }
                                );
            } else {
                $arrReturn =  ArrayHelper::map(TeacherExtended::find()
                                        ->select('distinct(teacher.id) as id, name, firstname, initial as initial,  titel,  teacher_fav.sort_helper AS sort_order')
                                        //->select(['CASE WHEN teacher_fav.id IS NOT null THEN "Favoriten" ELSE "Lehrer" END AS sort_order'])
                                        //->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "'.$objUser->username.'"')
                                        ->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "'.strtoupper($objUser->username).'"')
                                        ->andFilterWhere(['is_active' => 1])
                                        ->orderBy('sort_order desc, teacher.id asc')
                                        ->all(), 'id', 
                                    function($model, $defaultValue){
                                        $additionalInfo = "";
                                        
                                        if ($model->sort_order > 0)
                                            $additionalInfo = " *";
                                        
                                        return trim($model->name . " " . $model->firstname . " (" . $model->initial . ") " . $additionalInfo );
                                    }
                                );
            }

        //print_r($arrReturn);
        return $arrReturn;

        return ArrayHelper::map(Teacher::find()
                                                ->orderBy('name asc, firstname asc')
                                                ->all(), 'id', 
                                            function($model, $defaultValue){
                                                return $model->name . " " . $model->firstname . " (" . $model->initial . ")";
                                            }
                                );
    }
}
