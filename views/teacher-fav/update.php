<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */

$this->title = Yii::t('app', 'Update Teacher Fav: {name}', [
    'name' => $model->user_id . ' -> '. $model->value //$model->user_id . " (" . $model->name . " " .  $model->firstname . ")" ,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => trim($model->name . " " . $model->firstname), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="teacher-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
