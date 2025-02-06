<?php

namespace app\controllers;

use Yii;
use app\models\TeacherWishlist;
use app\models\TeacherWishlistSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TeacherWishlistController implements the CRUD actions for TeacherWishlist model.
 */
class TeacherWishlistController extends Controller
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
     * Lists all TeacherWishlist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeacherWishlistSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeacherWishlist model.
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
     * Creates a new TeacherWishlist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeacherWishlist();
        $model->id = uniqid();
        $model->created_at = time();
        $model->updated_at = time();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
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
     * Creates a new TeacherWishlist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateForTeacher($teacher_id)
    {
        $model = new TeacherWishlist();
        $model->id = uniqid();
        $model->teacher_id = $teacher_id;
        $model->created_at = time();
        $model->updated_at = time();
        $model->save(false);
        $model = TeacherWishlist::findOne($model->id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'disableTeacher' => true,
        ]);
    }

    /**
     * Updates an existing TeacherWishlist model.
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
            return $this->redirect(Yii::$app->request->referrer);
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TeacherWishlist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if(strpos(Yii::$app->request->referrer, "view") == false)
            return $this->redirect(Yii::$app->request->referrer);
        else    
            return $this->redirect(['index']);
    }

    /**
     * Finds the TeacherWishlist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return TeacherWishlist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherWishlist::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
