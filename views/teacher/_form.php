<?php

use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacher-form">


    <?php // $form = ActiveForm::begin(['id' => 'routingForm', 'enableClientValidation' => true, 'enableAjaxValidation' => true]); ?>
    <?php $form = ActiveForm::begin(['id' => 'routingForm',]); ?>

    <div class="row">

        <!--div class="col-lg-6">
            <?= $form->field($model, 'id')->textInput(['readonly' => !$model->isNewRecord, 'value' => strtoupper($model->id)]) ?>
        </div-->

        <div class="col-lg-6">
            <?= $form->field($model, 'initial')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord, 'value' => strtoupper($model->id)]) ?>
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
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'value' => strtoupper($model->name)]) ?>
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

        <?php
            if(!is_null($model->teacherWishlist)){
        ?>
            <!-- Wishlist -->
            <div class="col-lg-12"><hr />
                <h2>Leherwunsch</h2>
            </div>

            <div class="col-lg-6">
                <?= $form->field($model->teacherWishlist, 'hours_min')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model->teacherWishlist, 'hours_max')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-12">
                <?= $form->field($model->teacherWishlist, 'info')->textInput(['maxlength' => true]) ?>
            </div>
        <?php
            }
        ?>

        <?php
            if(!$model->isNewRecord){
        ?>
                <div class="col-lg-12 text-center" style="margin: 5px">
                        <?= Html::submitButton(Yii::t('app', '<< speichern & voriger Lehrer ('.$prevTeacher.')'), ['class' => 'btn btn-primary text-center', 'name' => 'prevTeacher', 'value' => $prevTeacher]) ?>
                        <?= Html::submitButton(Yii::t('app', 'speichern & nächster Lehrer ('.$nextTeacher.') >>'), ['class' => 'btn btn-primary text-center', 'name' => 'nextTeacher', 'value' => $nextTeacher]) ?>
                </div>
        <?php
            }
        ?>

        <div class="col-lg-12 text-center">            
                <?= Html::submitButton(!$model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Create'), ['class' => 'btn btn-success text-center',]) ?>            
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>