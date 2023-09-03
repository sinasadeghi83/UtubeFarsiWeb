<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%channel}}`.
 */
class m230903_171749_create_channel_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%channel}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'username' => $this->string()->unique()->notNull(),
            'description' => $this->string(),
            'links' => $this->string(),
            'header_path' => $this->string(),
            'prof_img_path' => $this->string(),
            'youtube_subscribers' => $this->integer()->notNull(),
            'youtube_views' => $this->integer(),
            'joined_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull().' DEFAULT NOW()',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%channel}}');
    }
}
