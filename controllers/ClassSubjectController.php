<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\Response;
use app\models\Subject;
use yii\web\Controller;
use app\models\Department;
use app\models\TeacherFav;
use mdm\admin\models\User;
use app\models\SchoolClass;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use app\models\ClassSubject;
use app\models\DepartmentExtended;
use yii\web\NotFoundHttpException;
use app\models\search\ClassSubjectSearch;

/**
 * ClassSubjectController implements the CRUD actions for ClassSubject model.
 */
class ClassSubjectController extends Controller
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
     * Lists all ClassSubject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassSubjectSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClassSubject model.
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
     * Creates a new ClassSubject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClassSubject();
        $model->id = uniqid();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save(false)) {
                Yii::$app->session->setFlash('success', "Zuweisung wurde gespeichert.");
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
     * Creates a new ClassSubject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */    
    public function actionCreateItem($id = null, $class = null, $subject = null)
    {
        try {
            $model = new ClassSubject();
            $model->id = empty($id) ? uniqid() : $id;
            $model->class = empty($class) ? null : $class;
            $model->subject = empty($subject) ? null : $subject;
            $model->value = 100;
            $model->created_at = time();
            $model->updated_at = time();

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                // Validierung 
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            
            elseif ($this->request->isPost && $model->load($this->request->post()) ) {
                $model->teacher = strtoupper($model->teacher);
                if($model->save(false)){
                    Yii::$app->session->setFlash('success', "Erstellung für die ".$model->class." wurde gespeichert.");
                    $anchorLink = !empty($model->subject) ? $model->subject : "";
                    return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
                }
            }

            
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } catch(Exception $ex){
            Yii::$app->session->setFlash('error', "<b>Erstellung für die ".$model->class." wurde NICHT vorgenommen.</b><br />" . $ex->getMessage());
            
            $anchorLink = !empty($model->subject) ? $model->subject : "";
            return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
            //return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Creates a new ClassSubject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */    
    public function actionAddSubjectToDepartment($id)
    {
        try {
            $model = new ClassSubject();
            $model->id = empty($id) ? uniqid() : $id;
            $model->class = empty($class) ? null : $class;
            $model->subject = empty($subject) ? null : $subject;
            $model->value = 100;
            $model->created_at = time();
            $model->updated_at = time();

            if ($this->request->isPost && $model->load($this->request->post())){
                if($model->save(false)) {
                    Yii::$app->session->setFlash('success', "Erstellung für die ".$model->class." wurde gespeichert.");
                    
                    $anchorLink = !empty($model->subject) ? $model->subject : "";
                    return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
                    //return $this->redirect(Yii::$app->request->referrer);
                }
            } 

            return $this->renderAjax('_form-create', [
                'model' => $model,
            ]);
        } catch(Exception $ex){
            Yii::$app->session->setFlash('error', "<b>Erstellung für die ".$model->class." wurde NICHT vorgenommen.</b><br />" . $ex->getMessage());
            
            $anchorLink = !empty($model->subject) ? $model->subject : "";
            return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
            //return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Creates a new ClassSubject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdateItem($id)
    {
        try{
            $model = $this->findModel($id);

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                // Validierung 
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            
            elseif ($this->request->isPost && $model->load($this->request->post()) ) {
                $model->teacher = strtoupper($model->teacher);
                $model->updated_at = time();
                if($model->save(false)){
                    Yii::$app->session->setFlash('success', "Aktualisierung für die ".$model->class." wurde gespeichert.");
                    $anchorLink = !empty($model->subject) ? $model->subject : "";
                    return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
            
        } catch(Exception $ex){
            Yii::$app->session->setFlash('error', "<b>Aktualisierung wurde NICHT gespeichert!</b><br />".$ex->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Updates an existing ClassSubject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(empty($model->created_at))
            $model->created_at = time();
        $model->updated_at = time();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            // Validierung 
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        elseif ($this->request->isPost && $model->load($this->request->post()) ) {
            $model->teacher = strtoupper($model->teacher);
            $model->updated_at = time();
            if($model->save(false)){
                Yii::$app->session->setFlash('success', "Aktualisierung für die ".$model->class." wurde gespeichert.");
                $anchorLink = !empty($model->subject) ? $model->subject : "";
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        /*

        if ($this->request->isPost && $model->load($this->request->post())){
            $model->updated_at = time();
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }
        */

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClassSubject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateDepartment($department = null, $class = null)
    {

        /////////////////////////////////////////////////////
        
        // Check if there is an Editable ajax request
        if (isset($_POST['hasEditable'])) {
            $model = ClassSubject::findOne($_POST['ClassSubject']['id']);
            // use Yii's response format to encode output as JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            // store old value of the attribute
            $oldValue = $model->name;
            
            // read your posted model attributes
            if ($model->load($_POST)) {
                // read or convert your posted information
                $value = $model->name;
                
                // validate if any errors
                if ($model->save()) {
                    // return JSON encoded output in the below format on success with an empty `message`
                    return ['output' => $value, 'message' => ''];
                } else {
                    // alternatively you can return a validation error (by entering an error message in `message` key)
                    return ['output' => $oldValue, 'message' => 'Incorrect Value! Please reenter.'];
                }
            }
            // else if nothing to do always return an empty JSON encoded output
            else {
                return ['output'=>'', 'message'=>''];
            }
            
        }

        

        /////////////////////////////////////////////////////


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

        if(is_null($class)){
            $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->select('id')->distinct();
            
        } else {
            // bestimmte Klasse
            $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->andFilterWhere(['id' => $class])->select('id')->distinct();
        }

        $classSubjects = ClassSubject::find()
                            //->join('left join', 'subject', 'class_subject.subject = subject.id')
                            ->andFilterWhere(['class_subject.class' => $classes])
                            //->orderBy('subject.sortorder asc, class_subject.subject')
                            ->all();

        //if (ClassSubject::loadMultiple($classSubjects, Yii::$app->request->post()) && ClassSubject::validateMultiple($classSubjects)) {
        if (ClassSubject::loadMultiple($classSubjects, Yii::$app->request->post()) ) {
            Yii::trace("count items: " . sizeof($classSubjects), "brch");
            foreach ($classSubjects as $classSub) {
                if(empty($classSub->teacher) && empty($classSub->hours)){
                    // delete entry if no teacher and no hours are set
                    ClassSubject::findOne($classSub->id)->delete();
                } else {
                    $saveModel = ClassSubject::findOne($classSub->id);
                    if(is_null($saveModel)){
                        Yii::trace("add new item to database (ID: ".$classSub->id.")", "BrCh");
                        $saveModel = new ClassSubject();
                        $saveModel->id = $classSub->id;
                        $saveModel->subject = $classSub->subject;
                        $saveModel->created_at = time();

                    }
                    $saveModel->value = $classSub->value;
                    $saveModel->hours = $classSub->hours;
                    $saveModel->teacher = $classSub->teacher;
                    $saveModel->updated_at = time();
                    Yii::trace("save element: " . $classSub->id);
                    $saveModel->save(false);
                } 
            }
            
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('update-department', [
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
    }

    /**
     * Deletes an existing ClassSubject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $anchorLink = !empty($model->subject) ? $model->subject : "";
        
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
        //return $this->redirect(Yii::$app->request->referrer);
        /*

        $class = $model->class;
        $department = 
        $model->delete();

        return $this->render('update-department', [
            'classSubjects' => $classSubjects, 
            'classes' => is_null($class) ? SchoolClass::find()->andFilterWhere(['department' => $department])->distinct()->All() : SchoolClass::find()->andFilterWhere(['department' => $department])->andFilterWhere(['id' => $class])->distinct()->All(),
            'subjects' => ClassSubject::find()->select('subject')->distinct('subject')->andFilterWhere(['class' => $classes])->orderBy('subject asc')->All(),
            //'subjects' => ClassSubject::find()->select('subject')->andFilterWhere(['class' => $classes])->orderBy('subject asc')->All(),
            'department' => $department,
            'objDepartment' => $objDepartment,
        ]);

        //return $this->redirect(['index']);
        */
    }

    /**
     * Finds the ClassSubject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return ClassSubject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClassSubject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
