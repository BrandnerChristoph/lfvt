<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject".
 *
 * @property string $id
 * @property string $name
 * @property int $updated_at
 * @property int $created_at
 *
 * @property ClassSubject[] $classSubjects
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'updated_at', 'created_at'], 'required'],
            [['updated_at', 'created_at', 'sortorder'], 'integer'],
            [['id'], 'string', 'max' => 10],
            [['name', 'type'], 'string', 'max' => 225],
            [['value', 'value_real'], 'number'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'subject_value'),
            'value_real' => Yii::t('app', 'real_value'),
            'sortorder' => Yii::t('app', 'sortorder'),
            'type' => Yii::t('app', 'subject_type'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[ClassSubjects]].
     *
     * @return \yii\db\ActiveQuery|ClassSubjectQuery
     */
    public function getClassSubjects()
    {
        return $this->hasMany(ClassSubject::className(), ['subject' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubjectQuery(get_called_class());
    }
    
    /**
     * provides list of subjects
     * 
     * @return void
     */
    public static function getArrayHelperList(){
        return ArrayHelper::map(Subject::find()
                                            ->orderby("id asc")
                                            ->all(), 'id', 
                                function($model, $defaultValue){
                                    return $model->id . " (".$model->name.")";
                                });

    }
}
