<?php

namespace app\controllers;

use Yii;
use app\models\Todo;
use app\models\Category;
use yii\data\ActiveDataProvider;    // handles data fetching, pagination, and sorting.
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;         // for allowing delete actions only for POST requests

class TodoController extends Controller
{
    /**
     * @return array
     *
     * Defines behaviors for the controller.  It specifies which actions
     * are allowed for different HTTP verbs.  In this case, it ensures
     * that the 'delete' action can only be accessed via a POST request,
     * enhancing security.
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],   // Ensures the 'delete' action is only accessible via POST
                ],
            ],
        ];
    }

    /**
     * @return string
     *
     * Handles the display of the To-Do list and the creation of new To-Do items.
     * It fetches data for displaying To-Do items, handles the submission of the
     * To-Do creation form, and renders the 'index' view with the necessary data.
     */
    public function actionIndex()
    {
        $model = new Todo();
        $categoryModel = new Category();
        $dataProvider = new ActiveDataProvider([
            'query' => Todo::find()->with('category'),
            'pagination' => [
                'pageSize' => 4,    // Sets the number of To-Do items per page
            ],
        ]);

        // Handles form submission and saving new To-Do items
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        // Renders the 'index' view with the necessary data
        return $this->render('index', [
            'model' => $model,
            'categoryModel' => $categoryModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return \yii\web\Response
     *
     * Handles AJAX requests to create new To-Do items.  It receives data via
     * POST, creates a new To-Do model, encodes and saves the data to the database,
     * and returns a JSON response indicating success or failure.
     */
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

    /**
     * @return array
     *
     * Handles AJAX requests to delete a To-Do item. It receives the To-Do item's
     * ID via POST, finds the corresponding model, deletes it from the database,
     * and returns a JSON response indicating success or failure.
     */
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

    /**
     * @param int $id The ID of the Todo model to be found
     * @return Todo
     * @throws NotFoundHttpException
     *
     * Finds a To-Do model by its ID. If the model is not found, it throws a
     * NotFoundHttpException.
     */
    protected function findModel($id)
    {
        if (($model = Todo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
