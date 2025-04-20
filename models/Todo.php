<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "todo".
 */
class Todo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'todo';
    }

    public function rules()
    {
        return [
            [['name', 'category_id'], 'required'],
            [['category_id'], 'integer'],
            [['timestamp'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function beforeSave($insert)
{
    if ($insert) {
        $dt = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $this->timestamp = $dt->format('Y-m-d H:i:s');
    }
    return parent::beforeSave($insert);
}


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Todo item name',
            'category_id' => 'Category',
            'timestamp' => 'Timestamp',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
    
}