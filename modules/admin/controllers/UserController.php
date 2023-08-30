<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\LoginForm;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;

/**
 * User controller for the `admin` module.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login',
            ],
        ];

        return $behaviors;
    }

    /**
     * Renders the index view for the module.
     *
     * @return string
     */
    public function actionIndex()
    {
        return [
            'we are in admin' => true,
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && ($token = $model->login())) {
            return [
                'token' => $token,
            ];
        }
        \Yii::$app->response->statusCode = 400;

        return [
            'errors' => $model->errors,
        ];
    }
}
