<?php

namespace DoctrineProxies\__CG__\Core\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Contact extends \Core\Model\Contact implements \Doctrine\ORM\Proxy\Proxy {

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
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'id',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'label',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'streets',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'city',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'state',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'zip',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'country',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'email',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'mobile',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'landLine',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'abcompanyContact',
                         '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'abpersonContact'
            );
        }

        return array('__isInitialized__',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'id',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'label',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'streets',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'city',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'state',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'zip',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'country',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'email',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'mobile',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'landLine',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'abcompanyContact',
                     '' . "\0" . 'Core\\Model\\Contact' . "\0" . 'abpersonContact'
        );
    }

    /**
     *
     */
    public function __wakeup () {

        if (!$this->__isInitialized__) {
            $this->__initializer__ = function (Contact $proxy) {

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
    public function getAbcompanyContact () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAbcompanyContact', array());

        return parent::getAbcompanyContact();
    }

    /**
     * {@inheritDoc}
     */
    public function getAbpersonContact () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAbpersonContact', array());

        return parent::getAbpersonContact();
    }

    /**
     * {@inheritDoc}
     */
    public function getCity () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCity', array());

        return parent::getCity();
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountry', array());

        return parent::getCountry();
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
    public function getLabel () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLabel', array());

        return parent::getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getLandLine () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLandLine', array());

        return parent::getLandLine();
    }

    /**
     * {@inheritDoc}
     */
    public function getMobile () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMobile', array());

        return parent::getMobile();
    }

    /**
     * {@inheritDoc}
     */
    public function getState () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getState', array());

        return parent::getState();
    }

    /**
     * {@inheritDoc}
     */
    public function getStreets () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStreets', array());

        return parent::getStreets();
    }

    /**
     * {@inheritDoc}
     */
    public function getZip () {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getZip', array());

        return parent::getZip();
    }

    /**
     * {@inheritDoc}
     */
    public function setAbcompanyContact (\Core\Model\ABCompanyContact $abcompanyContact = NULL) {

        $this->__initializer__
        && $this->__initializer__->__invoke($this, 'setAbcompanyContact', array($abcompanyContact));

        return parent::setAbcompanyContact($abcompanyContact);
    }

    /**
     * {@inheritDoc}
     */
    public function setAbpersonContact (\Core\Model\ABPersonContact $abpersonContact = NULL) {

        $this->__initializer__
        && $this->__initializer__->__invoke($this, 'setAbpersonContact', array($abpersonContact));

        return parent::setAbpersonContact($abpersonContact);
    }

    /**
     * {@inheritDoc}
     */
    public function setCity ($city) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCity', array($city));

        return parent::setCity($city);
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry ($country) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountry', array($country));

        return parent::setCountry($country);
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
    public function setLabel ($label) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLabel', array($label));

        return parent::setLabel($label);
    }

    /**
     * {@inheritDoc}
     */
    public function setLandLine ($landLine) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLandLine', array($landLine));

        return parent::setLandLine($landLine);
    }

    /**
     * {@inheritDoc}
     */
    public function setMobile ($mobile) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMobile', array($mobile));

        return parent::setMobile($mobile);
    }

    /**
     * {@inheritDoc}
     */
    public function setState ($state) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setState', array($state));

        return parent::setState($state);
    }

    /**
     * {@inheritDoc}
     */
    public function setStreets ($streets) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStreets', array($streets));

        return parent::setStreets($streets);
    }

    /**
     * {@inheritDoc}
     */
    public function setZip ($zip) {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setZip', array($zip));

        return parent::setZip($zip);
    }

}
