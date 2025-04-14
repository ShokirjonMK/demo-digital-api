<?php

use yii\db\Migration;

/**
 * Class m241127_061252_add_vocation_type_id_to_vocation
 */
class m241127_061252_add_vocation_type_id_to_vocation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%vocation}}', 'vocation_type_id', $this->integer()->null()->comment('Vocation Type ID'));

        // Create index for vocation_type_id
        $this->createIndex(
            'idx-vocation-vocation_type_id',
            '{{%vocation}}',
            'vocation_type_id'
        );

        // Add foreign key
        $this->addForeignKey(
            'fk-vocation-vocation_type_id',
            '{{%vocation}}',
            'vocation_type_id',
            '{{%vocation_type}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key first
        $this->dropForeignKey('fk-vocation-vocation_type_id', '{{%vocation}}');

        // Drop index
        $this->dropIndex('idx-vocation-vocation_type_id', '{{%vocation}}');

        // Drop column
        $this->dropColumn('{{%vocation}}', 'vocation_type_id');
    }
}
