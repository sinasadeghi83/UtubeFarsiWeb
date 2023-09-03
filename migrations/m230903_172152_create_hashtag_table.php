<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hashtag}}`.
 */
class m230903_172152_create_hashtag_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%hashtag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull().' DEFAULT NOW()',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%hashtag}}');
    }
}
