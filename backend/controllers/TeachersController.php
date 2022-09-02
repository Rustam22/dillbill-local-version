<?php

namespace backend\controllers;

use backend\models\Review;
use backend\models\ReviewSearch;
use Yii;
use backend\models\Teachers;
use backend\models\TeachersSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * TeachersController implements the CRUD actions for Teachers model.
 */
class TeachersController extends AppController
{

    /**
     * Lists all Teachers models.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TeachersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $reviewSearchModel = new ReviewSearch();
        $reviewDataProvider = $reviewSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'reviewSearchModel' => $reviewSearchModel,
            'reviewDataProvider' => $reviewDataProvider,
        ]);
    }



    /**
     * Displays a single Teachers model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Displays a single Packet model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewReview($id)
    {
        return $this->render('view-review', [
            'model' => $this->findModelReview($id),
        ]);
    }



    /**
     * Creates a new Packet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreateReview()
    {
        $model = new Review();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-review', 'id' => $model->id]);
        }

        return $this->render('create-review', [
            'model' => $model,
        ]);
    }
    /**
     * Creates a new Teachers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new Teachers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Updates an existing Teachers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return Response|string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Packet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdateReview(int $id)
    {
        $model = $this->findModelReview($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-review', 'id' => $model->id]);
        }

        return $this->render('update-review', [
            'model' => $model,
        ]);
    }



    /**
     * Deletes an existing Teachers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Deletes an existing Packet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     */
    public function actionDeleteReview(int $id): Response
    {
        $this->findModelReview($id)->delete();

        return $this->redirect(['index']);
    }



    /**
     * Finds the Teachers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Teachers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Teachers
    {
        if (($model = Teachers::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * Finds the Packet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Review the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelReview(int $id): Review
    {
        if (($model = Review::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
