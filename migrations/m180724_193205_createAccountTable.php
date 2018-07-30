<?php

use yii\db\Migration;

/**
 * Class m180724_193205_createAccountTable
 */
class m180724_193205_createAccountTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'username' => $this->string(128)->notNull(),
            'link' => $this->string()->null(64),
            'access_token' => $this->string()->notNull(64),
            'balance' => $this->bigInteger(20)->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'flags' => $this->integer()->defaultValue(1),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('account');
    }
}
