<?php

namespace app\controllers;

use app\models\License;
use app\models\Transaction;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\data\ActiveDataProvider;
use yii\rest\Action;
use yii\rest\ActiveController;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;

class LicenseController extends ActiveController
{
    public $modelClass = 'app\models\License';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'index',
                'view',
                'options',
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel'] = static function ($id, Action $action) {
            $modelClass = $action->modelClass;
            $model = $modelClass::find()
                ->where(['id' => $id])
                ->andWhere(['status' => 1])
                ->one()
            ;
            if (null === $model) {
                throw new NotFoundHttpException("Object not found: {$id}");
            }

            return $model;
        };

        return $actions;
    }

    public function actionBuy($id, $payid)
    {
        $user = \Yii::$app->user->identity;
        if ($user->activeLicense()) {
            throw new ConflictHttpException('You already has an active license!');
        }
        $transaction = Transaction::retrieveFromPayId($payid);
        $transaction->validateTransaction($user);

        if (!is_array($errors = $user->addLicense($id, $payid))) {
            $userLicense = $errors;

            return [
                'license' => License::findOne(['id' => $id]),
                'created_at' => $userLicense->created_at,
            ];
        }

        \Yii::$app->response->statusCode = 400;

        return [
            'errors' => $errors,
        ];
    }

    /**
     * @return object|\yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $query = License::find()->where(['status' => 1]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'length' => SORT_DESC,
                    'title' => SORT_ASC,
                ],
            ],
        ]);
    }
}
