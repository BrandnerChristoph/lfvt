<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ClassSubject]].
 *
 * @see ClassSubject
 */
class ClassSubjectQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClassSubject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClassSubject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
