<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use app\models\SchoolClass;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use yii\web\NotFoundHttpException;
use app\models\search\SchoolClassSearch;
use app\models\Teacher;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

/**
 * SchoolClassController implements the CRUD actions for SchoolClass model.
 */
class SchoolClassController extends Controller
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
     * Lists all SchoolClass models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SchoolClassSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SchoolClass model.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SchoolClass model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SchoolClass();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SchoolClass model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())){
            $model->updated_at = time();
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SchoolClass model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SchoolClass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return SchoolClass the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SchoolClass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
    /**
     * prints classes and their teachers to pdf
     *
     * @param  mixed $id
     * @param  mixed $period
     * @return void
     */
    public function actionPrintList($id, $period = null)
    {
        $model = $this->findModel($id);        
        
        $content = "<div class='container'><div class='row'>";
        $content .= "<h2>" . $model->id ."<br /><small>".$model->classname."</small></h2>";
        
        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Klassenvorstand</div> ";
        $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->classHead->firstname . " " . $model->classHead->name . " (".$model->class_head.")</b></div>";
        
        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Schüleranzahl</div> ";
        $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->studentsnumber . "</b></div>";
        
        // Stundentafel
        $content .= "<h2 style='border-top: 1px solid #1450A0; padding-top: 10;'>Stundentafel</h2>";
        
        $lessons = ClassSubject::find()->select('subject')->distinct()->orderby('subject asc')->andFilterWhere(['class' => $id])->all(); //->andFilterWhere(['class' => $id])->distinct('subject')->All();
        $cntEinheit = 0;
        $cntWerteinheit = 0;
        foreach($lessons as $item){
            $content .= "<div style='border-bottom: 1px solid grey;'>";
            
                $content .= "<div class='col-xs-3' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>".$item->subject."</b>";
                    $content .=  "<br /><span style='font-size: 10px'>";
                    $content .= $item->subjectItem->info;   // Info des Schulfachs
                    $content .= "</span>";
                    
                $content .= "</div>";
                $teacherItems = ClassSubject::find()
                                                ->andFilterWhere(['class' => $id])
                                                ->andFilterWhere(['subject' => $item->subject])
                                                ->orderby('teacher asc')
                                                ->all();
                $content .= "<div class='col-xs-7' style='padding:0px 0px 0px 0px; margin: 0px !important;'>";
                    $strContent = "";
                    $cntItemEinheit = 0;
                    $cntItemWerteinheit = 0;
                    $useLinebreakBefore = false;
                    foreach($teacherItems as $element){
                        $cntItemEinheit += $element->hours * ($element->value/100);
                        $cntItemWerteinheit += ($element->hours * ($element->value/100) * $element->subjectItem->value);
                        if($useLinebreakBefore)
                            $strContent .= "<br />";
                        $strContent .= $element->teacher0->name . " " . $element->teacher0->firstname . " (".$element->teacher.") - " . $element->hours . " Einh. (".Yii::$app->formatter->asDecimal($element->value,1)."%)<br />";
                        $strContent .= "<span style='font-size: 10px'>";                        
                            if(!empty($element->classroom)){
                                $strContent .= "<small>Raum: " . $element->classroom . "; </small>";
                                $useLinebreakBefore = true;
                            }
                            if(!empty($element->info)){
                                $strContent .= "<small>" . $element->info . " </small>";
                                $useLinebreakBefore = true;
                            }
                        $strContent .= "</span>";
                    }

                    $content .= $strContent;
                $content .= "</div>";
                $content .= "<div class='text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>";
                $content .= $cntItemEinheit;
                $content .= "<br /><small>" . Yii::$app->formatter->asDecimal($cntItemWerteinheit,3) . "</small>";
                $content .= "</div>";
                
                $cntEinheit += $cntItemEinheit ;
                $cntWerteinheit += $cntItemWerteinheit;

            $content .= "</div>";                                        
        }

        $content .= "<div style='font-size: 16px; margin-top: 20px; font-weight: 700;'>";
            $content .= "<div class='col-xs-7 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Einheiten</div>";
            $content .= "<div class='col-xs-5 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>".$cntEinheit."</div>";
            
            $content .= "<div class='col-xs-7 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Werteinheiten</div>";
            $content .= "<div class='col-xs-5 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>".Yii::$app->formatter->asDecimal($cntWerteinheit,3)."</div>";
        $content .= "</div>";                   

        /*
        $content .= "<div style='border-bottom: 3px solid black;'>" ;
                $content .= "<div class='col-xs-7' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fach</div>";
                $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrer.</div>";
                $content .= "<div class=' text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Einheiten</div>";
            $content .= "</b></div>";
        foreach($lessons as $item){
            $content .= "<div style='border-bottom: 1px solid grey;'>" ;
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $item->class . "</div>";
                $content .= "<div class='col-xs-7' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $item->subject;
                    $content .= " <small>(WE: " . Yii::$app->formatter->asDecimal($item->subjectItem->value, 3) . ")</small>";
                    $content .= "<br /><small>" . $item->subjectItem->name . "</small>";
                    $content .= "</div>";
                $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $item->hours . " <br /><small>(" . Yii::$app->formatter->asDecimal($item->value,1) . "%)</small>" . "</div>";
                
                $itemSum = ($item->hours * $item->value / 100) * $item->subjectItem->value;

                $content .= "<div class=' text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . Yii::$app->formatter->asDecimal($itemSum,3)  . "</b></div>";
            $content .= "</div>";
        }
        */
        
        $content .= "</div></div>";

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
                'SetFooter'=>[$id.'||'],
            ]
        ]);

        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
/*
        return $this->render('lfv', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);*/
    }

    /**
     * prints classes and their teachers to pdf
     *
     * @param  mixed $id
     * @param  mixed $period
     * @return void
     */
    public function actionPrintSubjectGroup($id = null, $period = null)
    {
        $schoolClasses = null;
            if(!is_null($id)){
                $schoolClasses = SchoolClass::find()->andFilterWhere(['id' => $id])->All();
            } 
            if(empty($schoolClasses) && !is_null($id)){
                $schoolClasses = SchoolClass::find()->andFilterWhere(['department' => $id])->orderBy('id asc')->All();
            } 
            if(empty($schoolClasses) && is_null($id)){
                $schoolClasses = SchoolClass::find()->andFilterWhere(['id' => $id])->All();
            }
           

            $pdf = new Pdf(['marginTop' => 25,]);
            $mpdf = $pdf->api; // fetches mpdf api
            $mpdf->SetHeader('<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>');
            $mpdf->SetFooter(strtoupper($id).'||');
            $stylesheet = file_get_contents('../vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'); // external css
            $mpdf->WriteHTML($stylesheet,1);

            $idx = 0;
            foreach ($schoolClasses as $model){
                $content = "";

                if(!$idx == 0)
                    $content .= "<pagebreak></pagebreak>";
                
                $idx++;

                $annualValueClass = 1;
                $cntEinheit = 0;
                $cntWerteinheit = 0;
                $cntRealWert = 0;

                //$model = $this->findModel($id);        
                
                $content .= "<div class='container'><div class='row'>";
                $content .= "<h2>" . $model->id ."<br /><small>".$model->classname."</small></h2>";
                
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Klassenvorstand</div> ";
                $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->classHead->firstname . " " . $model->classHead->name . " (".$model->class_head.")</b></div>";
                
                $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Schüleranzahl</div> ";
                $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->studentsnumber . "</b></div>";
                
                // Stundentafel
                $content .= "<h3 style='border-top: 1px solid #1450A0; padding-top: 10;'>Gegenstände nach Fach sortiert</h3>";
                
                $content .= "<div class='col-xs-4' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fach</b></div>";
                $content .= "<div class='col-xs-7'>";
                    $content .= "<div class='col-xs-3' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrer</b></div>";
                    $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Std.</b></div>";
                    $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>RST</b></div>";
                    $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>WE</b></div>";
                $content .= "</div>";
                $content .= "<div class='col-xs-12'>&nbsp;</div>";
                
                $lessons = ClassSubject::find()->select('subject')->distinct()->orderby('subject asc')->andFilterWhere(['class' => $model->id])->all(); //->andFilterWhere(['class' => $id])->distinct('subject')->All();
                
                foreach($lessons as $item){
                    $content .= "<div style='border-bottom: 1px solid grey;'>";
                    
                        $content .= "<div class='col-xs-4' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>".$item->subject;
                        //    $content .= "<br /><small>" . $item->subjectItem->name . "</small>";
                        $content .= "</div>";
                        $teacherItems = ClassSubject::find()
                                                        ->andFilterWhere(['class' => $model->id])
                                                        ->andFilterWhere(['subject' => $item->subject])
                                                        ->orderby('teacher asc')
                                                        ->all();

                        $annualValueClass = $model->annual_value;

                        $content .= "<div class='col-xs-7'>";
                        
                            foreach($teacherItems as $element){

                                //$cntItemEinheit += $element->hours * ($element->value/100);
                                //$strContent .= $element->teacher0->name . " " . $element->teacher0->firstname . " (".$element->teacher.") - " . $element->hours . " Einh. (".Yii::$app->formatter->asDecimal($element->value,1)."%)<br />";
                            

                                $cntItemEinheit = $element->hours * ($element->value/100) * $annualValueClass;
                                $cntItemWerteinheit = ($element->hours * ($element->value/100) * $element->subjectItem->value) * $annualValueClass;
                                $cntItemRealWert = ($element->hours * ($element->value/100) * $element->subjectItem->value_real) * $annualValueClass;
                                $prozJahr = ($element->value) == 100 ? "" : "<small> (".$element->value . "%)</small>";
                                
                                $content .= "<div class='col-xs-3' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $element->teacher0->id . $prozJahr . "</div>";
                                $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . Yii::$app->formatter->asDecimal($cntItemEinheit,2) . "</div>";
                                $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . Yii::$app->formatter->asDecimal($cntItemRealWert, 2) . "</div>";
                                $content .= "<div class='col-xs-3 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . Yii::$app->formatter->asDecimal($cntItemWerteinheit, 3) . "</div>";

                                $cntEinheit += $cntItemEinheit ;
                                $cntWerteinheit += $cntItemWerteinheit;
                                $cntRealWert += $cntItemRealWert;
                            }

                        $content .= "</div>";
                    $content .= "</div>";                                        
                }

                $content .= "<div style='font-size: 16px; margin-top: 20px; font-weight: 700;'>";
                    $content .= "<div class='col-xs-7 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Einheiten</div>";
                    $content .= "<div class='col-xs-5 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>".Yii::$app->formatter->asDecimal($cntEinheit, 3)."</div>";
                    
                    $content .= "<div class='col-xs-7 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Realeinheiten</div>";
                    $content .= "<div class='col-xs-5 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>".Yii::$app->formatter->asDecimal($cntRealWert, 3) ."</div>";
                    
                    $content .= "<div class='col-xs-7 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Werteinheiten</div>";
                    $content .= "<div class='col-xs-5 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>".Yii::$app->formatter->asDecimal($cntWerteinheit,3)."</div>";
                    
                $content .= "</div>";  
                
                if($annualValueClass != 1){
                    $content .= "<div class='col-xs-12'>";
                        $content .= "<br />Für die Klasse wird ein Jahres-Prozentwerte von ".Yii::$app->formatter->asDecimal($annualValueClass*100, 2)."% verwendet.";
                    $content .= "</div>";  

                }
                
                $content .= "</div></div>";
                
                $mpdf->WriteHTML($content,2);
            }

            //$content = substr($content, 0, strlen($content)-23);
/*
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
                    'SetFooter'=>[$id.'||'],
                ]
            ]);

            
            // return the pdf output as per the destination setting
            return $pdf->render(); 
            */

        
        
        /*
        $mpdf->WriteHTML(substr($content, 0, strlen($content)/2),2);
        $mpdf->WriteHTML(substr($content, strlen($content)/2),2);
        */
        $addInfo = "";
        if(!empty($id))
            $addInfo = "_".strtoupper($id);

        echo $mpdf->Output('Lehrerzuweisung'.$addInfo.'.pdf', 'I');
    }

     /**
     * prints classes and their teachers to pdf
     *
     * @param  mixed $id
     * @param  mixed $period
     * @return void
     */
    public function actionPrintTeacherList($id, $period = null)
    {
        $model = $this->findModel($id);
        
        $content = $this->getTeacherListContent($model);

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
                'SetFooter'=>[$id.'||'],
            ]
        ]);

        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }

    
    /**
     * HTML Content for Teacher
     *
     * @param  mixed $model
     * @return void
     */
    public static function getTeacherListContent($model){
        $content = "<div class='container'><div class='row'>";
        $content .= "<h2>" . $model->id ."<br /><small>".$model->classname."</small></h2>";
        
        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Klassenvorstand</div> ";
        //$content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->classHead->firstname . " " . $model->classHead->name . " (".$model->class_head.")</b></div>";
        if (empty($model->class_head))
            $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>-</b></div>";
        else {
            $strHead = $model->class_head;
            
            $objTeacher = Teacher::findOne($model->class_head);
            if(!is_null($objTeacher))
                $strHead = trim($objTeacher->titel . " " . $objTeacher->name . " " . $objTeacher->firstname);

            if(empty($strHead))  $strHead = $model->class_head;
            $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $strHead . "</b></div>";
        }
        
        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Schüleranzahl</div> ";
        $content .= "<div class='col-xs-10' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . $model->studentsnumber . "</b></div>";
        
        // Stundentafel
        $content .= "<h2 style='border-top: 1px solid #1450A0; padding-top: 10;'>Lehrerliste</h2>";
        
        $lessons = ClassSubject::find()->select('teacher')->distinct()->orderby('teacher asc')->andFilterWhere(['class' => $model->id])->all(); //->andFilterWhere(['class' => $id])->distinct('subject')->All();
        $cntEinheit = 0;
        $cntWerteinheit = 0;

        // Header
        $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Kürzel</b></div>";
        $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Lehrername</b></div>";
        $content .= "<div class='' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fächer</b></div>";

        foreach($lessons as $item){
            $content .= "<div style='border-bottom: 1px solid grey;'>";
            
                $content .= "<div class='col-xs-1' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>".$item->teacher;
                $content .= "</div>";
                $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>";
                    if(!empty($item->teacher0->name))
                        $content .= $item->teacher0->name . " " . $item->teacher0->firstname;
                    else
                        $content .= "-";
                $content .= "</div>";
                $content .= "<div class='' style='padding:0px 0px 0px 0px; margin: 0px !important;'>";
                $classSubjects =  ClassSubject::find()->select('subject')->distinct()->orderby('subject asc')
                                                        ->andFilterWhere(['class' => $model->id])
                                                        ->andFilterWhere(['teacher' => $item->teacher])
                                                        ->all();
                foreach($classSubjects as $itemClassSub){
                    $content .= $itemClassSub->subject . ", ";
                
                }
                $content = substr($content, 0, strlen($content)-2);

                $content .= "</div>";
                
            $content .= "</div>";                                        
        }

        
        $content .= "</div></div>";
        return $content;
    }

}
