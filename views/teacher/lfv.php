<?php

use Yii;
//use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teachers Lfv');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        $gridCols = [
            'subject',
            'group',
            [
                'attribute' => 'hours',
                'value' => function($model){
                    return Yii::$app->formatter->asDecimal($model->hours, 2);
                }
            ],
            [
                'attribute' => 'value',
                'value' => function($model){
                    return Yii::$app->formatter->asDecimal($model->value, 2);
                }
            ],
            [
                'label' => 'Werteinheit',
                'value' => function($model){
                    return Yii::$app->formatter->asDecimal($model->hours * $model->value, 2);
                },
                'pageSummary'=>true,
            ],
            'classroom',

            ['class' => 'yii\grid\ActionColumn'],
        ];
    ?>

    <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-outline-secondary btn-default'
            ],
            'columns' => $gridCols,
        ]); ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showPageSummary' => true,
        'columns' => $gridCols,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
