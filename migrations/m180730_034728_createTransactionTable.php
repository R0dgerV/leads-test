<?php

use yii\db\Migration;

/**
 * Class m180730_034728_createTransactionTable
 */
class m180730_034728_createTransactionTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transactions', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(11)->notNull(),
            'amount' => $this->bigInteger(20)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('transactions');
    }
}
