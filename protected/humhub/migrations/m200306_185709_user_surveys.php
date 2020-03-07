<?php

use yii\db\Migration;

/**
 * Class m200306_185709_user_surveys
 */
class m200306_185709_user_surveys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("user_surveys", [
            'id' => 'pk',
            'user_id' => 'int(11) NOT NULL',
            'survey_data' => 'MEDIUMBLOB'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200306_185709_user_surveys cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200306_185709_user_surveys cannot be reverted.\n";

        return false;
    }
    */
}
