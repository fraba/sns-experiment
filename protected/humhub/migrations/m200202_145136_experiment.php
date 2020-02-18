<?php

use yii\db\Migration;

/**
 * Class m200202_145136_experiment
 */
class m200202_145136_experiment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('experiment', [
            'id' => 'pk',
            'start' => 'datetime',
            'end' => 'datetime',
        ], '');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200202_145136_experiment cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200202_145136_experiment cannot be reverted.\n";

        return false;
    }
    */
}
