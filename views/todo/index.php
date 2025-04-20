<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;  // Add this line
use app\models\Category;

$this->title = 'To-do List';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="todo-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="todo-form">
        <?php $form = ActiveForm::begin(['id' => 'todo-form']); ?>
        
        <?= $form->field($model, 'category_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(Category::find()->all(), 'id', 'name'),
            ['prompt' => 'Select Category']
        ) ?>
        
        <?= $form->field($model, 'name')->textInput([
            'placeholder' => 'Enter todo item'
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Add Item', ['class' => 'btn btn-success']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>

    <?php Pjax::begin(['id' => 'pjax-todo-list']); // Add Pjax wrapper ?>
    <div class="todo-list">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'name',
                [
                    'attribute' => 'category_id',
                    'value' => 'category.name',
                    'label' => 'Category'
                ],
                [
                    'attribute' => 'timestamp',
                    'format' => 'datetime',
                    'label' => 'Created'
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function($url, $model) {
                            return Html::a('Delete', '#', [
                                'class' => 'btn btn-danger btn-sm',
                                'onclick' => 'deleteTodo('.$model->id.')'
                            ]);
                        }
                    ]
                ]
            ],
            'tableOptions' => ['class' => 'table table-striped'],
            'pager' => [
                'options' => ['class' => 'pagination justify-content-center'],
            ]
        ]); ?>
    </div>
    <?php Pjax::end(); // End Pjax wrapper ?>
</div>

<script>
// Handle form submission with AJAX
$('#todo-form').on('beforeSubmit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '<?= \yii\helpers\Url::to(['todo/create']) ?>',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                // Refresh the grid view
                $.pjax.reload({container: '#pjax-todo-list', timeout: false});
                $('#todo-form')[0].reset();
            } else {
                alert('Error: ' + JSON.stringify(response.errors));
            }
        }
    });
    
    return false;
});

// Handle todo deletion
function deleteTodo(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['todo/delete']) ?>',
            type: 'POST',
            data: {
                id: id,
                _csrf: '<?= Yii::$app->request->csrfToken ?>'
            },
            success: function(response) {
                if (response.success) {
                    $.pjax.reload({container: '#pjax-todo-list', timeout: false});
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                // Show detailed error info
                alert('Error occurred while deleting: ' + xhr.status + ' ' + xhr.statusText);
                console.error(xhr.responseText);
            }
        });
    }
}
</script>
