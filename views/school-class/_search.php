<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SchoolClassSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="school-class-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'classname') ?>

    <?= $form->field($model, 'departmennt') ?>

    <?= $form->field($model, 'period') ?>

    <?= $form->field($model, 'annual_value') ?>

    <?php // echo $form->field($model, 'class_head') ?>

    <?php // echo $form->field($model, 'studentsnumber') ?>

    <?php // echo $form->field($model, 'info') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
