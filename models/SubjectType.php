<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject".
 *
 * @property string $id
 * @property string $name
 * @property string $info
 * @property string $year_update
 * @property string $sortorder
 * @property string $color
 * @property int $updated_at
 * @property int $created_at
 *
 */
class SubjectType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'updated_at', 'created_at'], 'required'],
            [['updated_at', 'created_at', 'sortorder'], 'integer'],
            [['id'], 'string', 'max' => 20],
            [['name', 'info'], 'string', 'max' => 225],
            [['year_update'], 'string', 'max' => 5],
            [['color'], 'string', 'max' => 10],
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
            'info' => Yii::t('app', 'Info'),
            'sortorder' => Yii::t('app', 'sortorder'),
            'color' => Yii::t('app', 'color'),
            'year_update' => Yii::t('app', 'Jahreswechsel'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    
    /**
     * provides list of subjects
     * 
     * @return void
     */
    public static function getArrayHelperList(){
        return ArrayHelper::map(SubjectType::find()
                                            ->orderby("name asc")
                                            ->all(), 'id', 
                                function($model, $defaultValue){
                                    return $model->name;
                                });

    }
}
