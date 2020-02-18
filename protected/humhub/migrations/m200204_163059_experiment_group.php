<?php

use yii\db\Migration;

/**
 * Class m200204_163059_experiment_group
 */
class m200204_163059_experiment_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('experiment_group', [
            'id' => 'pk',
            'size' => 'int(11)',
            'topic_in' => 'varchar(255) DEFAULT NULL',
            'topic_probability' => 'int(3) DEFAULT NULL',
            'pol_op_in' => 'varchar(255) DEFAULT NULL',
            'pol_op_probability' => 'int(3) DEFAULT NULL',
            'experiment_id' => 'int(11) NOT NULL'
        ], '');
        $this->addForeignKey('experiment_id', 'experiment_group', 'experiment_id', 'experiment', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200204_163059_experiment_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200204_163059_experiment_group cannot be reverted.\n";

        return false;
    }
    */
}
