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
class TeacherFav extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_fav';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['teacher_id', 'id'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 50],
            [['value'], 'string', 'max' => 225],
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
            'teacher_id' => Yii::t('app', 'Teacher ID'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Wert'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TeacherQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TeacherQuery(get_called_class());
    }
}
