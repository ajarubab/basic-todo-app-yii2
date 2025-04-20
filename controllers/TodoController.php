<?php

namespace app\controllers;

use Yii;
use app\models\Todo;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TodoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $model = new Todo();
        $categoryModel = new Category();
        $dataProvider = new ActiveDataProvider([
            'query' => Todo::find()->with('category'),
            'pagination' => [
                'pageSize' => 4, // Set the number of items per page
            ],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
            'categoryModel' => $categoryModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Todo();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                'success' => true,
                'id' => $model->id,
                'name' => \yii\helpers\Html::encode($model->name),
                'category' => \yii\helpers\Html::encode($model->category->name),
                'timestamp' => Yii::$app->formatter->asDatetime($model->timestamp),
            ]);
        } else {
            return $this->asJson([
                'success' => false,
                'errors' => $model->getErrors(),
            ]);
        }
    }

    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['success' => false, 'message' => 'Missing ID'];
        }

        try {
            $model = $this->findModel($id);
            if ($model->delete()) {
                return ['success' => true, 'message' => 'Item deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete item'];
            }
        } catch (\yii\web\NotFoundHttpException $e) {
            return ['success' => false, 'message' => 'Item not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    protected function findModel($id)
    {
        if (($model = Todo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
