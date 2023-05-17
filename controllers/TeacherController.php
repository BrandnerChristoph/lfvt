<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use app\models\Teacher;
use yii\web\Controller;
use app\models\SchoolClass;
use kartik\form\ActiveForm;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\search\TeacherSearch;
use app\models\search\ClassSubjectSearch;

/**
 * TeacherController implements the CRUD actions for Teacher model.
 */
class TeacherController extends Controller
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
     * Lists all Teacher models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeacherSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Teacher models.
     * @return mixed
     */
    public function actionIndexPart($department = null, $class = null, $type = null)
    {
        $searchModel = new TeacherSearch();
        $params = $this->request->queryParams;
        
        if(!empty($class)){
            $teacherList = ClassSubject::find()
                                ->select('teacher')
                                ->distinct('teacher')
                                ->join('left join', "subject", "class_subject.subject = subject.id")
                                ->andFilterWhere(['class_subject.class' => $class])                                
                                ->andFilterWhere(['subject.type' => $type]);
            
        } else if(!empty($department)){
            $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->select('id')->distinct();
            $teacherList = ClassSubject::find()
                                ->select('teacher')
                                ->distinct('teacher')
                                ->join('left join', "subject", "class_subject.subject = subject.id")
                                ->andFilterWhere(['class' => $classes])                                
                                ->andFilterWhere(['subject.type' => $type]);
        } 

        if(!empty($teacherList)){
            $params["TeacherSearch"]["teacherListPreset"] = $teacherList;
        }

        $dataProvider = $searchModel->searchWithPreset($params);

        $dataProvider->pagination->pageSize = 500;

        return $this->renderAjax('index-preview', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        
    }

    /**
     * Lists all Classes by Teacher.
     * @return mixed
     */
    public function actionListSubject($teacher = null, $period = null)
    {
        if (is_null($period))
            $period = date("Y");
        
        //$searchModel = new TeacherSearch();
        $searchModel = new ClassSubjectSearch();
        $params["ClassSubjectSearch"]["teacher"] = $teacher;
        //$params["ClassSubjectSearch"]["teacher"] = $period;
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        
    }


    /**
     * Displays a single Teacher model.
     * @param int $id ID
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
     * Creates a new Teacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Teacher();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ActiveForm::validate($model);
          }

//        if (Yii::$app->request->isPost && $model->validate()) {
        if ($this->request->isPost) {
            if ($model->load($this->request->post()))
                $model->id = $model->initial;
                $model->created_at = time();
                $model->updated_at = time();
                if($model->save(false)) {
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
     * Updates an existing Teacher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Teacher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Teacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Teacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Teacher::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Updates an existing Teacher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLfv($id, $period = null)
    {
        $searchModel = new ClassSubjectSearch();
        $params['ClassSubjectSearch']['teacher'] = $id;
        $dataProvider = $searchModel->search($params);
        

        return $this->render('lfv', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * prints all the lessons from a teachers
     * 
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrintLesson($id = null, $period = null)
    {

        ini_set('pcre.backtrack_limit', 5000000);
        $content = "";
        if(is_null($id)){
            $teachers = Teacher::find()->andWhere('length(id) > 1')->all();
        } else {
            $teachers = Teacher::find()->andFilterWhere(['id' => $id])->limit(20)->all();
        }
        //$content .= "<link rel='stylesheet' href='@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'>";
        foreach ($teachers as $model) {
            $isAnnualValueNotOne = false;
            
            $content .= "<div class='container'><div class='row'><br />";
            $content .= "<h2>" . $model->name . " " . $model->firstname . "</h2>";

            //$content .= "<br />" . $model->name . " " . $model->firstname . "</h2>";
            
            $content .= "<div class='col-xs-4' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Einheiten: " . Yii::$app->formatter->asDecimal($model->hours,3) . "</div>";
            $content .= "<div class='col-xs-4 text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Realstunden (RST): " . Yii::$app->formatter->asDecimal($model->realHours,2) . "</div>";
            $content .= "<div class='col-xs-4' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Werteinheiten (WE): " . Yii::$app->formatter->asDecimal($model->teachingHours,3) . "</div>";
            
            $content .= "<h3>Wunschliste</h3>";
            
            if(sizeof($model->teacherWishlists) == 0)
                $content .= "<div class='col-xs-12' style='padding:0px 0px 0px 0px; margin: 0px !important;'>-</div>";

            foreach($model->teacherWishlists as $wl){
                $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Minimum: " . Yii::$app->formatter->asDecimal($wl->hours_min,3) . "</div>";
                $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>Maximum: " . Yii::$app->formatter->asDecimal($wl->hours_max,3) . "</div>";
                $content .= "<div class='col-xs-12' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $wl->info . "</div>";
            }
            
            $content .= "<h2 style='border-top: 1px solid #1450A0; padding-top: 10;'>Unterricht</h2>";
                $content .= "<div style='border-bottom: 3px solid black;'>" ;
                    $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Klasse</b></div>";
                    $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Fach</b></div>";
                    $content .= "<div class='col-xs-1  text-center' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>Einh.</b></div>";
                    $content .= "<div class='col-xs-1 text-right' style=''><b>WE</b></div>";
                    $content .= "<div class=' text-right' style=' padding:0px 0px 0px 0px; margin: 0px !important;'><b>RST</b></div>";
                $content .= "</b></div>";

                // Unterrichtseinheiten
                foreach(ClassSubject::find()->andFilterWhere(['teacher' => $model->id])->orderby('class asc')->All() as $item){
                    $content .= "<div style='border-bottom: 1px solid grey;'>" ;
                        $content .= "<div class='col-xs-2' style='padding:0px 0px 0px 0px; margin: 0px !important;'>";
                        empty($item->class) ? $content .= "&nbsp;" : $content .= $item->class;
                        $content .= "</div>";
                        
                        $content .= "<div class='col-xs-6' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $item->subject;
                            $content .= " <small>(WE: " . Yii::$app->formatter->asDecimal($item->subjectItem->value, 3);
                            if(!empty($item->subjectItem->value_real))
                                $content .= " / RST: " . Yii::$app->formatter->asDecimal($item->subjectItem->value_real, 2);
                            $content .= ")</small>";
                            $content .= "<br /><small>" . $item->subjectItem->name . "</small>";
                            $content .= "</div>";

                        $classAnnualValue = 1;
                        $objClass = SchoolClass::findOne($item->class);
                        if(!is_null($objClass)){
                            $classAnnualValue = $objClass->annual_value;
                            if($classAnnualValue != 1)
                                $isAnnualValueNotOne = True;
                        }

                            $content .= "<div class='col-xs-1 text-center' style='padding:0px 0px 0px 0px; margin: 0px !important;'>" . $item->hours * $classAnnualValue . " <br /><small>(" . Yii::$app->formatter->asDecimal($item->value,1) . "%)</small>" . "</div>";
                        
                        
    
                        // Realstunden
                            $itemRealSum = ($item->hours * $item->value / 100) * $item->subjectItem->value_real * $classAnnualValue;
                            $content .= "<div class=' text-right' style='padding:0px 0px 0px 0px; margin: 0px !important;'><b>" . Yii::$app->formatter->asDecimal($itemRealSum,2);
                            if($classAnnualValue != 1){
                                $content .= "*";
                            } else {
                                $content .= "&nbsp;";
                            }
                            $content .= "</b></div>";

                        // Werteinheiten
                            $itemSum = ($item->hours * $item->value / 100) * $item->subjectItem->value * $classAnnualValue;
    
                            $content .= "<div class='col-xs-1 text-right' style=''><b>" . Yii::$app->formatter->asDecimal($itemSum,3);
                            if($classAnnualValue != 1){
                                $content .= "*";
                            } else {
                                $content .= "&nbsp;";
                            }
                            $content .= "</b></div>";
                            
                    $content .= "</div>";
                }

                $content .= "</div></div>";
            

            if ($isAnnualValueNotOne){
                $content .= "<div><small><br />* ... für die Klasse werden Jahres-Prozentwerte verwendet</small></div>";
            }
            $content .= "<pagebreak></pagebreak>";            
        }
        ob_clean();

        // remove last pagebreak;
        $content = substr($content, 0, strlen($content)-23);

        /*
        echo $content;
        exit(0);
        */
        $pdf = new Pdf();
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->SetHeader('<img src="img/htl_logo.png" style="height: 30px;">||HTL Waidhofen/Ybbs<br /><small>3340 Waidhofen an der Ybbs, Im Vogelsang 8</small>');
        $mpdf->SetFooter(strtoupper($id).'||');
        $stylesheet = file_get_contents('../vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'); // external css
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);
        //$mpdf->render($content); // call mpdf write html
        
        $addInfo = "";
        if(!empty($id))
            $addInfo = "_".strtoupper($id);

        echo $mpdf->Output('Lehrerzuweisung'.$addInfo.'.pdf', 'O');
    }
}
