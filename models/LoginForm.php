<?php

namespace app\models;

use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property null|User $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            [['username', 'password'], 'trim'],
            [['username'], 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_]{7,29}$/'],
            ['username', 'string', 'length' => [5, 12]],
            ['password', 'string', 'length' => [8]],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            return $user->generateAccessToken();
        }

        return false;
    }

    /**
     * Finds user by [[username]].
     *
     * @return null|User
     */
    public function getUser()
    {
        if (false === $this->_user) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
