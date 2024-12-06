<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\Teacher;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TeacherWishlistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teacher Wishlists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-wishlist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Teacher Wishlist'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'teacher_id',
            //'period',
            'info:ntext',
            'hours_min',
            'hours_max',
            [
                'label' => 'aktuelle Stunden',
                'format' => 'raw',
                'value' => function ($model) {
                    
                    $objTeacher = Teacher::findOne($model->teacher_id);
                    if (!is_null($objTeacher))
                        return "<center>".$objTeacher->hours."</center>";
                    
                    return "" ;
                },
            ],
            'updated_at:datetime',
            //'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
