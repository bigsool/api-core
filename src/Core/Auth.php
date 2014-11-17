<?php


namespace Core;


use Core\Model\User;

class Auth {

    const GUEST = 'GUEST';

    const AUTHENTICATED = 'AUTHENTICATED';

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string[]
     */
    protected $rights = [];

    /**
     *
     */
    public function __construct () {

        $this->rights[] = self::GUEST;

    }

    // Could be CREATE_PROJECT, SHARE_PROJECT ...

    /**
     * @return User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser (User $user) {

        $this->user = $user;
        $this->rights[] = self::AUTHENTICATED;
    }

    /**
     * @param string|string[] $rights
     *
     * @return bool
     */
    public function hasRights ($rights) {

        return 0 == count(array_diff((array)$rights, $this->rights));

    }

} 