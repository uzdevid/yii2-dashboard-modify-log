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

        $tableName = Yii::$app->db->tablePrefix . 'action';

        if (Yii::$app->db->getTableSchema($tableName, true) === null) {
            $this->batchInsert('action', ['id', 'action'],
                [
                    [10701, 'system/modify-log/index'],
                    [10702, 'system/modify-log/create'],
                    [10703, 'system/modify-log/update'],
                    [10704, 'system/modify-log/view'],
                    [10705, 'system/modify-log/delete']
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('modify_log');
        return true;
    }
}
