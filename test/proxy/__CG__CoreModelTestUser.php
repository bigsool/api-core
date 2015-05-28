<?php

namespace DoctrineProxies\__CG__\Core\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class TestUser extends \Core\Model\TestUser implements \Doctrine\ORM\Proxy\Proxy {

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();

    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct ($initializer = NULL, $cloner = NULL) {

        $this->__initializer__ = $initializer;
        $this->__cloner__ = $cloner;
    }

    /**
     *
     * @return array
     */
    public function __sleep () {

        if ($this->__isInitialized__) {
            return array('__isInitialized__',
                         'findQueryContext',
                         'id',
                         'email',
                         'password',
                         'name',
                         'firstname',
                         'lang',
                         'salt',
                         'registerDate',
                         'lastLoginDate',
                         'knowsFrom',
                         'confirmationKey',
                         'ownedCompany',
                         'ownedCompanyRestrictedId',
                         'isOwnedCompanyFaulted',
                         'storage',
                         'storageRestrictedId',
                         'isStorageFaulted',
                         'company',
                         'companyRestrictedId',
                         'isCompanyFaulted'
            );
        }

        return array('__isInitialized__',
                     'findQueryContext',
                     'id',
                     'email',
                     'password',
                     'name',
                     'firstname',
                     'lang',
                     'salt',
                     'registerDate',
                     'lastLoginDate',
                     'knowsFrom',
                     'confirmationKey',
                     'ownedCompany',
                     'ownedCompanyRestrictedId',
                     'isOwnedCompanyFaulted',
                     'storage',
                     'storageRestrictedId',
                     'isStorageFaulted',
                     'company',
                     'companyRestrictedId',
                     'isCompanyFaulted'
        );
    }

    /**
     *
     */
    public function __wakeup () {

        if (!$this->__isInitialized__) {
            $this->__initializer__ = function (TestUser $proxy) {

                $proxy->__setInitializer(NULL);
                $proxy->__setCloner(NULL);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if (!array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     *
     */
    public function __clone () {

        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner () {

        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner (\Closure $cloner = NULL) {

        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer () {

        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer (\Closure $initializer = NULL) {

        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties () {

        return self::$lazyPropertiesDefaults;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized () {

        return $this->__isInitialized__;
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized ($initialized) {

        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     */
    public function getCompany () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCompany', array());

        return parent::getCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function getConfirmationKey () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getConfirmationKey', array());

        return parent::getConfirmationKey();
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', array());

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstname () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFirstname', array());

        return parent::getFirstname();
    }

    /**
     * {@inheritDoc}
     */
    public function getId () {

        if ($this->__isInitialized__ === false) {
            return (int)parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getKnowsFrom () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getKnowsFrom', array());

        return parent::getKnowsFrom();
    }

    /**
     * {@inheritDoc}
     */
    public function getLang () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLang', array());

        return parent::getLang();
    }

    /**
     * {@inheritDoc}
     */
    public function getLastLoginDate () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastLoginDate', array());

        return parent::getLastLoginDate();
    }

    /**
     * {@inheritDoc}
     */
    public function getName () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getOwnedCompany () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwnedCompany', array());

        return parent::getOwnedCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPassword', array());

        return parent::getPassword();
    }

    /**
     * {@inheritDoc}
     */
    public function getRegisterDate () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRegisterDate', array());

        return parent::getRegisterDate();
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSalt', array());

        return parent::getSalt();
    }

    /**
     * {@inheritDoc}
     */
    public function getStorage () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStorage', array());

        return parent::getStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function getUnrestrictedCompany () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUnrestrictedCompany', array());

        return parent::getUnrestrictedCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function getUnrestrictedOwnedCompany () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUnrestrictedOwnedCompany', array());

        return parent::getUnrestrictedOwnedCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function getUnrestrictedStorage () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUnrestrictedStorage', array());

        return parent::getUnrestrictedStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function setCompany (\Core\Model\TestCompany $company = NULL) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCompany', array($company));

        return parent::setCompany($company);
    }

    /**
     * {@inheritDoc}
     */
    public function setConfirmationKey ($confirmationKey) {

        $this->__initializer__
        && $this->__initializer__->__invoke($this, 'setConfirmationKey', array($confirmationKey));

        return parent::setConfirmationKey($confirmationKey);
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail ($email) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmail', array($email));

        return parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstname ($firstname) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFirstname', array($firstname));

        return parent::setFirstname($firstname);
    }

    /**
     * {@inheritDoc}
     */
    public function setKnowsFrom ($knowsFrom) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setKnowsFrom', array($knowsFrom));

        return parent::setKnowsFrom($knowsFrom);
    }

    /**
     * {@inheritDoc}
     */
    public function setLang ($lang) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLang', array($lang));

        return parent::setLang($lang);
    }

    /**
     * {@inheritDoc}
     */
    public function setLastLoginDate ($lastLoginDate) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastLoginDate', array($lastLoginDate));

        return parent::setLastLoginDate($lastLoginDate);
    }

    /**
     * {@inheritDoc}
     */
    public function setName ($name) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setOwnedCompany (\Core\Model\TestCompany $ownedCompany = NULL) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOwnedCompany', array($ownedCompany));

        return parent::setOwnedCompany($ownedCompany);
    }

    /**
     * {@inheritDoc}
     */
    public function setPassword ($password) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPassword', array($password));

        return parent::setPassword($password);
    }

    /**
     * {@inheritDoc}
     */
    public function setRegisterDate ($registerDate) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRegisterDate', array($registerDate));

        return parent::setRegisterDate($registerDate);
    }

    /**
     * {@inheritDoc}
     */
    public function setSalt ($salt) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSalt', array($salt));

        return parent::setSalt($salt);
    }

    /**
     * {@inheritDoc}
     */
    public function setStorage (\Core\Model\TestStorage $storage = NULL) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStorage', array($storage));

        return parent::setStorage($storage);
    }

}
