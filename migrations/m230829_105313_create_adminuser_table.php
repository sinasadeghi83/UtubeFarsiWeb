<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%adminuser}}`.
 */
class m230829_105313_create_adminuser_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%adminuser}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(12)->unique(),
            'name' => $this->string(100),
            'password' => $this->string()->notNull(),
            'phone' => $this->string(15)->notNull()->unique(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%adminuser}}');
    }
}
