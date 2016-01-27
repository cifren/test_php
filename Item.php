<?php

class Item
{

    /**
     *  Item Id
     * 
     * @var int  
     */
    protected $id;

    /**
     * item name
     * @var string
     */
    protected $name;

    /**
     *  Item price before promotion
     * @var float
     */
    protected $price;

    /**
     *  price after promotion
     * @var float
     */
    protected $promotionalPrice = null;

    /**
     *  flag for promotion applied
     * @var bool
     */
    protected $promotionApplied = false;

    public function __construct($id, $name, $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function getPromotionalPrice()
    {
        return $this->promotionalPrice ? $this->promotionalPrice : $this->getPrice();
    }

    public function setPromotionalPrice($promotionalPrice)
    {
        $this->promotionalPrice = $promotionalPrice;
        return $this;
    }

    public function getPromotionApplied()
    {
        return $this->promotionApplied;
    }

    public function setPromotionApplied($promotionApplied)
    {
        $this->promotionApplied = $promotionApplied;
        return $this;
    }

}
