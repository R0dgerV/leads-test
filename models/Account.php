<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use Exception;
use yii\web\HttpException;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $username
 * @property string $link
 * @property string $access_token
 * @property int $balance
 * @property string $created_at
 * @property string $updated_at
 * @property int $flags
 */
class Account extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'filter', 'filter' => 'strtolower'],
            [['username'], 'required'],
            [['username'], 'string', 'max' => 128],
            [['username'], 'email', 'checkDNS' => true, 'enableIDN' => false],
            [['balance', 'flags'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @param bool $rememberMe
     * @return bool
     */
    public function login($rememberMe = true) {
        return Yii::$app->user->login($this, $rememberMe ? 3600*24*30 : 0);
    }

    /**
     * @void
     */
    public function generateLink() {
        $this->link = $this->createLink();
    }

    /**
     * @void
     */
    public function generateAccessToken() {
        $this->access_token = $this->createAccessToken();
    }

    /**
     * @return string
     */
    protected function createAccessToken()
    {
        return sha1(md5(microtime() . $this->username) . Yii::$app->params['salt']);
    }

    /**
     * @return string
     */
    protected function createLink()
    {
        return md5(sha1(microtime() . $this->username));
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $account = Account::findOne($id);

        return !empty($account) ? new static($account) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $account = Account::find()->where(['access_token' => $token])->one();

        return !empty($account) ? new static($account) : null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $account = Account::find()->where(['username' => $username]);

        return !empty($account) ? new static($account) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->access_token;
    }

    /**
     * @param float $summ
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function changeBalance($summ)
    {
        $summ = floatval($summ);

        if (empty($summ)) {
            throw new HttpException(400, 'The balance can not be changed to 0');
        }

        $this->balance += $summ;
        if ($this->balance < 0) {
            throw new HttpException(400, 'You do not have enough money in your account');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new Transactions();
            $model->amount = $summ;
            $model->account_id = $this->getPrimaryKey();

            if ($model->save()) {
                $this->save();
                $transaction->commit();
            }
        } catch(Exception $e) {
            $transaction->rollback();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->access_token === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'link' => 'Link',
            'access_token' => 'Access Token',
            'balance' => 'Balance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'flags' => 'Flags',
        ];
    }
}
