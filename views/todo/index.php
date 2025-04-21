<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\Category;

/**
 * View file for displaying and managing the To-Do list.
 * 
 * This page includes:
 * - A form to add new To-Do items with category selection.
 * - A grid view listing existing To-Do items with pagination.
 * - AJAX-based interactions for adding and deleting items without full page reload.
 */
$this->title = 'To-do List';
?>

<div class="todo-index">
    <!-- Page header -->
    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <!-- To-Do item creation form -->
    <div class="todo-form">
        <?php $form = ActiveForm::begin(['id' => 'todo-form']); ?>

        <!-- Dropdown list for selecting category -->
        <?= $form->field($model, 'category_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(Category::find()->all(), 'id', 'name'),   // Fetch categories as id => name pairs
            ['prompt' => 'Select Category']
        ) ?>

        <!-- Text input for entering To-Do item name -->
        <?= $form->field($model, 'name')->textInput([
            'placeholder' => 'Enter todo item'
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Add Item', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- Pjax wrapper for AJAX-based partial page updates -->
    <?php Pjax::begin(['id' => 'pjax-todo-list']); // Add Pjax wrapper 
    ?>
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
                        'delete' => function ($url, $model) {    // Render a delete button with onclick handler
                            return Html::a('Delete', '#', [
                                'class' => 'btn btn-danger btn-sm',
                                'onclick' => 'deleteTodo(' . $model->id . ')'
                            ]);
                        }
                    ]
                ]
            ],
            'tableOptions' => ['class' => 'table table-striped'],
            'pager' => [
                'options' => ['class' => 'pagination justify-content-center'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                'activePageCssClass' => 'active',
                'prevPageLabel' => '<<',
                'nextPageLabel' => '>>',
                'hideOnSinglePage' => false,
            ],
        ]); ?>
    </div>
    <?php Pjax::end(); // End Pjax wrapper 
    ?>
</div>

<script>
    // JavaScript to handle AJAX form submission and deletion

    // Intercept form submission to send via AJAX
    $('#todo-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['todo/create']) ?>', // URL to send data
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                if (response.success) {
                    // On success, reload the todo list grid via Pjax
                    $.pjax.reload({
                        container: '#pjax-todo-list',
                        timeout: false
                    });
                    $('#todo-form')[0].reset();
                } else {
                    alert('Error: ' + JSON.stringify(response.errors));
                }
            }
        });

        return false; // Prevent default form submission
    });

    // Function to handle deletion of a To-Do item via AJAX
    function deleteTodo(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['todo/delete']) ?>', // URL for deletion
                type: 'POST',
                data: {
                    id: id,
                    _csrf: '<?= Yii::$app->request->csrfToken ?>' // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the todo list grid on successful deletion
                        $.pjax.reload({
                            container: '#pjax-todo-list',
                            timeout: false
                        });
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