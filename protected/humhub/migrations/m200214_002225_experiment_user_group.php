<?php

use yii\db\Migration;

/**
 * Class m200214_002225_experiment_user_group
 */
class m200214_002225_experiment_user_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('experiment_user_group', [
            'id' => 'pk',
            'user_id' => 'int(11)',
            'group_id' => 'int(11)'
        ]);

        $this->addForeignKey('user_id', 'experiment_user_group', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('group_id', 'experiment_user_group', 'group_id', 'experiment_group', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_002225_experiment_user_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_002225_experiment_user_group cannot be reverted.\n";

        return false;
    }
    */
}
