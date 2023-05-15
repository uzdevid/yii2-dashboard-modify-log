<?php

use yii\db\Migration;

/**
 * Class m230502_170808_modify_log
 */
class m230502_170808_modify_log extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void {
        $this->createTable('modify_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'model' => $this->string(255)->notNull(),
            'table' => $this->string(255)->notNull(),
            'model_id' => $this->integer(11)->notNull(),
            'event' => $this->string(255)->notNull(),
            'attribute' => $this->string(255)->notNull(),
            'value' => $this->text()->null()->defaultValue(null),
            'old_value' => $this->text()->null()->defaultValue(null),
            'modify_time' => $this->bigInteger(16)->notNull(),
        ]);

        $this->batchInsert('action', ['path'],
            [
                ['system/modify-log/index'],
                ['system/modify-log/create'],
                ['system/modify-log/view'],
                ['system/modify-log/update'],
                ['system/modify-log/delete']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('modify_log');
        return true;
    }
}
