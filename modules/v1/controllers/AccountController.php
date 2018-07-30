<?php

namespace app\modules\v1\controllers;

use app\filters\auth\HttpHeaderAuth;
use app\modules\v1\models\Accounts;
use yii\rest\ActiveController;
use Yii;

class AccountController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
                'class' => HttpHeaderAuth::className(),
        ];

        return $behaviors;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['update']);
        return $actions;
    }

    public function actionUpdate($id)
    {
        $raw = json_decode(Yii::$app->request->getRawBody(), true);
        /** @var Accounts $model */
        $model = $this->modelClass::findOne($id);
        $model->changeBalance($raw['balance']);

        echo json_encode($model->getAttributes(['balance']));
    }

    public $modelClass = 'app\modules\v1\models\Accounts';
}
