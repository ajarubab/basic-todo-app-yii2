<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * Represents a category to which To-Do items can belong.
 * Provides validation rules, attribute labels, and relations.
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @return string
     *
     * Specifies the name of the database table associated with this Active Record class.
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @return array
     *
     * Defines validation rules for the model attributes.
     * - 'name' is required and must be a string with a maximum length of 255 characters.
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     *
     * Defines user-friendly labels for the model attributes.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * Defines the relation between Category and Todo.
     * Each Category can have many Todo items.
     * This sets up a "has-many" relation where 'category_id' in Todo refers to 'id' in Category.
     */
    public function getTodos()
    {
        return $this->hasMany(Todo::class, ['category_id' => 'id']);
    }
}