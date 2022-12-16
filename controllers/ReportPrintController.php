<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use app\models\Department;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use app\models\SchoolClass;
use yii\web\NotFoundHttpException;
use app\models\search\DepartmentSearch;
use app\models\Teacher;
use yii\helpers\ArrayHelper;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class ReportPrintController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all classes and their teachers
     * @return mixed
     */
    public function actionTeacherInClass($department = null, $class = null)
    {

        
        
        $content = "";

        $schoolClasses = SchoolClass::find()->andFilterWhere(['id' => $class])->orderby('department asc, id asc')->All();
        
            foreach($schoolClasses as $objClass){
                $content .= SchoolClassController::getTeacherListContent($objClass);
                $content .= "<pagebreak></pagebreak>";
            }

            $content = substr($content, 0, strlen($content)-23); // remove last pagebreak
        

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // Margin Top
            'marginTop' => 25, 
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            // set mPDF properties on the fly
            'options' => ['title' => 'Unterricht '],
            // call mPDF methods on the fly
            'methods' => [ 
                'SetHeader'=>['<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                //'SetFooter'=>[$id.'||{PAGENO}'],
                'SetFooter'=>['||'],
            ]
        ]);

        
        // return the pdf output as per the destination setting
        return $pdf->render(); 











////////////////////////////////////////////////////////////////////////////////////


        //$teacherList = Teacher::find()->select('id', 'name', 'firstname', 'title')->asArray()->All();

        $arrTeacher =  ArrayHelper::map(Teacher::find()
                                        ->select('distinct(teacher.id) as id, name, firstname, initial as initial,  titel,  teacher_fav.sort_helper AS sort_order')
                                        //->select(['CASE WHEN teacher_fav.id IS NOT null THEN "Favoriten" ELSE "Lehrer" END AS sort_order'])
                                        //->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "'.$objUser->username.'"')
                                        ->leftJoin('teacher_fav','`teacher`.`id`= `teacher_fav`.`value` AND `teacher_fav`.`user_id` = "BN"')
                                        ->orderBy('sort_order desc, teacher.id asc')
                                        ->all(), 'id', 
                                    function($model, $defaultValue){
                                        $additionalInfo = "";
                                        return trim($model->name . " " . $model->firstname . " " . $model->titel);
                                    }
                                );
        $content = "";
        if(!is_null($class))
            $schoolClasses = SchoolClass::find()->andFilterWhere(['id' => $class])->orderby('department asc, id asc')->All();
        elseif(!is_null($department))
            $schoolClasses = SchoolClass::find()->andFilterWhere(['department' => $department])->orderby('department asc, id asc')->All();
        else
            $schoolClasses = SchoolClass::find()->orderby('department asc, id asc')->All();
        
        $content = "<div class='container'>";
        $content = "<div class='row'>";
            foreach($schoolClasses as $objClass){
                $content .= "<h2>".$objClass->id."</h2>";
                
                $content .= "<div style='border-bottom: 3px solid black;'>" ;
                    $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Kürzel</b></div>";
                    $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrername</b></div>";
                    $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fach</b></div>";
                    $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Stunden</b></div>";      
                $content .= "</div>";
                $lessons = null;
                $lessons = ClassSubject::find()
                                ->andFilterWhere(['class' => $objClass->id])
                                ->orderby('teacher asc, subject asc')->All();

                foreach($lessons as $objLesson){
                    $content .= "<div style='border-bottom: 1px solid grey;'>" ;
                
                        $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->teacher . "</div>";
                        $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $arrTeacher[$objLesson->teacher] . "</div>";
                        //$content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->teacher0->name . " " . $objLesson->teacher0->firstname . "</div>";
                        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->subject  . "</div>";
                        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . Yii::$app->formatter->asDecimal($objLesson->hours,3)  . "</div>";            
                    $content .= "</div>";
                }

                $content .= "<pagebreak></pagebreak>";
            }
        $content .= "</div>";
        $content .= "</div>";

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // Margin Top
            'marginTop' => 25,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            // set mPDF properties on the fly
            'options' => ['title' => 'Unterricht '],
            // call mPDF methods on the fly
            'methods' => [ 
                'SetHeader'=>['<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                //'SetFooter'=>[$id.'||{PAGENO}'],
                'SetFooter'=>['||'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render(); 
   }
}