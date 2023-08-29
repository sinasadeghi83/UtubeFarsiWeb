<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%license}}`.
 */
class m230829_095110_create_license_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%license}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'length' => $this->integer()->notNull(),
            'price' => $this->bigInteger()->notNull(),
            'status' => $this->boolean()->defaultValue(false),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%license}}');
    }
}
