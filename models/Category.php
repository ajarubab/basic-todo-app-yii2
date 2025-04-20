<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 */
class Category extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'category';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getTodos()
    {
        return $this->hasMany(Todo::class, ['category_id' => 'id']);
    }
}