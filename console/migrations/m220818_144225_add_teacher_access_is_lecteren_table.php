<?php

use yii\db\Migration;

/**
 * Class m220818_144225_add_teacher_access_is_lecteren_table
 */
class m220818_144225_add_teacher_access_is_lecteren_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('teacher_access', 'is_lecture', $this->tinyInteger(1)->defaultValue(0)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220818_144225_add_teacher_access_is_lecteren_table cannot be reverted.\n";

        return false;
    }

}
