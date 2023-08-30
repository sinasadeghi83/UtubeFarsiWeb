<?php

namespace app\modules\admin\controllers;

use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\rest\Controller;

/**
 * License controller for the `admin` module.
 */
class LicenseController extends ActiveController
{
    public $modelClass = 'app\models\License';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
            ],
        ];

        return $behaviors;
    }
}
