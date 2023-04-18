<?php

use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacher-form">


    <?php $form = ActiveForm::begin(['id' => 'routingForm', 'enableClientValidation' => true, 'enableAjaxValidation' => true]); ?>

    <div class="row">

        <!--div class="col-lg-6">
            <?= $form->field($model, 'id')->textInput(['readonly' => !$model->isNewRecord]) ?>
        </div-->

        <div class="col-lg-6">
            <?= $form->field($model, 'initial')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>
        </div>

        <div class="col-lg-6">
        <?= $form->field($model, 'is_active')->widget(Select2::classname(),[
                            'data' => [1 => 'ja', 0 => 'nein'],
                            'options' => [
                                'placeholder' => 'aktiv / inaktiv',
                                
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                'multiple' => false,
                            ]
                        ]) 
        ?>
        </div>
        
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'email_1')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'email_2')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
        </div>


        <div class="col-lg-12 text-center">
            
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success text-center',]) ?>
            
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>