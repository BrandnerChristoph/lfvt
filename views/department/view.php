<?php

use Yii;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Department */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="department-view">

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
            'head_of_department',
            //'default_color',
            [
                'label' => Yii::t('app', 'default_color'),
                'format' => 'raw',
                'value' => call_user_func(function ($data) {
                    if(!empty($data->default_color))
                        return "<b><span style='color: ".$data->default_color."'>" . $data->default_color . "</span></b>";
                }, $model),
                
            ],
            //'updated_at:datetime',
            //'created_at:datetime',
        ],
    ]) ?>

</div>
