<?php

namespace app\models;

use floor12\phone\PhoneValidator;
use yii\base\Model;

/**
 * SignupForm is the model behind the signup form.
 *
 * @property null|User $user
 */
class SignupForm extends Model
{
    public $username;
    public $password;
    public $name;
    public $phone;

    /**
     * @return array the validation rules
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'name', 'phone'], 'required'],
            [['username', 'password', 'name', 'phone'], 'trim'],
            [['username'], 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_]{7,29}$/'],
            ['name', 'match', 'pattern' => "/^[a-z ,.'-]+$/i"],
            ['phone', PhoneValidator::class],
            ['username', 'string', 'length' => [5, 12]],
            ['password', 'string', 'length' => [8]],
        ];
    }

    /**
     * Signs up a user using the provided informations.
     *
     * @return bool whether the user is signed up successfully
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->attributes = $this->attributes;
            $user->validate();
            $user->save();
            $this->addErrors($user->errors);

            return $user;
        }

        return false;
    }
}
