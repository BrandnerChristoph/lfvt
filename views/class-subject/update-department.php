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
use kartik\bs4dropdown\ButtonDropdown;
//use kartik\widgets\ActiveForm;

use app\models\TeacherExtended;
use yii\web\UrlManager;

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
<?php
    //$this->registerCss(".pull-right { right: 0 !important ; left:auto; }");
?>

<div class="class-subject-update">

    <h1><?= Html::encode($this->title) ?></h1>
        <div class="col-lg-12 text-right">
            <script>
                function showTeacherInDepartment(){
                    window.open("<?= Url::to(['/teacher/index-part', 
                                                'department' => $department
                                                ])?>",
                                "Lehrerauslastung",
                                "directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=700,height=900");
                }
                function showAllTeachers(){
                    window.open("<?= Url::to(['/teacher/index-part', 
                                                ])?>",
                                "Lehrerauslastung (alle Lehrer)",
                                "directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=700,height=900");
                }
            </script>
            
            <?= Html::a("alle Lehrer", '#', ['onClick' => 'showAllTeachers();', 'class' =>'btn btn-success']) ?>
            <?= Html::a("Lehrer in der Abteilung", '#', ['onClick' => 'showTeacherInDepartment();', 'class' =>'btn btn-primary']) ?>
            <?php //Html::a(Icon::show('print'), Url::to(["/report-print/department-overview", 'department'=> $department]), ['class' =>'btn btn-primary']) ?>
            <?= 
                ButtonDropdown::widget([
                    'label' => Icon::show('print'), 
                    'encodeLabel' => false,
                    'dropdown' => [
                        'items' => [
                            [
                                'label' => 'Lehrfächerverteilung', 
                                'url' => Url::to(["/report-print/department-overview", 'department'=> $department]),
                                'linkOptions' => [
                                    'target'=>'_blank', 
                                //    'class' => 'text-right'
                                ]
                                
                            ],
                            [
                                'label' => 'Gegenstände je Klassse', 
                                'url' => Url::to(["/school-class/print-subject-group", 'id'=> $department]),
                                'linkOptions' => [
                                    'target'=>'_blank', 
                                //    'class' => 'text-right'
                                ]
                            ],
                        ],
                        'options' => [
                            'class' => 'dropdown-menu-right', // right dropdown
                        ],
                    ],
                    
                    'buttonOptions' => [
                        'class' => 'btn-outline-secondary',
                    ]
                ]);
            ?>
        </div>
    
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
                    echo "<tr style='height: 65px !important;'>";
                        $style = "";
                        
                        $classSubject->subjectItem->type == "Allgemeinbildenden Gegenstände" ? $style = "style='border-left: 3px solid green' title='".$classSubject->subjectItem->name." - ".$classSubject->subjectItem->type."'" : null;
                        $classSubject->subjectItem->type == "Fachtheorie" ? $style = "style='border-left: 3px solid red' title='Fachtheorie'" : null;
                        $classSubject->subjectItem->type == "Werkstatt" ? $style = "style='border-left: 3px solid blue' title='Werkstatt'" : null;

                        echo "<td ".$style.">";
                            echo "<center>";
                                echo "<div class='' title='".$classSubject->subjectItem->info. "' >";
                                    echo $classSubject->subject;
                                echo "</div>";
                                if (!empty($classSubject->subjectItem->value))
                                    echo "<small>".Yii::$app->formatter->asDecimal($classSubject->subjectItem->value,3) ."<br /></small>";
                            echo "</center>";
                            echo "<div id='".$classSubject->subject."' style='margin-top: -190px;'></div>";
                        echo "</td>";

                        
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
                                            echo "<div class='col-lg-9'>";
                                            foreach($objList as $obj){                                                    

                                                if(!is_null($obj)){
                                                    echo "<strong>";
                                                    $strTitleAdditionalInfo = "";
                                                    if(!empty($obj->info))
                                                        $strTitleAdditionalInfo .= " | " . $obj->info;
                                                    echo "<div class='' title='".$obj->teacher0->name." ".$obj->teacher0->firstname."".$strTitleAdditionalInfo. "' >";
                                                            if($obj->teacher == "?"){
                                                                //echo "<b>";
                                                                echo "<span style='color: red;'>";
                                                            }

                                                                echo $obj->teacher;

                                                                /*
                                                                // https://demos.krajee.com/editable
                                                                echo Editable::widget([
                                                                    'model'=>$obj, 
                                                                    'attribute' => 'teacher',
                                                                    //'name'=>'teacher', 
                                                                    //'asPopover' => false,
                                                                    //'value' => $obj->teacher,
                                                                    //'header' => 'Name',
                                                                    'size'=>'md',
                                                                    'options' => ['class'=>'form-control', 'placeholder'=>'Lehrer Kürzel...'],
                                                                    
                                                                ]);
                                                                */
                                                            if($obj->teacher == "?"){
                                                                echo "</span>";
                                                                //echo "</b>";
                                                            }
                                                            echo "</strong>";
                                                            echo "<small>";
                                                                if($obj->hours <= 0)
                                                                    echo "<span style='color: red;'>";
                                                                
                                                                echo " <strong>" . Yii::$app->formatter->asDecimal($obj->hours, 1) . "</strong>";
                                                                
                                                                if($obj->hours <= 0)
                                                                    echo "</span>";

                                                                if($obj->value != 100)
                                                                    echo " (" . Yii::$app->formatter->asDecimal($obj->value, 0) . "%)";
                                                            echo "</small>";

                                                            // edit item
                                                            echo '<a class="showModalButton btn btn-sm" '
                                                                        . 'style="font-size: 0.85rem; color:grey; padding: 0.2rem 0.3rem;" '
                                                                        . 'value="' . Url::to(['update-item', 'id' => $obj->id]) . '" href="#" title="'.$classSubject->subject.' / '.$class->id.'">'
                                                                        . Icon::show('edit')        
                                                                        . '</a>';

                                                            // delete item
                                                            echo Html::a(Icon::show('trash'), 
                                                                                ["delete", 'id' => $obj->id], 
                                                                                [
                                                                                    "class" => "btn btn-sm",
                                                                                    "style" => "font-size: 0.85rem; color:grey; padding: 0.2rem 0.3rem;" ,
                                                                                    'data' => [
                                                                                        //'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                                                                        'method' => 'post',
                                                                                    ]
                                                                                ] 
                                                                            );                                                                
                                                        echo "</div>";                                                 
                                                }                                          
                                            }  
                                            echo "</div>";

                                            // create new Item
                                            /*
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
                                            */
                                            echo '<div style="text-align: right;">';
                                                // green button
                                                echo '<a class="showModalButton btn btn-sm" style="background-color: #90ee90;font-size:0.5rem;padding: 0.2rem 0.3rem;" 
                                                            value="' . Url::to(['create-item', 
                                                                                    'id' => uniqid(), 
                                                                                    'class' => $class->id, 
                                                                                    'subject' => $classSubject->subject
                                                                                    ]) . '" 
                                                            href="#" 
                                                            title="+ '.$classSubject->subject.' / '.$class->id.'">'
                                                                            . Icon::show('plus')
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

    <div class="col-lg-12 text-center">
        <a class="showModalButton btn btn-success"  
            value="<?= Url::to(['add-subject-to-department', 
                                    'id' => uniqid(), 
                                    'department' => $department,
                                    ]) ?>" 
            href="#" 
            title="neues Fach in der Abteilung hinzufügen">
                <?= Icon::show('plus') . " neues Fach in Abteilung hinzufügen" ?>
            </a>
        
    </div>
</div>
