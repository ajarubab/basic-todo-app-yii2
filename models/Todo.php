<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "todo".
 */
class Todo extends \yii\db\ActiveRecord
{
    /**
     * @return string
     *
     * Returns the name of the database table associated with this Active Record class.
     */
    public static function tableName()
    {
        return 'todo';
    }

    /**
     * @return array
     *
     * Defines the validation rules for attributes in this model.  It specifies
     * which attributes are required, their data types, and any constraints.
     */
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

    /**
     * @param bool $insert
     * @return bool
     * @throws \Exception
     *
     * This method is invoked before saving a record (after validation, if any).
     * The default implementation raises the [[EVENT_BEFORE_INSERT]] event when `$insert` is true,
     * or the [[EVENT_BEFORE_UPDATE]] event otherwise.
     * Only when the event is not handled will the actual saving take place.
     *
     * The timestamp is set to the current time in the 'Asia/Kolkata' timezone.
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $dt = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $this->timestamp = $dt->format('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

     /**
     * @return array
     *
     * Defines the attribute labels for the model's attributes.  These labels
     * are used in forms and other UI elements to provide user-friendly names
     * for the attributes.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Todo item name',
            'category_id' => 'Category',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * Defines a relationship with the Category model.  It specifies that each
     * Todo item belongs to one Category, using the 'category_id' attribute as
     * the foreign key.
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
}
