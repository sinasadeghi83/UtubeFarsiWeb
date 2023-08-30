<?php

namespace app\modules\admin;

/**
 * admin module definition class.
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\admin\models\AdminUser',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => 'adminuser', 'httpOnly' => true],
            'idParam' => 'admin_user', // this is important !
        ]);

        \Yii::$app->set('jwt', [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'adminZhI4aQwaCmG5qpZdDstAAPqlSOIHVc17',
        ]);
    }
}
