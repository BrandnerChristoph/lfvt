<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use app\models\TeacherFav;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\ClassSubject;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\search\TeacherFavSearch;
use app\models\search\ClassSubjectSearch;
use Exception;
use mdm\admin\models\User;

/**
 * TeacherController implements the CRUD actions for Teacher model.
 */
class TeacherFavController extends Controller
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
        $searchModel = new TeacherFavSearch();
        $params = $this->request->queryParams;
        if(!Yii::$app->user->can("Superadmin")){
            $objUser = User::findOne(Yii::$app->user->id);    
            $params['TeacherFavSearch']['user_id'] = $objUser->username;
        }

        $dataProvider = $searchModel->search($params);
        

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
        $model = new TeacherFav();
        $model->id = uniqid();
        $objUser = User::findOne(Yii::$app->user->id);
        $model->user_id = $objUser->username;
        $model->type = "teacher_myFavorites";
        $model->created_at = time();
        $model->updated_at = time();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())){

                if($model->type == "teacher_myFavorites") $model->sort_helper = 1000;                             // Favoriten des Lehrers
                if(substr($model->type, 0, 14) == substr("teacher_suggest", 0, 14)) $model->sort_helper = 2000;           // fachspezifische Lehrer
                

                if($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
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

        if(!Yii::$app->user->can("Superadmin")){
            $objUser = User::findOne(Yii::$app->user->id);    
            if(strtoupper($model->user_id) != strtoupper($objUser->username))
                throw new Exception("Data can not be processed");
        }

        if ($this->request->isPost && $model->load($this->request->post())){
            $model->updated_at = time();
            $model->sort_helper = 0;
            if($model->type == "teacher_myFavorites") $model->sort_helper = 1000;    // Favoriten des Lehrers
            if(substr($model->type, 0, 14) == substr("teacher_suggest", 0, 14)) $model->sort_helper = 2000;           // fachspezifische Lehrer
            
            if($model->save()) 
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
        $objDel = $this->findModel($id);
        
        if(!Yii::$app->user->can("Superadmin")){
            $objUser = User::findOne(Yii::$app->user->id);    
            if(strtoupper($objDel->user_id) != strtoupper($objUser->username))
                throw new Exception("Data can not be deleted");
        }


        $objDel->delete();

        if(strpos(Yii::$app->request->referrer, "view") == false)
            return $this->redirect(Yii::$app->request->referrer);
        else    
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
        if (($model = TeacherFav::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
