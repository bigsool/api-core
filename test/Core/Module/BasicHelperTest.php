<?php


namespace Core\Module;


use Core\Model\Product;
use Core\Registry;
use Core\TestCase;

class BasicHelperTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();

        self::resetDatabase($ctx);

    }

    public function testCreate () {

        $basicHelper = new BasicHelper();

        $product = $basicHelper->basicSave(new Product, [
            'duration'   => NULL,
            'bundleid'   => 'bundle id',
            'name'       => 'produit',
            'consumable' => true,
            'price'      => 12.5,
            'weight'     => 2,
            'available'  => true,
            'vat'        => 13.5
        ], false);

        $this->assertInstanceOf(Registry::realModelClassName('product'), $product);
        $this->assertNull($product->getId());

        $product = $basicHelper->basicSave('product', [
            'duration'   => NULL,
            'bundleid'   => 'bundle id',
            'name'       => 'produit',
            'consumable' => true,
            'price'      => 12.5,
            'weight'     => 2,
            'available'  => true,
            'vat'        => 13.5
        ]);

        $this->assertInstanceOf(Registry::realModelClassName('product'), $product);
        $this->assertSame(1, $product->getId());

    }

} 