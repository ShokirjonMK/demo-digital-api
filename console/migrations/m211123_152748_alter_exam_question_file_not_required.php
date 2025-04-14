<?php

use yii\db\Migration;

/**
 * Class m211123_152748_alter_exam_question_file_not_required
 */
class m211123_152748_alter_exam_question_file_not_required extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {  
        $this->execute("ALTER TABLE `exam_question` CHANGE `file` `file` VARCHAR(255) NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211123_152748_alter_exam_question_file_not_required cannot be reverted.\n";

        return false;
    }

}
