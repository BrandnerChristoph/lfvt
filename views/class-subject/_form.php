<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Teacher;
use yii\web\JsExpression;
use kartik\select2\Select2;
use kartik\widgets\Typeahead;
use kartik\widgets\ActiveForm;
use app\models\TeacherExtended;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */
/* @var $form yii\widgets\ActiveForm */
/*
if (!state.id) return state.text; // optgroup
    src = '$url' +  state.id.toLowerCase() + '.png'
    return '<img class="flag" src="' + src + '"/>' + state.text;
*/

$format = <<< SCRIPT
function format(state) {
    return state.text + '<i class="glyphicon glyphicon-star"></i>';
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);
?>

<div class="class-subject-form" id="class-subject-form">
    <?php $form = ActiveForm::begin(['id' => 'update-form', 
                                        'fieldConfig' => [
                                            'inputOptions' => [
                                                'autocomplete' => 'off'
                                            ]
                                        ]
                                    ]); ?>
    <div class="container">
        <div class="row">
            <?php // $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
            <div class="col-lg-6">
                <?= $form->field($model, 'class')->textInput(['maxlength' => true, 'readonly' => false])->label("Klasse") ?>
                </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'subject')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            </div>

            
            <div class="col-lg-6">
                <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'hours')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-6">

                <?php
                    $template = '<div><p class="repo-language">{{display}}</p></div>';
                ?>
                <?= $form->field($model, 'teacher', ['enableAjaxValidation' => true, ])->widget(Typeahead::classname(), [
                    'options' => ['placeholder' => 'Lehrerkürzel ...'],
                    'pluginOptions' => ['highlight'=>true],
                    'dataset' => [
                        [
                            'display' => 'value',
                            'remote' => [
                                //'url' => Url::to(['teacher/teacher-list-typeahead', 'q' => '%QUERY', ]) . '?q=%QUERY',
                                'url' => Url::to(['teacher/teacher-list-typeahead']) . '&q=%QUERY',
                                'wildcard' => '%QUERY'
                            ],
                            'templates' => [
                                'notFound' => '<div class="text-danger" style="padding:0 8px">keine Daten</div>',
                                'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                            ],
                            'limit' => 20,
                        ]
                    ],
                    'pluginEvents' =>  [
                        'typeahead:change' => 'function(e, d) { 
                            console.log("done");
                        }',
                        //'typeahead:selected' => 'function(e, d) { console.log("done selected");}'
                    ],
                ])

                ?>
            </div>
            
            <div class="col-lg-6">
                <?= $form->field($model, 'classroom')->textInput(['maxlength' => true]) ?>
            </div>

            <?php //= $form->field($model, 'classroom')->textInput(['maxlength' => true]) ?>


            <div class="form-group col-lg-12 text-center">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $( document ).ready(function() {
        $("#classsubject-teacher").focus();
        $("#classsubject-teacher").select();
    });
</script>
