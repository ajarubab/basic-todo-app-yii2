<?php

use yii\db\Migration;

class m250418_092212_create_todo_tables extends Migration
{
    /**
     * Applies the migration.
     *
     * This method is called when running the migration to create the database schema.
     * It creates:
     * - `category` table with `id` as primary key and `name` as a required string.
     * - `todo` table with:
     *    - `id` as primary key,
     *    - `name` as a required string,
     *    - `category_id` as a required integer foreign key referencing `category(id)` with cascade on delete,
     *    - `timestamp` with default current timestamp.
     *
     * It also inserts three default categories: 'Category A', 'Category B', and 'Category C'.
     *
     * @return void
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
            // Foreign key constraint linking category_id to category.id, with cascade delete
            'FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE',
        ]);

        $this->insert('category', ['name' => 'Category A']);
        $this->insert('category', ['name' => 'Category B']);
        $this->insert('category', ['name' => 'Category C']);
    }

    /**
     * Reverts the migration.
     *
     * This method is called when rolling back the migration.
     * It drops the `todo` and `category` tables, effectively removing all related data and schema.
     *
     * @return void
     */
    public function safeDown()
    {
        $this->dropTable('todo');
        $this->dropTable('category');
    }
}
