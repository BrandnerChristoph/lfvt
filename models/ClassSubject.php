<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "class_subject".
 *
 * @property string $id
 * @property string $class
 * @property string $subject
 * @property string $group Lehrverpflichtungsgruppe
 * @property float $value
 * @property float $hours
 * @property string $teacher
 * @property string $classroom
 * @property int $updated_at
 * @property int $created_at
 *
 * @property SchoolClass $class0
 * @property Subject $subject0
 * @property Teacher $teacher0
 */
class ClassSubject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'class_subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'class', 'subject', 'value', 'updated_at', 'created_at'], 'required'],
            [['value', 'hours'], 'number'],
            [['value'], 'number', 'min' => 0, 'max' => 300],
            [['updated_at', 'created_at'], 'integer'],
            [['teacher', 'id', 'class', 'classroom'], 'string', 'max' => 50],
            [['subject', 'group'], 'string', 'max' => 10],
            [['id'], 'unique'],
            [['class'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolClass::className(), 'targetAttribute' => ['class' => 'id']],
            [['subject'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject' => 'id']],
           // [['teacher'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'class' => Yii::t('app', 'Class'),
            'subject' => Yii::t('app', 'Subject'),
            'group' => Yii::t('app', 'Group'),
            'value' => Yii::t('app', 'Value'),
            'hours' => Yii::t('app', 'Hours'),
            'teacher' => Yii::t('app', 'Teacher'),
            'classroom' => Yii::t('app', 'Classroom'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Class0]].
     *
     * @return \yii\db\ActiveQuery|SchoolClassQuery
     */
    public function getClassItem()
    {
        return $this->hasOne(SchoolClass::className(), ['id' => 'class']);
    }

    /**
     * Gets query for [[Subject0]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubjectItem()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject']);
    }

    /**
     * Gets query for [[Teacher0]].
     *
     * @return \yii\db\ActiveQuery|TeacherQuery
     */
    public function getTeacher0()
    {
        return $this->hasOne(Teacher::className(), ['id' => 'teacher']);
    }

    /**
     * {@inheritdoc}
     * @return ClassSubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClassSubjectQuery(get_called_class());
    }
}
