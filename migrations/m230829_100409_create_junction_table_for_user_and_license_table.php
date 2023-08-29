<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_license}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%license}}`
 */
class m230829_100409_create_junction_table_for_user_and_license_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_license}}', [
            'user_id' => $this->integer(),
            'license_id' => $this->integer(),
            'created_at' => $this->timestamp()->notNull(),
            'PRIMARY KEY(user_id, license_id)',
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_license-user_id}}',
            '{{%user_license}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_license-user_id}}',
            '{{%user_license}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `license_id`
        $this->createIndex(
            '{{%idx-user_license-license_id}}',
            '{{%user_license}}',
            'license_id'
        );

        // add foreign key for table `{{%license}}`
        $this->addForeignKey(
            '{{%fk-user_license-license_id}}',
            '{{%user_license}}',
            'license_id',
            '{{%license}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_license-user_id}}',
            '{{%user_license}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_license-user_id}}',
            '{{%user_license}}'
        );

        // drops foreign key for table `{{%license}}`
        $this->dropForeignKey(
            '{{%fk-user_license-license_id}}',
            '{{%user_license}}'
        );

        // drops index for column `license_id`
        $this->dropIndex(
            '{{%idx-user_license-license_id}}',
            '{{%user_license}}'
        );

        $this->dropTable('{{%user_license}}');
    }
}
