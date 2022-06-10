<?php

use yii\db\Migration;

/**
 * Class m220608_202311_profile
 */
class m220608_202311_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable('profile', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'first_name' => $this->string(),
			'last_name' => $this->string(),
			'about' => $this->string(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
		$this->addForeignKey(
			'fk-profile-user_id-user-id',
			'profile',
			'user_id',
			'user',
			'id',
			'CASCADE'
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_202311_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_202311_profile cannot be reverted.\n";

        return false;
    }
    */
}
