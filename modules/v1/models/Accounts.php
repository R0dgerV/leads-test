<?php

namespace app\modules\v1\models;

use app\models\Account;

class Accounts extends Account
{
    /**
     * @return array
     */
    public function fields()
    {
        return ['balance', 'id'];
    }
}
