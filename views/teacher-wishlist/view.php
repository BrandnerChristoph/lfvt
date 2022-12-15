<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Teacher;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherWishlist */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Wishlists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="teacher-wishlist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'teacher_id',
            [
                'attribute' => 'teacher_id', 
                'format' => 'raw',
                'value' => function ($model) {
                    $objTeacher = Teacher::findOne($model->teacher_id);
                    if(!is_null($objTeacher)){
                        return "<a href='" . Url::to(['teacher/view', 'id' => $model->teacher_id]) . "'>" . trim($objTeacher->firstname . " " . $objTeacher->name . " (" . $model->teacher_id . ")") . "</a>";
                    }
                    return $model->teacher_id;
                }
            ],
            'period',
            'info:ntext',
            'updated_at:datetime',
            'created_at:datetime',
        ],
    ]) ?>

</div>
