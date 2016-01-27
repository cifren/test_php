<?php

include __DIR__ . '/Item.php';

/**
 * Description of Items
 *
 * @author cifren
 */
class ItemRepository
{

    protected $itemDatabase = array(
        '001' => array('name' => 'Lavender heart', 'price' => '9.25'),
        '002' => array('name' => 'Personalised cufflinks', 'price' => '45.00'),
        '003' => array('name' => 'Kids T-shirt', 'price' => '19.95'),
    );
    protected $items = array();

    public function __construct()
    {
        foreach ($this->itemDatabase as $key => $item) {
            $this->items[$key] = new Item($key, $item['name'], $item['price']);
        }
    }

    public function find($id)
    {        
        //TODO: error if not found
        return $this->items[$id];
    }

    public function findAll()
    {
        return $this->items;
    }

}
