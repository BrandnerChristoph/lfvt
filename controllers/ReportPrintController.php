<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use app\models\Teacher;
use yii\web\Controller;
use app\models\Department;
use app\models\TeacherFav;
use mdm\admin\models\User;
use app\models\SchoolClass;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use yii\helpers\ArrayHelper;
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
        $content = "";
        $schoolClasses = SchoolClass::find()->orderby('department asc, id asc')->All();
        
        foreach($schoolClasses as $objClass){
            $content .= SchoolClassController::getTeacherListContent($objClass);
            $content .= "<pagebreak></pagebreak>";
        }

        $content = substr($content, 0, strlen($content)-23); // remove last pagebreak
        

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'marginTop' => 25, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            'options' => ['title' => 'Unterricht '],
            'methods' => [ 
                'SetHeader'=>['<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                'SetFooter'=>['||'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();  
   }

   public function actionDepartmentOverview($department = null)
    {
        $class = null;
        if(is_null($department)){
            $objUser = User::findOne(Yii::$app->user->id);

            $objFavDepartment = TeacherFav::find()
                                            ->andFilterWhere(['type' => 'lfvt_default_department'])
                                            ->andFilterWhere(['user_id' => $objUser->username])
                                            ->One();
            if(!is_null($objFavDepartment)){
                $department = $objFavDepartment->value;            
            }

            $objDepartment = Department::findOne($department); //->orderBy('id asc')->One();
            if(!is_null($objDepartment))
                $department = $objDepartment->id;
        } else {
            $objDepartment = Department::findOne($department);
        }

        if(is_null($objDepartment)){
            // if no param is provided
            $objDepartment = Department::find()->One();
            $department = $objDepartment->id;
        }
        $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->select('id')->distinct();
        
        $classSubjects = ClassSubject::find()
                            //->join('left join', 'subject', 'class_subject.subject = subject.id')
                            ->andFilterWhere(['class_subject.class' => $classes])
                            //->orderBy('subject.sortorder asc, class_subject.subject')
                            ->all();
        
        $content = $this->renderPartial('/report/department-overview', [
                                'classSubjects' => $classSubjects, 
                                'classes' => is_null($class) ? SchoolClass::find()->andFilterWhere(['department' => $department])->distinct()->All() : SchoolClass::find()->andFilterWhere(['department' => $department])->andFilterWhere(['id' => $class])->distinct()->All(),
                                //'subjects' => ClassSubject::find()->select('subject')->distinct('subject')->andFilterWhere(['class' => $classes])->orderBy('subject asc')->All(),
                                'subjects' => ClassSubject::find()
                                                ->join('left join', 'subject', 'class_subject.subject = subject.id')
                                                ->select('class_subject.subject, subject.sortorder')
                                                ->distinct('subject')
                                                ->andFilterWhere(['class_subject.class' => $classes])
                                                //->orderBy('subject asc')
                                                ->orderBy('subject.sortorder asc, class_subject.subject')
                                                ->All(),
                                'department' => $department,
                                'objDepartment' => $objDepartment,
                            ]);
        //$content = $this->render();
        

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'marginTop' => 25, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px;}', 
            'options' => ['title' => 'Abteilung '],
            'methods' => [ 
                'SetHeader'=>['<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                'SetFooter'=>['Lehrfächerverteilung||'.$department],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();  
   }

   public function actionTeacherWorkload()
    {
        $content = "";
        $teacherList = Teacher::find()->orderby('name asc, firstname asc')->All();
        
        $content .= "<div class='container'><div class='row'>";
        $content .= "<h2>Lehrer Auslastung</h2>"; 
        
        // Header
        $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrer</b></div>";
        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: right;'><b>Wert&nbsp;&nbsp;&nbsp;&nbsp;</b></div>";
        $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: right;'><b>Stunden</b></div>";
        $content .= "<div class='col-xs-3' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: center;'><b>Wunsch</b></div>";
        
        $content .= "<div class='col-xs-12' style='padding:0px 0px 0px 0px; margin: 0px !important;'></div>";

        foreach($teacherList as $item){
            $content .= "<div style='border-bottom: 1px solid grey;'>";
            
                $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . trim($item->name . " " . $item->firstname . " (" . $item->initial . ")");
                $content .= "</div>";
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: right;'>";
                    $content .= Yii::$app->formatter->asDecimal($item->teachingHours, 3);
                $content .= "&nbsp;&nbsp;&nbsp;&nbsp;</div>";
                $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: right;'>";
                    $content .= Yii::$app->formatter->asDecimal($item->hours, 0);
                $content .= "</div>";
                $content .= "<div class='col-xs-3' style='padding:0px 0px 0px 0px; margin: 0px !important; text-align: center;'>";
                    if(!is_null($item->teacherWishlist)){

                        if(isset($item->teacherWishlist->hours_min) && isset($item->teacherWishlist->hours_max)){
                            $fontColor = "red";
                            if($item->teacherWishlist->hours_max < $item->hours){
                                $fontColor = "red";
                            } else if($item->teacherWishlist->hours_min > $item->hours){
                                $fontColor = "red";
                            } else if($item->teacherWishlist->hours_min <= $item->hours && $item->teacherWishlist->hours_max >= $item->hours){
                                $fontColor = "green";
                            }
    
    
                            $content .=  "<span style='color:".$fontColor.";'>";
                                $content .= $item->teacherWishlist->hours_min . " - " . $item->teacherWishlist->hours_max;
                            $content .=  "</span>";
                        }
                        else {
                            $content .= "&nbsp;";
                        }
                    }

                $content .= "</div>";
                
            $content .= "</div>";                                        
        }

        
        $content .= "</div></div>";
        //return $content;



        

        //$content = substr($content, 0, strlen($content)-23); // remove last pagebreak
        

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'marginTop' => 25, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            //'cssFile' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            'options' => ['title' => 'Unterricht '],
            'methods' => [ 
                'SetHeader'=>['<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>'], 
                'SetFooter'=>['||'],
            ]
        ]);

        //$pdf->content("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css",1);
        
        // return the pdf output as per the destination setting
        return $pdf->render();  
   }
}