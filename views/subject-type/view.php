<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'SubjectTypes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="subject-view">

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
            'name',
            'sortorder',
            //'year_update',
            [
                'attribute' => 'year_update',
                'format' => 'raw',
                'value' => call_user_func(function ($data) {
                    if(!empty($data->year_update)){
                        if($data->year_update == "A")
                            return "Aufsteigerprinzip";
                        if($data->year_update == "J")
                            return "Jahrgangsprinzip";
                    }
                }, $model),
                
            ],
            [
                'label' => Yii::t('app', 'color'),
                'format' => 'raw',
                'value' => call_user_func(function ($data) {
                    if(!empty($data->color))
                        return "<b><span style='color: ".$data->color."'>" . $data->color . "</span></b>";
                }, $model),
                
            ],
            'info',
            'updated_at:datetime',
            'created_at:datetime',
        ],
    ]) ?>

</div>
