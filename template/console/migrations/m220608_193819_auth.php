<?php

use yii\db\Migration;

/**
 * Class m220608_193819_auth
 */
class m220608_193819_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable('auth', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'source' => $this->string()->notNull(),
			'source_id' => $this->string()->notNull(),
		]);
	
		$this->addForeignKey('fk-auth-user_id-user-id',
			'auth',
			'user_id',
			'user',
			'id',
			'CASCADE',
			'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_193819_auth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_193819_auth cannot be reverted.\n";

        return false;
    }
    */
}
