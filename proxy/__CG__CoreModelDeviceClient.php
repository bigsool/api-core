<?php

namespace DoctrineProxies\__CG__\Core\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class DeviceClient extends \Core\Model\DeviceClient implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'reseller', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'clientName', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'clientVersion', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'lastLogin', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'user', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'device');
        }

        return array('__isInitialized__', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'reseller', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'clientName', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'clientVersion', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'lastLogin', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'user', '' . "\0" . 'Core\\Model\\DeviceClient' . "\0" . 'device');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (DeviceClient $proxy) {
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
    public function setReseller($reseller)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setReseller', array($reseller));

        return parent::setReseller($reseller);
    }

    /**
     * {@inheritDoc}
     */
    public function getReseller()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getReseller();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getReseller', array());

        return parent::getReseller();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientName($clientName)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientName', array($clientName));

        return parent::setClientName($clientName);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientName()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getClientName();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientName', array());

        return parent::getClientName();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientVersion($clientVersion)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientVersion', array($clientVersion));

        return parent::setClientVersion($clientVersion);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientVersion()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getClientVersion();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientVersion', array());

        return parent::getClientVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastLogin($lastLogin)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastLogin', array($lastLogin));

        return parent::setLastLogin($lastLogin);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastLogin()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastLogin', array());

        return parent::getLastLogin();
    }

    /**
     * {@inheritDoc}
     */
    public function setUser(\Core\Model\User $user)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUser', array($user));

        return parent::setUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUser', array());

        return parent::getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setDevice(\Core\Model\Device $device)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDevice', array($device));

        return parent::setDevice($device);
    }

    /**
     * {@inheritDoc}
     */
    public function getDevice()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDevice', array());

        return parent::getDevice();
    }

}
