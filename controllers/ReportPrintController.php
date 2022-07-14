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
    public function actionTeacherInClass()
    {
        $schoolClasses = SchoolClass::find()->orderby('department asc, id asc')->All();
        $content = "";

        foreach($schoolClasses as $objClass){
            
            //$content = $this->renderPartial('_reportView');
            
            $content .= "<div class='container'><div class='row'>";
            $content .= "<h2>" . $objClass->id ."</h2>";
    
            $lessons = ClassSubject::find()
                            ->andFilterWhere(['class' => $objClass->id])
                            ->orderby('teacher asc, subject asc')->All();


            $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Kürzel</b></div>";
            $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrername</b></div>";
            $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fach</b></div>";
            $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Stunden</b></div>";      

            foreach($lessons as $objLesson){
            
                $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->teacher . "</div>";
                $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->teacher0->name . " " . $objLesson->teacher0->firstname . "</div>";
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $objLesson->subject  . "</div>";
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . Yii::$app->formatter->asDecimal($objLesson->hours,3)  . "</div>";            
            }
            
            $content .= "</div></div>";
            $content .= "<pagebreak></pagebreak>";
            
        }
            
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
                    'SetHeader'=>['<img src="https://www.htlwy.at/wp2017/wp-content/themes/htl2017/assets/Logo_HTLWaidhofen_std_fbg_rgb_web.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                    //'SetFooter'=>[$id.'||{PAGENO}'],
                    'SetFooter'=>['||{PAGENO}'],
                ]
            ]);
    
            
            // return the pdf output as per the destination setting
            return $pdf->render(); 
        
    }
}