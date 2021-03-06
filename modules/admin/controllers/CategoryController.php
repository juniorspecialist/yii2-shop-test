<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Category;
use app\modules\admin\models\SearchCategory;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchCategory();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Category();
        //$models = Category::getListCategory();
        if($model->isNewRecord){
            $model->active = 1;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->fileAttach($model);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                //'models' => $models,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        //$models = Category::getListCategory();
        $model->updated = Yii::$app->formatter->format($model->updated, 'date');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //если картинка есть, то больше не добавляем
            if(!$model->getImage()->itemId){
                $this->fileAttach($model);
            }
            $model->updated = Yii::$app->formatter->format($model->updated, 'date');
            return $this->render('update', [
                'model' => $model,
                //'models' => $models,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                //'models' => $models,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * attach image
     * @param type $model
     * @return boolean
     */
    protected function fileAttach($model) {
        $model->image = \yii\web\UploadedFile::getInstance($model, 'image');
        if($model->image){
            $path = Yii::getAlias('@webroot/upload/files/').$model->image->baseName.'.'.$model->image->extension;
            $model->image->saveAs($path);
            $model->attachImage($path);
            return true;
        }
        return false;
    }
}
