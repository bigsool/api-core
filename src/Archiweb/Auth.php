<?php


namespace Archiweb;


class Auth {

    const GUEST = 'GUEST';

    const AUTHENTICATED = 'AUTHENTICATED';

    // Could be CREATE_PROJECT, SHARE_PROJECT ...

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

    /**
     * @param string|string[] $rights
     *
     * @return bool
     */
    public function hasRights ($rights) {

        return 0 == count(array_diff((array)$rights, $this->rights));

    }

} 