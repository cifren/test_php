<?php

include __DIR__ . "/../CheckoutFactory.php";
include __DIR__ . "/../ItemRepository.php";

class CheckoutTest extends PHPUnit_Framework_TestCase
{

    private $itemRepository;

    public function testCheckout1()
    {
        $ids = ["001", "002", "003"];
        $co = CheckoutFactory::create();

        //scan all articles based on the selection
        foreach ($ids as $id) {
            //calls the item object
            $item = $this->getItemRepository()->find($id);
            $co->scan($item);
        }
        $price = $co->total();
        $this->assertEquals(66.78, $price);
    }

    public function testCheckout2()
    {
        $ids = ["001", "003", "001"];
        $co = CheckoutFactory::create();

        //scan all articles based on the selection
        foreach ($ids as $id) {
            //calls the item object
            $item = $this->getItemRepository()->find($id);
            $co->scan($item);
        }
        $price = $co->total();
        $this->assertEquals(36.95, $price);
    }

    public function testCheckout3()
    {
        $ids = ["001", "002", "001", "003"];
        $co = CheckoutFactory::create();

        //scan all articles based on the selection
        foreach ($ids as $id) {
            //calls the item object
            $item = $this->getItemRepository()->find($id);
            $co->scan($item);
        }
        $price = $co->total();
        $this->assertEquals(73.76, $price);
    }

    private function getItemRepository()
    {
        if (!isset($this->itemRepository)) {
            $this->itemRepository = new ItemRepository();
        }
        return $this->itemRepository;
    }

}
