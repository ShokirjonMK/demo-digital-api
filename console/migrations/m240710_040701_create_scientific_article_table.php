<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scientific_article}}`.
 */
class m240710_040701_create_scientific_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'scientific_article';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('scientific_article');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%scientific_article}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('Name of the article'),
            'abstract' => $this->text()->null()->comment('Abstract of the article'),
            'key_words' => $this->text()->null()->comment('Key words'),
            'co_authors' => $this->text()->null()->comment('Co-authors'),
            'journal_name' => $this->string()->null()->comment('Journal name'),
            'journal_country' => $this->string()->null()->comment('Journal country'),
            'user_id' => $this->integer()->notNull()->comment('User ID'),
            'scientific_specialization_id' => $this->integer()->null()->comment('Scientific specialization ID'),
            'specialization' => $this->string()->null()->comment('Specialization'),
            'language_id' => $this->integer()->null()->comment('Language of the article'),
            'issn' => $this->string(255)->null()->comment('ISSN number'),
            'doi' => $this->string(255)->null()->comment('DOI number'),
            'date' => $this->date()->null()->comment('Date of publication'),
            'journal_type' => $this->string()->null()->comment('Type of journal'),
            'kpi_data' => $this->integer()->null()->defaultValue(0)->comment('yes in kpi data'),
            'sdg' => $this->string(255)->null()->comment('SDG data'),
            'kavrtili' => $this->string(255)->null()->comment('Kavrtili data'),
            'type' => $this->integer()->null()->defaultValue(1)->comment('type of the article (1 = milliy oak, 2 = xalqaro jurnali (scopus va wosdan tashqari) chop etilgani, 3 = Scopus va wos-à Ilmiy boshqarmani o’zi kiritadi)'),


            'file' => $this->string(255)->null()->comment('File'),
            'link' => $this->string(255)->null()->comment('link'),


            'order' => $this->tinyInteger(1)->defaultValue(1)->comment('Order of the item'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('Creation timestamp'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('Update timestamp'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who created the record'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who last updated the record'),
            'archived' => $this->integer()->notNull()->defaultValue(0)->comment('Is the item archived (0 = no, 1 = yes)'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-scientific_article-scientific_specialization_id',
            'scientific_article',
            'scientific_specialization_id',
            'scientific_specialization',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-scientific_article-user_id',
            'scientific_article',
            'user_id',
            'users',
            'id',
        );
        $this->addForeignKey(
            'fk-scientific_article-language_id',
            'scientific_article',
            'language_id',
            'languages',
            'id',
        );
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key for table 'scientific_specialization'
        $this->dropForeignKey(
            'fk-scientific_article-scientific_specialization_id',
            'scientific_article'
        );
        $this->dropForeignKey(
            'fk-scientific_article-user_id',
            'scientific_article'
        );
        $this->dropForeignKey(
            'fk-scientific_article-language_id',
            'scientific_article'
        );
        $this->dropTable('{{%scientific_article}}');
    }
}
