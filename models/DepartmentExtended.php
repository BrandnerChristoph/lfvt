<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the extended model class for table "department".
 *
 * @property SchoolClass[] $schoolClasses
 */
class DepartmentExtended extends Department
{    
    /**
     * getNumberOfClasses in a special department
     *
     * @param  string $department
     * @param  string $year
     * @return int
     */
    public static function getNumberOfClasses($department, $year = null){    
        return SchoolClass::find()->andFilterWhere(["department" => $department])->andFilterWhere(["period" => is_null($year) ? date("Y") : $year])->All();
    }
    
    /**
     * getAllDepartmentsArrayMap
     *
     * @return array
     */
    public static function getAllDepartmentsArrayMap(){
        return ArrayHelper::map(Department::find()
                                                ->orderBy('name asc')
                                                ->all(), 'id', 
                                            function($model, $defaultValue){
                                                return $model->name;
                                            }
                                );
    }
}
