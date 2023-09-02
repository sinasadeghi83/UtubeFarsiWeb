<?php

namespace app\models;

use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "user".
 *
 * @property int           $id
 * @property null|string   $username
 * @property null|string   $name
 * @property string        $password
 * @property string        $phone
 * @property License[]     $licenses
 * @property UserLicense[] $userLicenses
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['password', 'phone'], 'required'],
            [['username'], 'string', 'max' => 12],
            [['username'], 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_]{7,29}$/'],
            ['name', 'match', 'pattern' => "/^[a-z ,.'-]+$/i"],
            [['name'], 'string', 'max' => 100],
            ['password', 'string', 'max' => 255],
            [['phone'], 'string', 'length' => 11],
            [['username', 'phone'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'name' => 'Name',
            'password' => 'Password',
            'phone' => 'Phone',
        ];
    }

    /**
     * Gets query for [[Licenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLicenses()
    {
        return $this->hasMany(License::class, ['id' => 'license_id'])->viaTable('user_license', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserLicenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserLicenses()
    {
        return $this->hasMany(UserLicense::class, ['user_id' => 'id']);
    }

    public function activeLicense()
    {
        return License::find()
            ->select('license.*')
            ->leftJoin('user_license', 'user_license.license_id = license.id')
            ->where(['user_license.user_id' => $this->id])
            ->andWhere('(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(user_license.created_at)) < license.length')
            ->one()
        ;
    }

    public function addLicense($license_id, $payid)
    {
        $license = License::findOne(['id' => $license_id, 'status' => 1]);
        if (!$license) {
            throw new NotFoundHttpException("There's no active license with this id!");
        }
        $userLicense = new UserLicense();
        $userLicense->user_id = $this->id;
        $userLicense->license_id = $license_id;
        $userLicense->payment_id = $payid;

        $result = $userLicense->save();

        if (!$result) {
            return $userLicense->errors;
        }

        $userLicense->refresh();

        return $userLicense;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['id' => (string) $token->getClaim('uid')]);
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     *
     * @return null|static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string current user auth key
     */
    public function getAuthKey()
    {
        return null;
    }

    public function generateAccessToken()
    {
        // here you can put some credentials validation logic
        // so if it success we return token
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();

        /** @var Jwt $jwt */
        $jwt = \Yii::$app->jwt;
        $token = $jwt->getBuilder()
            ->setIssuer(Url::base(true), false)// Configures the issuer (iss claim)
            ->setAudience(Url::base(true), false)// Configures the audience (aud claim)
            // ->setId('4f1g23a12aa', true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + 60 * 60 * 12)// Configures the expiration time of the token (exp claim)
            ->set('uid', $this->id)// Configures a new claim, called "uid"
            ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken() // Retrieves the generated token
        ;

        return (string) $token;
    }

    /**
     * @param string $authKey
     *
     * @return null|bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function validatePassword($password)
    {
        return \Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function clearSecrets()
    {
        $this->password = '';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
            }

            return true;
        }

        return false;
    }
}
