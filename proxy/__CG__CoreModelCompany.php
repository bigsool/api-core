<?php

namespace DoctrineProxies\__CG__\Core\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Company extends \Core\Model\Company implements \Doctrine\ORM\Proxy\Proxy
{
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
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'id', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'name', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'address', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'zipCode', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'city', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'state', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'country', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'tel', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'fax', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'tva', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'owner', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'storage', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'users');
        }

        return array('__isInitialized__', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'id', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'name', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'address', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'zipCode', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'city', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'state', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'country', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'tel', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'fax', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'tva', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'owner', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'storage', '' . "\0" . 'Core\\Model\\Company' . "\0" . 'users');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Company $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddress', array());

        return parent::getAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddress', array($address));

        return parent::setAddress($address);
    }

    /**
     * {@inheritDoc}
     */
    public function getZipCode()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getZipCode', array());

        return parent::getZipCode();
    }

    /**
     * {@inheritDoc}
     */
    public function setZipCode($zipCode)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setZipCode', array($zipCode));

        return parent::setZipCode($zipCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getCity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCity', array());

        return parent::getCity();
    }

    /**
     * {@inheritDoc}
     */
    public function setCity($city)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCity', array($city));

        return parent::setCity($city);
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getState', array());

        return parent::getState();
    }

    /**
     * {@inheritDoc}
     */
    public function setState($state)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setState', array($state));

        return parent::setState($state);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountry', array());

        return parent::getCountry();
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry($country)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountry', array($country));

        return parent::setCountry($country);
    }

    /**
     * {@inheritDoc}
     */
    public function getTel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTel', array());

        return parent::getTel();
    }

    /**
     * {@inheritDoc}
     */
    public function setTel($tel)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTel', array($tel));

        return parent::setTel($tel);
    }

    /**
     * {@inheritDoc}
     */
    public function getFax()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFax', array());

        return parent::getFax();
    }

    /**
     * {@inheritDoc}
     */
    public function setFax($fax)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFax', array($fax));

        return parent::setFax($fax);
    }

    /**
     * {@inheritDoc}
     */
    public function getTva()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTva', array());

        return parent::getTva();
    }

    /**
     * {@inheritDoc}
     */
    public function setTva($tva)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTva', array($tva));

        return parent::setTva($tva);
    }

    /**
     * {@inheritDoc}
     */
    public function getOwner()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwner', array());

        return parent::getOwner();
    }

    /**
     * {@inheritDoc}
     */
    public function setOwner(\Core\Model\User $owner = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOwner', array($owner));

        return parent::setOwner($owner);
    }

    /**
     * {@inheritDoc}
     */
    public function getStorage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStorage', array());

        return parent::getStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function setStorage(\Core\Model\Storage $storage = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStorage', array($storage));

        return parent::setStorage($storage);
    }

    /**
     * {@inheritDoc}
     */
    public function addUser(\Core\Model\User $users)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addUser', array($users));

        return parent::addUser($users);
    }

    /**
     * {@inheritDoc}
     */
    public function removeUser(\Core\Model\User $users)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeUser', array($users));

        return parent::removeUser($users);
    }

    /**
     * {@inheritDoc}
     */
    public function getUsers()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUsers', array());

        return parent::getUsers();
    }

}
