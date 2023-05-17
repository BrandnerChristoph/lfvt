<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model app\models\SchoolClass */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'School Classes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="school-class-view">

    <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?= ButtonDropdown::widget([
            'label' => 'Aktion',
            'class' => 'btn btn-default border',
            'dropdown' => [
                'items' => [
                    [
                        'label' => 'Stundentafel', 
                        'url' => Yii::$app->urlManager->createUrl(['/school-class/print-list', 'id' => $model->id]),
                        'linkOptions' => ['target'=>'_blank'],
                    ],
                    [
                        'label' => 'Gegenstände', 
                        'url' => Yii::$app->urlManager->createUrl(['/school-class/print-subject-group', 'id' => $model->id]),
                        'linkOptions' => ['target'=>'_blank'],
                    ],
                    [
                        'label' => 'Lehrerliste', 
                        'url' => Yii::$app->urlManager->createUrl(['/school-class/print-teacher-list', 'id' => $model->id]),
                        'linkOptions' => ['target'=>'_blank'],
                    ],
                ],
            ],
        ]) ?>
<br />&nbsp;<br />
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'classname',
            'department',
            //'period',
            'annual_value',
            'class_head',
            'studentsnumber',
            'info',
            //'updated_at:datetime',
            //'created_at:datetime',
        ],
    ]) ?>

</div>
