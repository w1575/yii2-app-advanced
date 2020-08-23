<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%article}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%creator}}`
 */
class m200823_173821_create_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->comment('Заголовок'),
            'description' => $this->string(512)->comment('Описание'),
            'content' => $this->text()->comment('Контент'),
            'creator_id' => $this->integer()->comment('Создатель'),
        ]);

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-article-creator_id}}',
            '{{%article}}',
            'creator_id'
        );

        // add foreign key for table `{{%creator}}`
        $this->addForeignKey(
            '{{%fk-article-creator_id}}',
            '{{%article}}',
            'creator_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%creator}}`
        $this->dropForeignKey(
            '{{%fk-article-creator_id}}',
            '{{%article}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-article-creator_id}}',
            '{{%article}}'
        );

        $this->dropTable('{{%article}}');
    }
}
