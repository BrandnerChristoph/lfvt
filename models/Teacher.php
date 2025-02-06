<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "teacher".
 *
 * @property int $id
 * @property string $initial
 * @property string|null $name
 * @property string|null $firstname
 * @property string|null $email_1
 * @property string|null $email_2
 * @property string|null $phone
 * @property string|null $mobile
 * @property int $is_active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ClassSubject[] $classSubjects
 * @property SchoolClass[] $schoolClasses
 * @property TeacherWishlist[] $teacherWishlists
 */
class Teacher extends \yii\db\ActiveRecord
{
    public $sortOrder; // ist Favorit

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'initial', 'created_at', 'updated_at'], 'required'],
            [['is_active', 'created_at', 'updated_at'], 'integer'],
            [['initial'], 'string', 'max' => 5],
            [['name', 'firstname', 'email_1', 'email_2'], 'string', 'max' => 225],
            [['phone', 'mobile'], 'string', 'max' => 25],
            [['id', 'initial'], 'unique'],
            [['sortOrder'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'initial' => Yii::t('app', 'Initial'),
            'name' => Yii::t('app', 'Name'),
            'firstname' => Yii::t('app', 'Firstname'),
            'email_1' => Yii::t('app', 'Email 1'),
            'email_2' => Yii::t('app', 'Email 2'),
            'phone' => Yii::t('app', 'Phone'),
            'mobile' => Yii::t('app', 'Mobile'),
            'is_active' => Yii::t('app', 'Is Active'),
            'isFav' => Yii::t('app', 'Is Favorite'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[ClassSubjects]].
     *
     * @return \yii\db\ActiveQuery|ClassSubjectQuery
     */
    public function getClassSubjects()
    {
        return $this->hasMany(ClassSubject::className(), ['teacher' => 'id'])->orderBy(['class' => SORT_ASC]);
    }

    /**
     * Gets query for [[SchoolClasses]].
     *
     * @return \yii\db\ActiveQuery|SchoolClassQuery
     */
    public function getSchoolClasses()
    {
        return $this->hasMany(SchoolClass::className(), ['class_head' => 'id']);
    }

    /**
     * Gets query for [[TeacherWishlists]].
     *
     * @return \yii\db\ActiveQuery|TeacherWishlistQuery
     
    public function getTeacherWishlists()
    {
        return $this->hasMany(TeacherWishlist::className(), ['teacher_id' => 'id']);
    }
    */
    public function getTeacherWishlist()
    {
        return $this->hasOne(TeacherWishlist::className(), ['teacher_id' => 'id']);
    }

    
    /**
     * Gets query for [[ClassSubjects]].
     *
     * @return \yii\db\ActiveQuery|ClassSubjectQuery
     */
    public function getFav($type = null)
    {
        if(is_null($type))
            return $this->hasMany(TeacherFav::className(), ['teacher' => $this->id])->orderBy(['value' => SORT_ASC]);
        else
            return $this->hasMany(TeacherFav::className(), ['teacher' => $this->id, 'type' => $type])->orderBy(['value' => SORT_ASC]);
    
    }


    /**
     * return the number of teaching-hours
     */
    public function getHours()
    {
        $listClassSub = ClassSubject::find()->andFilterWhere(['teacher'=> $this->id])->all();

        $sum = 0;
        foreach($listClassSub as $classSubItem)
        {
            $objClass = SchoolClass::findOne($classSubItem->class);
            $valAnnual=1;
            if(!is_null($objClass))
                $valAnnual = $objClass->annual_value;

            //$sum = $sum + ($classSubItem->hours * ($classSubItem->value/100));
            $sum = $sum + (($classSubItem->hours * ($classSubItem->value/100)) * $valAnnual);
        }
        return round($sum, 2);
    }

    /**
     * return the sum of teaching-hours (with value of class)
     */
    public function getTeachingHours()
    {
        $listClassSub = ClassSubject::find()->andFilterWhere(['teacher'=> $this->id])->all();

        $sum = 0;
        foreach($listClassSub as $classSubItem)
        {
            $objSubject = Subject::findOne($classSubItem->subject);
            $objClass  = SchoolClass::findOne($classSubItem->class);

            if(!is_null($objSubject) && !is_null($objClass))
                $sum = $sum + (($classSubItem->hours * ($classSubItem->value/100))*$objSubject->value * $objClass->annual_value);
        }
        return round($sum, 3);
    }

    /**
     * return the sum of teaching-hours (with value of class)
     */
    public function getRealHours()
    {
        $listClassSub = ClassSubject::find()->andFilterWhere(['teacher'=> $this->id])->all();

        $sum = 0;
        foreach($listClassSub as $classSubItem)
        {
            $objSubject = Subject::findOne($classSubItem->subject);
            $objClass  = SchoolClass::findOne($classSubItem->class);

            if(!is_null($objSubject) && !is_null($objClass))
                $sum = $sum + (($classSubItem->hours * ($classSubItem->value/100))*$objSubject->value_real * $objClass->annual_value);
        }
        return round($sum, 3);
    }

    
    /**
     * return an array with 
     * NOT IN USE (seit nur 1 Wunsch gespeichert wird)
     */
    public function getWishHoursAsArray()
    {
        $returnArray = array();

        foreach($this->teacherWishlists as $listItem){
            if(!empty($listItem['hours_min']))
                $returnArray['min'] = $listItem['hours_min'];
            if(!empty($listItem['hours_max'])) //&& ($listItem['hours_min'] != $listItem['hours_max']))
                $returnArray['max'] = $listItem['hours_max'];
        }
        return $returnArray;
    }

    /**
     * {@inheritdoc}
     * @return TeacherQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TeacherQuery(get_called_class());
    }

    public static function findFav()
    {
        return new TeacherQuery(get_called_class());
    }

    /*
    public function afterFind(){
        $this->isFav = 0;

        if($this->id == "BN")
            $this->isFav = 1;

        parent::afterFind();        
    }
    */

    /**
     * getArrayMap
     *
     * @return array
     */
    public static function getAllTeachersArrayMap(){
        return ArrayHelper::map(Teacher::find()
                                                ->orderBy('name asc, firstname asc')
                                                ->all(), 'id', 
                                            function($model, $defaultValue){
                                                return $model->name . " " . $model->firstname . " (" . $model->initial . ")";
                                            }
                                );
    }

    public function fetchHoursByDepartment($department)
    {
        $listClassSub = ClassSubject::find()
                            ->andFilterWhere(['teacher'=> $this->id])
                            ->andFilterWhere(['class' => SchoolClass::find()->select('id')->andFilterWhere(['department' => $department])])
                            ->all();

        $sum = 0;
        foreach($listClassSub as $classSubItem)
        {
            $objClass = SchoolClass::findOne($classSubItem->class);
            $valAnnual=1;
            if(!is_null($objClass))
                $valAnnual = $objClass->annual_value;

            //$sum = $sum + ($classSubItem->hours * ($classSubItem->value/100));
            $sum = $sum + (($classSubItem->hours * ($classSubItem->value/100)) * $valAnnual);
        }
        return round($sum, 2);
    }
    /**
     * return the sum of teaching-hours (with value of class)
     */
    public function fetchTeachingHoursByDepartment($department)
    {
        $listClassSub = ClassSubject::find()
                        ->andFilterWhere(['teacher'=> $this->id])
                        ->andFilterWhere(['class' => SchoolClass::find()->select('id')->andFilterWhere(['department' => $department])])
                        ->all();

        $sum = 0;
        foreach($listClassSub as $classSubItem)
        {
            $objSubject = Subject::findOne($classSubItem->subject);
            $objClass  = SchoolClass::findOne($classSubItem->class);

            if(!is_null($objSubject) && !is_null($objClass))
                $sum = $sum + (($classSubItem->hours * ($classSubItem->value/100))*$objSubject->value * $objClass->annual_value);
        }
        return round($sum, 3);
    }
}
