<?php

namespace app\controllers;

use yii\rest\ActiveController;

class VideoController extends ActiveController
{
    public $modelClass = 'app\models\Video'; // Change this to your Video model class

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }
}
