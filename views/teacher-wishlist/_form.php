<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use app\models\TeacherExtended;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherWishlist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacher-wishlist-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-4">
        <?php // $form->field($model, 'teacher_id')->textInput() ?>
        <?= $form->field($model, 'teacher_id')->widget(Select2::classname(),[
                        'data' => TeacherExtended::getAllTeachersArrayMap(),
                        'options' => [
                            'placeholder' => 'Lehrer',                            
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => false,
                        ]
                    ]) 
    ?>
    </div>
    
    <div class="col-lg-4">
        <?= $form->field($model, 'hours_min')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-4">
        <?= $form->field($model, 'hours_max')->textInput(['maxlength' => true]) ?>
    </div>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
