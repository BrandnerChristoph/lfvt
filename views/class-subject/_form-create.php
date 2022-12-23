<?php

use app\models\SchoolClass;
use yii\helpers\Html;
use app\models\TeacherExtended;
use app\models\Subject;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\web\View;

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
    <?php $form = ActiveForm::begin(); ?>
    <div class="container">
        <div class="row">
            <?php // $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
            <div class="col-lg-6">
                <?php // $form->field($model, 'class')->textInput(['maxlength' => true])->label("Klasse") ?>
                <?= $form->field($model, 'class')->widget(Select2::classname(),[
                                'data' => SchoolClass::getArrayHelperList(),
                                'options' => [
                                    'placeholder' => 'Klasse',
                                    'appendTo' => '#myModal',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'multiple' => false,
                                    'templateResult' => new JsExpression('format'),
                                    'templateSelection' => new JsExpression('format'),
                                    'escapeMarkup' => $escape,
                                ],
                            ])->label("Klasse")
                ?>
                </div>
            <div class="col-lg-6">
                <?php //= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'subject')->widget(Select2::classname(),[
                                'data' => Subject::getArrayHelperList(),
                                'options' => [
                                    'placeholder' => 'Fach',
                                    'appendTo' => '#myModal',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'multiple' => false,
                                    'templateResult' => new JsExpression('format'),
                                    'templateSelection' => new JsExpression('format'),
                                    'escapeMarkup' => $escape,
                                ],
                            ])
                ?>
            </div>

            
            <div class="col-lg-6">
                <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'hours', ['inputOptions' => ['autofocus' => 'autofocus']])->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'teacher')->widget(Select2::classname(),[
                                'data' => TeacherExtended::getAllTeachersArrayMap(),
                                'options' => [
                                    'placeholder' => 'Lehrer',
                                    'appendTo' => '#myModal',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'multiple' => false,
                                    'templateResult' => new JsExpression('format'),
                                    'templateSelection' => new JsExpression('format'),
                                    'escapeMarkup' => $escape,
                                ],
                            ])->label("Lehrer")
                ?>
            </div>

            <?php //= $form->field($model, 'classroom')->textInput(['maxlength' => true]) ?>


            <div class="form-group col-lg-12 text-center">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>
