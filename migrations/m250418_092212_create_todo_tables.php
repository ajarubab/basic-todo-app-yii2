<?php

use yii\db\Migration;

class m250418_092212_create_todo_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);

        $this->createTable('todo', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'category_id' => $this->integer()->notNull(),
            'timestamp' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE',
        ]);

        $this->insert('category', ['name' => 'Category A']);
        $this->insert('category', ['name' => 'Category B']);
        $this->insert('category', ['name' => 'Category C']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('todo');
        $this->dropTable('category');
    }
}
