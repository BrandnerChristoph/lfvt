<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherWishlist */

$this->title = Yii::t('app', 'Create Teacher Wishlist');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Wishlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-wishlist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
