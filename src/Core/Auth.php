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
     * @var Credential|null
     */
    protected $superUserCredential;

    /**
     * @var string[]
     */
    protected $rights = [];

    /**
     * @param mixed $credential
     * @param mixed $superUserCredential
     */
    public function __construct ($credential = NULL, $superUserCredential = NULL) {

        $this->rights[] = self::GUEST;

        $this->setCredential($credential);
        $this->setSuperUserCredential($superUserCredential);

    }

    /**
     * @return Auth
     */
    public static function createInternalAuth () {

        $auth = new static;
        $auth->rights[] = static::ROOT;
        $auth->rights[] = static::INTERNAL;

        return $auth;

    }

    /**
     * @return Credential|null
     */
    public function getCredential () {

        return $this->credential;

    }

    /**
     * @param Credential $credential
     *
     * @return Auth
     */
    public function setCredential ($credential) {

        $this->credential = $credential;
        if (is_null($credential)) {
            $key = array_search(self::AUTHENTICATED, $this->rights);
            if ($key !== false) {
                unset($this->rights[$key]);
            }
        }
        else {
            $this->rights[] = self::AUTHENTICATED;
        }

        return $this;

    }

    /**
     * @return Credential|null
     */
    public function getSuperUserCredential () {

        return $this->superUserCredential;

    }

    /**
     * @param Credential $credential
     *
     * @return Auth
     */
    public function setSuperUserCredential ($credential) {

        $this->superUserCredential = $credential;

        return $this;

    }

    /**
     * @return bool
     */
    public function isLoggedAs (): bool {
        return !!$this->getSuperUserCredential();
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

    /**
     * @return Auth
     */
    public function addRootRight () {

        $this->rights[] = static::ROOT;

        return $this;

    }

}