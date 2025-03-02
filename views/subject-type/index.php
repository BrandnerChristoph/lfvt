<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'SubjectTypes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">

    <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'Create SubjectType'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'filename' => 'Faecher',
            'columnSelectorOptions' => [
                'icon' => '<i class="fa fa-list"></i>',
            ],
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-outline-secondary btn-default'
            ],
            'columns' => [
                'id',
                'name',
                'info',
                'year_update',
                'sortorder',
                'updated_at:datetime',
                'created_at:datetime',
            ]
        ]) ?>
    <p/>


    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'info',
            'year_update',
            'sortorder',
            [
                'attribute' => 'color',
                'format' => 'raw',
                'value' => function ($model){
                    if(!empty($model->color))
                        return "<b><span style='color: ".$model->color."'>" . $model->color . "</span></b>";
                }
            ],
            'updated_at:datetime',
            'created_at:datetime',

            //['class' => 'yii\grid\ActionColumn'],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {delete}'],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
