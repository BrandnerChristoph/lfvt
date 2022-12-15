<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\SchoolClass;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use app\models\Department;
use app\models\DepartmentExtended;
use yii\web\NotFoundHttpException;
use app\models\search\ClassSubjectSearch;
use app\models\Subject;
use Exception;

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

            if ($this->request->isPost && $model->load($this->request->post())){
                if($model->save(false)) {
                    Yii::$app->session->setFlash('success', "Erstellung für die ".$model->class." wurde gespeichert.");
                    
                    $anchorLink = !empty($model->subject) ? $model->subject : "";
                    return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
                    //return $this->redirect(Yii::$app->request->referrer);

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
    public function actionUpdateItem($id)
    {
        try{
            $model = $this->findModel($id);
            
            if ($this->request->isPost && $model->load($this->request->post()) && $model->save(false)) {
                Yii::$app->session->setFlash('success', "Aktualisierung für die ".$model->class." wurde gespeichert.");
                $anchorLink = !empty($model->subject) ? $model->subject : "";
                return $this->redirect(Yii::$app->request->referrer. "#". $anchorLink);
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

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
    public function actionUpdateDepartment($department = 'IT', $class = null)
    {
        //if(is_null($department)){
            $objDepartment = Department::findOne($department); //->orderBy('id asc')->One();
            if(!is_null($objDepartment))
                $department = $objDepartment->id;
        //}
        if(is_null($class)){
            $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->select('id')->distinct();
            
        } else {
            // bestimmte Klasse
            $classes = SchoolClass::find()->andFilterWhere(['department' => $department])->andFilterWhere(['id' => $class])->select('id')->distinct();
        }

        $classSubjects = ClassSubject::find()->andFilterWhere(['class' => $classes])->all();

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
            'subjects' => ClassSubject::find()->select('subject')->distinct('subject')->andFilterWhere(['class' => $classes])->orderBy('subject asc')->All(),
            //'subjects' => ClassSubject::find()->select('subject')->andFilterWhere(['class' => $classes])->orderBy('subject asc')->All(),
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
