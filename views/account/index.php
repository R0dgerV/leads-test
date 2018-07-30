<?php
/* @var $this yii\web\View */
/* @var $model \app\models\Account */

use yii\helpers\Url;
?>
<h1>Account info</h1>

<p>Name: <?=$model->username;?></p>
<p class="bg-info">X-Api-Key: <?=$model->access_token;?></p>
<p> Посмотреть баланс CURL: <code>curl -X GET <?= Url::toRoute(['/api/v1/account/', 'id' => $model->getPrimaryKey()], true);?>
    -H 'accept: application/json' -H 'x-api-key: dc78eea14d6780783f99307cd51dbc611c7f771f'</code></p>

<p> Изменить баланс CURL PUT: <code>curl -X GET <?= Url::toRoute(['/api/v1/account/', 'id' => $model->getPrimaryKey()], true);?>
        -H 'accept: application/json' -H 'x-api-key: dc78eea14d6780783f99307cd51dbc611c7f771f -d '{"balance":-20}'</code></p>

<a class="btn btn-info" href="<?= Url::toRoute(['/account/edit', 'id' => $model->getPrimaryKey()]);?>">Edit Account</a>