<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Departments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Department'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'head_of_department',
            //'default_color',
            [
                'attribute' => 'default_color',
                'format' => 'raw',
                'value' => function ($model){
                    if(!empty($model->default_color))
                        return "<b><span style='color: ".$model->default_color."'>" . $model->default_color . "</span></b>";
                }
            ],
            [
                'attribute' => 'Schüler',
                'format' => 'raw',
                'value' => function ($model) {                    
                    return $model->getNumberOfPupils(); 
                },
            ],
            //'updated_at:datetime',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
