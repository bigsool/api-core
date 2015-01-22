<?php

namespace DoctrineProxies\__CG__\Core\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class PurchasedProduct extends \Core\Model\PurchasedProduct implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'id', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'realPrice', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'quantity', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'durationfactor', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'enddate', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'transaction', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'product');
        }

        return array('__isInitialized__', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'id', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'realPrice', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'quantity', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'durationfactor', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'enddate', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'transaction', '' . "\0" . 'Core\\Model\\PurchasedProduct' . "\0" . 'product');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (PurchasedProduct $proxy) {
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
    public function setRealPrice($realPrice)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRealPrice', array($realPrice));

        return parent::setRealPrice($realPrice);
    }

    /**
     * {@inheritDoc}
     */
    public function getRealPrice()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRealPrice', array());

        return parent::getRealPrice();
    }

    /**
     * {@inheritDoc}
     */
    public function setQuantity($quantity)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setQuantity', array($quantity));

        return parent::setQuantity($quantity);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuantity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getQuantity', array());

        return parent::getQuantity();
    }

    /**
     * {@inheritDoc}
     */
    public function setDurationfactor($durationfactor)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDurationfactor', array($durationfactor));

        return parent::setDurationfactor($durationfactor);
    }

    /**
     * {@inheritDoc}
     */
    public function getDurationfactor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDurationfactor', array());

        return parent::getDurationfactor();
    }

    /**
     * {@inheritDoc}
     */
    public function setEnddate($enddate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEnddate', array($enddate));

        return parent::setEnddate($enddate);
    }

    /**
     * {@inheritDoc}
     */
    public function getEnddate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEnddate', array());

        return parent::getEnddate();
    }

    /**
     * {@inheritDoc}
     */
    public function setTransaction(\Core\Model\Transaction $transaction)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTransaction', array($transaction));

        return parent::setTransaction($transaction);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransaction()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTransaction', array());

        return parent::getTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function setProduct(\Core\Model\Product $product)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProduct', array($product));

        return parent::setProduct($product);
    }

    /**
     * {@inheritDoc}
     */
    public function getProduct()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProduct', array());

        return parent::getProduct();
    }

}
