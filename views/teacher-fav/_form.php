<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacher-form">

    <?php $form = ActiveForm::begin(); ?>

    
    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    
    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    

    <?= $form->field($model, 'id')->hiddenInput()->label("") ?>
    
    <?php ActiveForm::end(); ?>

</div>
