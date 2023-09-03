<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%video}}`.
 * Has foreign keys to the tables:.
 *
 * - `{{%channel}}`
 */
class m230903_172520_create_video_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%video}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'descriptions' => $this->string(),
            'channel_id' => $this->integer()->notNull(),
            'youtube_views' => $this->integer()->notNull(),
            'publish_date' => $this->timestamp()->notNull(),
            'youtube_link' => $this->string()->notNull(),
            'video_path' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull().' DEFAULT NOW()',
        ]);

        // creates index for column `channel_id`
        $this->createIndex(
            '{{%idx-video-channel_id}}',
            '{{%video}}',
            'channel_id'
        );

        // add foreign key for table `{{%channel}}`
        $this->addForeignKey(
            '{{%fk-video-channel_id}}',
            '{{%video}}',
            'channel_id',
            '{{%channel}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `{{%channel}}`
        $this->dropForeignKey(
            '{{%fk-video-channel_id}}',
            '{{%video}}'
        );

        // drops index for column `channel_id`
        $this->dropIndex(
            '{{%idx-video-channel_id}}',
            '{{%video}}'
        );

        $this->dropTable('{{%video}}');
    }
}
