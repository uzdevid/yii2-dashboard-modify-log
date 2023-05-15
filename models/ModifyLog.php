<?php

namespace uzdevid\dashboard\modify\log\models;

use uzdevid\dashboard\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "modify_log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $model
 * @property string $table
 * @property int $model_id
 * @property string $event
 * @property string $attribute
 * @property string|null $value
 * @property string|null $old_value
 * @property int $modify_time
 *
 * @property User $user
 */
class ModifyLog extends ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'modify_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['model', 'table', 'model_id', 'event', 'attribute'], 'required'],
            [['user_id', 'model_id', 'modify_time'], 'integer'],
            [['value', 'old_value'], 'string'],
            [['modify_time'], 'safe'],
            [['table', 'event', 'attribute'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => Yii::t('system.model', 'ID'),
            'user_id' => Yii::t('system.model', 'User ID'),
            'model_id' => Yii::t('system.model', 'Model ID'),
            'event' => Yii::t('system.model', 'Event'),
            'attribute' => Yii::t('system.model', 'Attribute'),
            'value' => Yii::t('system.model', 'Value'),
            'old_value' => Yii::t('system.model', 'Old Value'),
            'modify_time' => Yii::t('system.model', 'Modify Time'),
        ];
    }

    public function behaviors(): array {
        $behaviors = parent::behaviors();
        $behaviors['TimestampBehavior'] = [
            'class' => TimestampBehavior::class,
            'attributes' => [
                BaseActiveRecord::EVENT_BEFORE_INSERT => ['modify_time'],
            ],
            'value' => time()
        ];

        $behaviors['BlameableBehavior'] = [
            'class' => BlameableBehavior::class,
            'createdByAttribute' => 'user_id',
            'updatedByAttribute' => false,
        ];

        return $behaviors;
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
