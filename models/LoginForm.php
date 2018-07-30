<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var bool
     */
    public $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'strtolower'],
            [['email'], 'required'],
            [['email'], 'email', 'checkDNS' => true, 'enableIDN' => false],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
/*
    public function validateLink($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
*/

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function sendEmail()
    {
        if ($this->validate()) {
            $account = Account::find()->where(['username' => $this->email])->one();

            if (!$account) {
                $account = new Account();
                $account->username = $this->email;
                $account->generateAccessToken();
            }

            $account->generateLink();

            if ($account->save()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($account->getIsNewRecord()) {
                        $account->changeBalance(1000);
                    }

                    Yii::$app->mailer->compose()
                        ->setTo($account->username)
                        ->setFrom([ 'leads@rodger.pw'=> 'Admin Site'])
                        ->setHtmlBody('You ' . Html::a('Link', Url::toRoute([
                                'site/login',
                                'email' => $account->username,
                                'link' => $account->link,
                                true
                            ], true)) . ' to login')
                        ->setTextBody('You ' . Url::toRoute([
                                'site/login',
                                'email' => $account->username,
                                'link' => $account->link
                            ], true) . ' to login')
                        ->send();
                    $transaction->commit();

                    return true;
                } catch(Exception $e) {
                    $transaction->rollback();
                }
            } else {
                new \Exception('Account not created');
            }
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user =1;
        }

        return $this->_user;
    }
}
