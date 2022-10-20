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
//use kartik\widgets\ActiveForm;

use app\models\TeacherExtended;
use function PHPSTORM_META\type;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

//$this->registerJsFile(Yii::$app->request->baseUrl . '/js/checkFormBeforeLeave.js');

$this->title = Yii::t('app', 'Update Zuweisung: {name}', [
    'name' => $department,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Class Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $department, 'url' => ['#']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$teacherList = Teacher::getAllTeachersArrayMap();
$subjectList = Subject::getArrayHelperList();
$classList = SchoolClass::getArrayHelperList();
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
        
    <style>
        table { border: none; border-collapse: collapse; }
        table td { border-left: 1px solid #000; }
        table td:first-child { border-left: none; }
    </style>
    
    <table class="table table-striped " style="margin-top: 10px; table-layout:fixed;" >
        <thead style="position: sticky; top: 55px; background-color:  <?= $objDepartment->default_color ?>; z-index:900;">
            <tr>
                <th><center>Fach</center></th>
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
                    echo "<tr>";
                        echo "<td><center>";
                            echo $classSubject->subject;
                            if (!empty($classSubject->subjectItem->value))
                                echo "<br /><small>".Yii::$app->formatter->asDecimal($classSubject->subjectItem->value,3) ."</small>";
                        echo "</center></td>";

                        
                            foreach($classes as $class){
                                echo "<td>";
                                    echo "<div class='row'>";
                                        // create new Item
                                        /*
                                            echo '<div class="col-log-12  pull-right">';
                                            echo '<a class="showModalButton btn btn-sm btn-success " 
                                                        value="' . Url::to(['create-item', 
                                                                                'id' => uniqid(), 
                                                                                'class' => $class->id, 
                                                                                'subject' => $classSubject->subject
                                                                                ]) . '" 
                                                        href="#" 
                                                        title="'.$classSubject->subject.' / '.$class->id.'">'
                                                                        . Icon::show('plus') . '<small> ' .  $classSubject->subject . ' (' . $class->id . ')</small>'
                                                                        . '</a>';
                                        echo '</div>';
                                        */


                                        // get Model
                                        $objList = ClassSubject::find()->andFilterWhere(["subject" => $classSubject->subject])->andFilterWhere(["class" => $class->id])->All();
                                            foreach($objList as $obj){                                                    

                                                if(!is_null($obj)){
                                                    echo "<div class='col-lg-12' class='' title='".$obj->teacher0->name." ".$obj->teacher0->firstname."' >";
                                                            echo $obj->teacher;
                                                            echo "<small>";
                                                                echo " " . $obj->hours;
                                                                if($obj->value != 100)
                                                                    echo " (" . Yii::$app->formatter->asDecimal($obj->value, 1) . "%)";
                                                            echo "</small>";

                                                            // edit item
                                                            echo '<a class="showModalButton btn btn-sm" value="' . Url::to(['update-item', 'id' => $obj->id]) . '" href="#" title="'.$classSubject->subject.' / '.$class->id.'">'
                                                                        . Icon::show('edit')        
                                                                        . '</a>';

                                                            // delete item
                                                            echo Html::a(Icon::show('trash'), 
                                                                                ["delete", 'id' => $obj->id], 
                                                                                [
                                                                                    "class" => "btn btn-sm btn-error",
                                                                                    'data' => [
                                                                                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                                                                        'method' => 'post',
                                                                                    ]
                                                                                ] 
                                                                            );                                                                
                                                        echo "</div>";                                                 
                                                }                                          
                                            }  

                                            // create new Item
                                            echo '<div class="col-lg-12 text-center">';
                                                echo '<a class="showModalButton btn btn-sm btn-success" 
                                                            value="' . Url::to(['create-item', 
                                                                                    'id' => uniqid(), 
                                                                                    'class' => $class->id, 
                                                                                    'subject' => $classSubject->subject
                                                                                    ]) . '" 
                                                            href="#" 
                                                            title="'.$classSubject->subject.' / '.$class->id.'">'
                                                                            . Icon::show('plus') . '<small> ' .  $classSubject->subject . ' (' . $class->id . ')</small>'
                                                                            . '</a>';
                                            echo '</div>';
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
