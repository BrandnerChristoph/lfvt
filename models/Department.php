<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "department".
 *
 * @property string $id
 * @property string $name
 * @property string $head_of_department
 * @property string|null $default_color HTML Farbcode
 * @property int $updated_at
 * @property int $created_at
 *
 * @property SchoolClass[] $schoolClasses
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'head_of_department', 'updated_at', 'created_at'], 'required'],
            [['updated_at', 'created_at'], 'integer'],
            [['id'], 'string', 'max' => 5],
            [['name', 'head_of_department'], 'string', 'max' => 225],
            [['default_color'], 'string', 'max' => 10],
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
            'head_of_department' => Yii::t('app', 'Head Of Department'),
            'default_color' => Yii::t('app', 'Default Color'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[SchoolClasses]].
     *
     * @return \yii\db\ActiveQuery|SchoolClassQuery
     */
    public function getSchoolClasses()
    {
        return $this->hasMany(SchoolClass::className(), ['department' => $this->id]);
    }

    /**
     * 
     */
    public function getNumberOfPupils(){  
        return SchoolClass::find()->andFilterWhere(['department' => $this->id])->sum('studentsnumber');  
    }

    /**
     * {@inheritdoc}
     * @return DepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentQuery(get_called_class());
    }
}
