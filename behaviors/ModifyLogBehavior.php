<?php

namespace uzdevid\dashboard\modify\log\behaviors;

use uzdevid\dashboard\base\db\ActiveRecord;
use uzdevid\dashboard\modify\log\models\ModifyLog;
use yii\base\Behavior;

class ModifyLogBehavior extends Behavior {
    public bool $afterInsert = true;
    public bool $afterDelete = true;
    public bool $afterUpdate = true;
    public array $attributes = [];
    protected array $oldAttributes = [];

    public function init(): void {
        if (empty($this->attributes)) {
            $this->attributes = array_keys($this->owner->attributes);
        }

        parent::init();
    }

    public function events(): array {
        $events = [];

        if ($this->afterInsert) {
            $events[ActiveRecord::EVENT_AFTER_INSERT] = 'afterInsert';
        }

        if ($this->afterDelete) {
            $events[ActiveRecord::EVENT_AFTER_DELETE] = 'afterDelete';
        }

        if ($this->afterUpdate) {
            $events[ActiveRecord::EVENT_AFTER_UPDATE] = 'afterUpdate';
        }

        $events[ActiveRecord::EVENT_BEFORE_UPDATE] = 'beforeUpdate';

        return $events;
    }

    public function afterInsert(): void {
        $this->fixAttributes('afterInsert');
    }

    public function afterDelete(): void {
        $this->fixAttributes('afterDelete');
    }

    public function afterUpdate(): void {
        foreach ($this->attributes as $attribute) {
            if ($this->oldAttributes[$attribute] != $this->owner->attributes[$attribute]) {
                $model = new ModifyLog();
                $model->model = $this->owner->className();
                $model->table = $this->owner->tableName();
                $model->model_id = $this->owner->id;
                $model->event = 'afterUpdate';
                $model->attribute = $attribute;
                $model->value = (string)$this->owner->attributes[$attribute];
                $model->old_value = (string)$this->oldAttributes[$attribute];
                $model->save();
            }
        }
    }

    public function beforeUpdate(): void {
        $this->oldAttributes = $this->owner->oldAttributes;
    }

    protected function fixAttributes(string $event): void {
        $model = new ModifyLog();
        $model->model = $this->owner->className();
        $model->table = $this->owner->tableName();
        $model->model_id = $this->owner->id;
        $model->event = $event;
        $model->attribute = 'self';
        $model->value = json_encode($this->owner->attributes, JSON_UNESCAPED_UNICODE);
        $model->old_value = json_encode($this->owner->attributes, JSON_UNESCAPED_UNICODE);
        $model->save();
    }
}