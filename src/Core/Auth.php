<?php


namespace Core;


use Core\Model\Credential;

class Auth {

    const GUEST = 'GUEST';

    const AUTHENTICATED = 'AUTHENTICATED';

    const ROOT = 'ROOT';

    const INTERNAL = 'INTERNAL';

    /**
     * @var Credential|null
     */
    protected $credential;

    /**
     * @var string[]
     */
    protected $rights = [];

    /**
     * @return Auth
     */
    public static function createInternalAuth() {

        $auth = new static;
        $auth->rights[] = static::ROOT;
        $auth->rights[] = static::INTERNAL;

        return $auth;

    }

    /**
     * @param mixed $credential
     */
    public function __construct ($credential = NULL) {

        $this->rights[] = self::GUEST;

        $this->setCredential($credential);

    }

    /**
     * @return Credential|null
     */
    public function getCredential () {

        return $this->credential;
    }

    /**
     * @param Credential $credential
     */
    public function setCredential ($credential) {

        $this->credential = $credential;
        $this->rights[] = self::AUTHENTICATED;

    }

    /**
     * @param string|string[] $rights
     *
     * @return bool
     */
    public function hasRights ($rights) {

        return self::staticHasRights($this->rights, $rights);

    }

    /**
     * @param string|string[] $currentRights
     * @param string|string[] $minRights
     *
     * @return bool
     */
    public static function staticHasRights ($currentRights, $minRights) {

        return 0 == count(array_diff((array)$minRights, (array)$currentRights));

    }

} 