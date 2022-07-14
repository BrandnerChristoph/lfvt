<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "teacher_wishlist".
 *
 * @property string $id
 * @property int $teacher_id
 * @property string|null $period
 * @property string|null $info
 * @property int|null $updated_at
 * @property int|null $created_at
 *
 * @property Teacher $teacher
 */
class TeacherWishlist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_wishlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'teacher_id'], 'required'],
            [['teacher_id', 'updated_at', 'created_at'], 'integer'],
            [['info'], 'string'],
            [['id'], 'string', 'max' => 50],
            [['period'], 'string', 'max' => 10],            
            [['hours_min', 'hours_max'], 'number', 'min' => 0, 'max' => 100],
            [['id'], 'unique'],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher_id' => 'id']],
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
            'period' => Yii::t('app', 'Period'),
            'info' => Yii::t('app', 'Info'),
            'hours_min' => Yii::t('app', 'Minimumstunden'),
            'hours_max' => Yii::t('app', 'Maximumstunden'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery|TeacherQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['id' => 'teacher_id']);
    }

    /**
     * {@inheritdoc}
     * @return TeacherWishlistQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TeacherWishlistQuery(get_called_class());
    }
}
