<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "school_class".
 *
 * @property string $id
 * @property string $classname
 * @property string $departmennt
 * @property int $period
 * @property float $annual_value Jahreswert
 * @property int $class_head Klassenvorstand
 * @property int|null $studentsnumber
 * @property string|null $info
 * @property int|null $updated_at
 * @property int|null $created_at
 *
 * @property Teacher $classHead
 * @property ClassSubject[] $classSubjects
 * @property Department $departmennt0
 */
class SchoolClass extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'school_class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'classname', 'department', 'period', 'class_head'], 'required'],
            [['period', 'class_head', 'studentsnumber', 'updated_at', 'created_at'], 'integer'],
            [['annual_value'], 'number'],
            [['id'], 'string', 'max' => 50],
            [['classname'], 'string', 'max' => 10],
            [['department'], 'string', 'max' => 5],
            [['info'], 'string', 'max' => 225],
            [['id'], 'unique'],
            [['department'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department' => 'id']],
            [['class_head'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['class_head' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'classname' => Yii::t('app', 'Classname'),
            'department' => Yii::t('app', 'Department'),
            'period' => Yii::t('app', 'Period'),
            'annual_value' => Yii::t('app', 'Annual Value'),
            'class_head' => Yii::t('app', 'Class Head'),
            'studentsnumber' => Yii::t('app', 'Studentsnumber'),
            'info' => Yii::t('app', 'Info'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[ClassHead]].
     *
     * @return \yii\db\ActiveQuery|TeacherQuery
     */
    public function getClassHead()
    {
        return $this->hasOne(Teacher::className(), ['id' => 'class_head']);
    }

    /**
     * Gets query for [[ClassSubjects]].
     *
     * @return \yii\db\ActiveQuery|ClassSubjectQuery
     */
    public function getClassSubjects()
    {
        return $this->hasMany(ClassSubject::className(), ['class' => 'id']);
    }

    /**
     * Gets query for [[Department0]].
     *
     * @return \yii\db\ActiveQuery|DepartmentQuery
     */
    public function getDepartmennt0()
    {
        return $this->hasOne(Department::className(), ['id' => 'department']);
    }

    /**
     * {@inheritdoc}
     * @return SchoolClassQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SchoolClassQuery(get_called_class());
    }
    
    /**
     * provides list of teachers
     * 
     * @return void
     */
    public static function getArrayHelperList(){
        return ArrayHelper::map(SchoolClass::find()
                                            ->orderby("id asc")
                                            ->all(), 'id', 
                                function($model, $defaultValue){
                                    return $model->id;
                                });

    }
}
