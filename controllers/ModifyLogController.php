<?php

namespace uzdevid\dashboard\modify\log\controllers;

use Mistralys\Diff\Diff;
use uzdevid\dashboard\base\web\Controller;
use uzdevid\dashboard\modify\log\models\ModifyLog;;
use uzdevid\dashboard\modify\log\models\search\ModifyLogSearch;
use uzdevid\dashboard\widgets\ModalPage\ModalPage;
use uzdevid\dashboard\widgets\ModalPage\ModalPageOptions;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

class ModifyLogController extends Controller {
    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);

        $this->viewPath = '@uzdevid/yii2-dashboard-modify-log/views/modify-log';
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return string
     */
    public function actionIndex(): string {
        $searchModel = new ModifyLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $dataProvider->sort->defaultOrder = [
            'id' => SORT_DESC,
            'modify_time' => SORT_DESC
        ];

        return $this->render('index', compact('dataProvider', 'searchModel'));
    }

    public function actionDiff($id) {
        $model = ModifyLog::findOne($id);

        if ($model == null) {
            throw new NotFoundHttpException(\Yii::t('system.message', 'The requested page does not exist.'));
        }

        $diff = Diff::compareStrings(Html::decode($model->old_value), Html::decode($model->value));

        if ($this->request->isAjax) {
            $modal = ModalPage::options(true, ModalPageOptions::SIZE_XL);
            $view = $this->renderAjax('modal/diff', compact('model', 'diff'));

            return [
                'success' => true,
                'modal' => $modal,
                'body' => [
                    'title' => ModalPage::title($model->model, '<i class="bi bi-person"></i>'),
                    'view' => $view
                ]
            ];
        }

        return $this->render('diff', compact('model', 'diff'));
    }
}
