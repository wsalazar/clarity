<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 8/3/14
 * Time: 8:12 PM
 */

namespace Authenticate\Entity\User;

class UserManager {

    /** @var
     **/
    protected $userFactory;

    public function __construct(UserFactory $userFactory)
    {
        $this->$userFactory = $userFactory;
    }

    public function createUser($data)
    {
        echo 'haha';
        $user = $this->userFactory->createUser($data);
    }

} 