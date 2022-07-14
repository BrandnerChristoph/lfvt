<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TeacherWishlist]].
 *
 * @see TeacherWishlist
 */
class TeacherWishlistQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TeacherWishlist[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TeacherWishlist|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
