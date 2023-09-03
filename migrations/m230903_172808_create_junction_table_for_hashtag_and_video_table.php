<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hashtag_video}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%hashtag}}`
 * - `{{%video}}`
 */
class m230903_172808_create_junction_table_for_hashtag_and_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hashtag_video}}', [
            'hashtag_id' => $this->integer(),
            'video_id' => $this->integer(),
            'PRIMARY KEY(hashtag_id, video_id)',
        ]);

        // creates index for column `hashtag_id`
        $this->createIndex(
            '{{%idx-hashtag_video-hashtag_id}}',
            '{{%hashtag_video}}',
            'hashtag_id'
        );

        // add foreign key for table `{{%hashtag}}`
        $this->addForeignKey(
            '{{%fk-hashtag_video-hashtag_id}}',
            '{{%hashtag_video}}',
            'hashtag_id',
            '{{%hashtag}}',
            'id',
            'CASCADE'
        );

        // creates index for column `video_id`
        $this->createIndex(
            '{{%idx-hashtag_video-video_id}}',
            '{{%hashtag_video}}',
            'video_id'
        );

        // add foreign key for table `{{%video}}`
        $this->addForeignKey(
            '{{%fk-hashtag_video-video_id}}',
            '{{%hashtag_video}}',
            'video_id',
            '{{%video}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%hashtag}}`
        $this->dropForeignKey(
            '{{%fk-hashtag_video-hashtag_id}}',
            '{{%hashtag_video}}'
        );

        // drops index for column `hashtag_id`
        $this->dropIndex(
            '{{%idx-hashtag_video-hashtag_id}}',
            '{{%hashtag_video}}'
        );

        // drops foreign key for table `{{%video}}`
        $this->dropForeignKey(
            '{{%fk-hashtag_video-video_id}}',
            '{{%hashtag_video}}'
        );

        // drops index for column `video_id`
        $this->dropIndex(
            '{{%idx-hashtag_video-video_id}}',
            '{{%hashtag_video}}'
        );

        $this->dropTable('{{%hashtag_video}}');
    }
}
