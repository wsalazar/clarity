<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 8/3/14
 * Time: 7:50 PM
 */

namespace Authenticate\Entity\User;

use Authenticate\Entity\User;

class UserFactory {

    public function createUser(array $params)
    {
        return new User(
            isset($params['userid']) ?$params['userid'] : null,
            $params['firstName'],
            $params['lastName'],
            $params['username'],
            $params['email'],
            $params['password'],
            $params['role'],
            $params['datecreated']
        );
    }
} 