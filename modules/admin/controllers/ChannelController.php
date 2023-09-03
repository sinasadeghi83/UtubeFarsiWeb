<?php

namespace app\modules\admin\controllers;

use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;

/**
 * Channel controller for the `admin` module.
 */
class ChannelController extends ActiveController
{
    public $modelClass = 'app\models\Channel';

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
