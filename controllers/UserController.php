<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\SignupForm;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;

class UserController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login',
                'signup',
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        return ['success' => true];
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

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(\Yii::$app->request->post()) && ($user = $model->signup())) {
            $user->clearSecrets();

            return $user;
        }
        \Yii::$app->response->statusCode = 400;

        return [
            'errors' => $model->errors,
        ];
    }
}
