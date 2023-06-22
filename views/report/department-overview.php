<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\icons\Icon;
use yii\widgets\ActiveForm;
use app\models\Subject;
use app\models\Teacher;
use yii\bootstrap4\Modal;
use app\models\Department;
use app\models\SchoolClass;
use kartik\widgets\Select2;
use app\models\ClassSubject;
use kartik\editable\Editable;
//use kartik\widgets\ActiveForm;

use app\models\TeacherExtended;
use function PHPSTORM_META\type;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

$teacherList = Teacher::getAllTeachersArrayMap();
$subjectList = Subject::getArrayHelperList();
$classList = SchoolClass::getArrayHelperList();
?>

<div class="class-subject-update">

    
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
        
    <table class="table table-striped" style="margin-top: 10px; table-layout:fixed;" >
        <thead style="position: sticky; top: 55px; background-color:  <?= $objDepartment->default_color ?>; z-index:900;">
            <tr>
                <th style="width: 60px"><center>Fach</center></th>
                <?php
                    $col_id = 0;
                    foreach($classes as $class){
                        echo "<th id='col-".$col_id."-class' " .
                            " style='width: ".$widthProz."%;'" . 
                            " title='KV: " . TeacherExtended::getTeacherFullName($class->class_head) . " | Schüleranzahl: ".$class->studentsnumber."'>";
                            echo "<center>" . $class->id . "<br /><small>" . TeacherExtended::getTeacherInitial($class->class_head) . " (".$class->studentsnumber.")</small></center>";
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
                    echo "<tr style='height: 65px !important;'>";
                        $style = "";
                        
                        $classSubject->subjectItem->type == "Allgemeinbildenden Gegenstände" ? $style = "style='border-left: 3px solid green' title='".$classSubject->subjectItem->name." - ".$classSubject->subjectItem->type."'" : null;
                        $classSubject->subjectItem->type == "Fachtheorie" ? $style = "style='border-left: 3px solid red' title='Fachtheorie'" : null;
                        $classSubject->subjectItem->type == "Werkstatt" ? $style = "style='border-left: 3px solid blue' title='Werkstatt'" : null;

                        echo "<td ".$style.">";
                            echo "<center>";
                                echo $classSubject->subject;
                                /*
                                if (!empty($classSubject->subjectItem->value))
                                    echo "<br /><small>".Yii::$app->formatter->asDecimal($classSubject->subjectItem->value,3) ."<br /></small>";
                                */
                            echo "</center>";
                            echo "<div id='".$classSubject->subject."' style='margin-top: -190px;'></div>";
                        echo "</td>";

                        
                            foreach($classes as $class){
                                echo "<td style='border-left: 1px solid blue;'>";
                                    echo "<div class='row'>";
                                        
                                        // get Model
                                        $objList = ClassSubject::find()->andFilterWhere(["subject" => $classSubject->subject])->andFilterWhere(["class" => $class->id])->All();
                                            echo "<div class='col-lg-9'>";
                                            foreach($objList as $obj){                                                    

                                                echo "<center>";
                                                if(!is_null($obj)){
                                                    //echo "<strong>";
                                                    echo "<div class='' title='".$obj->teacher0->name." ".$obj->teacher0->firstname."' >";
                                                            if($obj->teacher == "?"){
                                                                //echo "<b>";
                                                                echo "<span style='color: red;'>";
                                                            }

                                                                echo $obj->teacher;

                                                            if($obj->teacher == "?"){
                                                                echo "</span>";
                                                                
                                                            }
                                                            //echo "</strong>";
                                                            echo "<small>";
                                                                echo  " " . Yii::$app->formatter->asDecimal($obj->hours, 1) ;
                                                                
                                                                if($obj->value != 100)
                                                                    echo " (" . Yii::$app->formatter->asDecimal($obj->value, 0) . "%)";
                                                            echo "</small>";                                                               
                                                        echo "</div>";                                                 
                                                }  
                                                
                                                echo "</center>";                                        
                                            }  
                                            echo "</div>";

                                        $index++;    
                                        
                                    echo "</div>";
                                echo "</td>";
                            }
                    echo "</tr>";

                }
            ?>                
        </tbody>
    </table>
</div>
