<?php

use yii\db\Migration;

/**
 * Class m200404_170834_user_surveys_survey_id
 */
class m200404_170834_user_surveys_survey_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_surveys', "survey_id", "string not null");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200404_170834_user_surveys_survey_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200404_170834_user_surveys_survey_id cannot be reverted.\n";

        return false;
    }
    */
}
