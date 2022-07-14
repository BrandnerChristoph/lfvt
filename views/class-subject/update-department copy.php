<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Teacher;
//use yii\widgets\ActiveForm;
use yii\bootstrap4\Modal;
use app\models\Department;
use kartik\widgets\Select2;
use app\models\ClassSubject;
use kartik\widgets\ActiveForm;
use app\models\TeacherExtended;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/checkFormBeforeLeave.js');

$this->title = Yii::t('app', 'Update Class Subject: {name}', [
    'name' => $department,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Class Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $department, 'url' => ['#']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="class-subject-update">

    <h1><?= Html::encode($this->title) ?></h1>
    
        <div class="col-lg-12">
            <center>
            <?php
                $listDepartment = Department::find()->All();

                foreach($listDepartment as $listItem){
                    //echo '<div class="btn btn-default border" style="width: 110px">' . Html::a($listItem->id, ["update-department", 'department' => $listItem->id, "class" => "btn btn-default border"] ) . '</div>';
                    echo Html::a($listItem->id, ["update-department", 'department' => $listItem->id], ["class" => "btn btn-primary border", "style" => 'width: 110px;'] );
                }

            ?>
            </center>
        </div>

        <?php
            // not in use
            $col_width = 12/(sizeof($classes)+1);
            $prozWidth = 95;
            if(sizeof($classes) <= 5)
                $prozWidth = 90;

            if(sizeof($classes) <=1)
                $widthProz = 95/1;
            else
                $widthProz = 95/sizeof($classes);
            
        ?>
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                                //'template' => "{label}\n{input}\n{hint}\n{error}",
                                'template' => "{input}\n{hint}\n{error}",
                            ]
        ],); ?>
            <style>
                table { border: none; border-collapse: collapse; }
                table td { border-left: 1px solid #000; }
                table td:first-child { border-left: none; }
            </style>

        <!--table class="table table-striped table-bordered table-condensed" style="margin-top: 10px" -->
        <table class="table table-striped " style="margin-top: 10px; table-layout:fixed;" >
        <!--thead style='height: 120px; display:inline-block; top: 0px;  background: grey;'-->
            <thead style="position: sticky; top: 55px; background-color:  <?= $objDepartment->default_color ?>; z-index:900;">
                <tr>
                    <th><center>Fach</center></th>
                    <?php
                        $col_id = 0;
                        foreach($classes as $class){
                            echo "<th id='col-".$col_id."-class' " .
                                " style='width: ".$widthProz."%;'" . 
                                //title='Jahr: " . $class->period . " / KV: " . TeacherExtended::getTeacherFullName($class->class_head) . "'>";
                                " title='KV: " . TeacherExtended::getTeacherFullName($class->class_head) . "'>";
                                //echo "<center>" . $class->classname . " <small>(" . TeacherExtended::getTeacherInitial($class->class_head) . ")</small></center>";
                                echo "<center>" . $class->id . " <small>(" . TeacherExtended::getTeacherInitial($class->class_head) . ")</small></center>";
                            echo "</th>";
                            $col_id++;
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // tr (rows): Fächer
                    // td (cols): Klassen

                    $index = 0;
                    $btnIndex = 1000;

                    foreach($subjects as $classSubject){
                        echo "<tr>";
                            echo "<td><center>";
                                echo $classSubject->subject;                                
                                echo "<br /><small>".Yii::$app->formatter->asDecimal($classSubject->subjectItem->value,3) ."</small>";
                            echo "</center></td>";

                            
                                foreach($classes as $class){
                                    echo "<td>";
                                        echo "<div class='row'>";
                                            // get Model
                                            $objList = ClassSubject::find()->andFilterWhere(["subject" => $classSubject->subject])->andFilterWhere(["class" => $class->id])->All();
                                                foreach($objList as $obj){                                                    

                                                    if(!is_null($obj)){
                                                        //echo "<span class='row col-lg-12' style='padding: 20px; border-bottom: 1px solid black;'>";
                                                        echo "<span id='item-".$index."' class='row col-lg-12'>";
                                                            echo "<div class='col-lg-12' >";
                                                                //echo "<div class='background-aqua'>HEADER</div>";

                                                                echo $form->field($obj, "[$index]teacher")->widget(Select2::classname(),[
                                                                        'data' => TeacherExtended::getAllTeachersArrayMap(),
                                                                        'options' => [
                                                                            'placeholder' => 'Lehrer',                            
                                                                        ],
                                                                        'pluginOptions' => [
                                                                            'allowClear' => true,
                                                                            'multiple' => false,
                                                                            'max-width' => '250px',
                                                                        ]
                                                                    ])->label('Lehrer') ;
                                                            echo "</div>";
                                                            
                                                            echo "<div class='col-lg-6'>";
                                                                    echo $form->field($obj, "[$index]hours")->textInput(['placeholder' => "Stunden"]);
                                                            echo "</div>";
                                                            echo "<div class='col-lg-6'>";
                                                                    echo $form->field($obj, "[$index]value")->textInput(['placeholder' => "Prozent"]);
                                                            echo "</div>";
                                                            echo "<div style='visibility: hidden; display:none;'>";
                                                            echo $form->field($obj, "[$index]id");
                                                            echo "</div>";
                                                        echo "</span>";
                                                        $index++;                                                        
                                                }                                          
                                            }  

                                            $btnIndex++;
                                            $rand = rand();

                                            echo "<div class='col-lg-12 text-center'>";

                                                echo Html::button( "+ " . $classSubject->subject . '-' . $class->id, 
                                                    [ 
                                                        'id' => "btn-add-".$btnIndex,
                                                        //$("#item-' . ($index - 1) . '").after($addGroup);
                                                        'class' => 'btn btn-success',
                                                        'onclick' => '(function ( $event ) { 
                                                                            var addIndex = Math.floor(Math.random() * 500000) + 500000;
                                                                            console.log("add item ("+addIndex+")");
                                                                            

                                                                            var $addGroup = "<span id=\'item-"+addIndex+"\' class=\'row col-lg-12\'> \
                                                                                                    <div class=\'col-lg-12\'> \
                                                                                                        ' . /*str_replace('"', "\'", preg_replace("/\r|\n/", "", $form->field($obj, "[".'+addIndex+'."]teacher")->widget(Select2::classname(),[
                                                                                                            'data' => TeacherExtended::getAllTeachersArrayMap(),
                                                                                                            'options' => [
                                                                                                                'placeholder' => 'Lehrer',                            
                                                                                                            ],
                                                                                                            'pluginOptions' => [
                                                                                                                'allowClear' => true,
                                                                                                                'multiple' => false,
                                                                                                                'max-width' => '250px',
                                                                                                            ]
                                                                                                        ]))) */  ' \
                                                                                                    </div> \
                                                                                                    <div class=\'col-lg-6\'> \
                                                                                                        <input type=\'text\' id=\'classsubject-"+addIndex+"-hours\' class=\'form-control is-valid\' name=\'ClassSubject["+addIndex+"][hours]\' placeholder=\'Stunden\' aria-invalid=\'false\'> \
                                                                                                    </div> \
                                                                                                    <div class=\'col-lg-6\'> \
                                                                                                        <input type=\'text\' id=\'classsubject-"+addIndex+"-value\' class=\'form-control is-valid\' name=\'ClassSubject["+addIndex+"][value]\' placeholder=\'Prozent\' aria-invalid=\'false\'> \
                                                                                                    </div> \
                                                                                                    <input type=\'hidden\' id=\'classsubject-"+addIndex+"-id\' class=\'form-control is-valid\' name=\'ClassSubject["+addIndex+"][id]\' value=\'"+addIndex+"\' > \
                                                                                                    <input type=\'hidden\' id=\'classsubject-"+addIndex+"-class\' class=\'form-control is-valid\' name=\'ClassSubject["+addIndex+"][class]\' value=\''.$class->id.'\' > \
                                                                                                    <input type=\'hidden\' id=\'classsubject-"+addIndex+"-subject\' class=\'form-control is-valid\' name=\'ClassSubject["+addIndex+"][subject]\' value=\''.$classSubject->subject.'\' > \
                                                                                                </span>";
                                                                            
                                                                            $("#btn-add-' . $btnIndex . '").before($addGroup);
                                                                            
                                                                            console.log("insert done");
                                                                            
                                                                        })();',                                                        

                                                    ]);
                                                //echo Html::button('Button 2', [ 'class' => 'btn btn-primary', 'onclick' => 'alert("Button 2 clicked");' ]);
                                            echo "</div>";

                                            
                                            /*
                                                Modal::begin([
                                                    'title' => '<h2>Lehrer/Fach hinzufügen</h2>',
                                                    'id'=>'modal',
                                                    'size' => 'modal-lg',
                                                    'toggleButton' => [
                                                        'label' => $class->id . " - " . $classSubject->subject . ' hinzu', 
                                                        'class' =>'button btn-success center', 
                                                        'style' => 'margin:auto; margin-top: 10px;'],
                                                
                                                ]);

                                                
                                                Modal::end();
                                                */
                                            
                                        echo "</div>";
                                        //echo "<a href='#' class='btn btn-success'>add</a>";
                                    echo "</td>";
                                }
                        echo "</tr>";
                    }
                ?>                
            </tbody>
        </table>

        <div class="form-group">
            <center>
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </center>
        </div>

    <?php ActiveForm::end(); ?>

</div>
