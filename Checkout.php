<?php

include __DIR__ . "/CheckoutInterface.php";
include __DIR__ . "/PromotionCalculator.php";

/**
 * scan of items and calculation of total basket price
 */
class Checkout implements CheckoutInterface
{

    /**
     *  List of items part of the checkout
     * 
     * @var array
     */
    protected $checkedItems = array();

    /**
     *  Promotional rules
     * 
     * @var array
     */
    protected $promotionalRules;

    /**
     * Create new checkout instance based on promotional rules
     *
     * @param object $promotionalRules
     */
    public function __construct($promotionalRules)
    {
        $this->promotionalRules = $promotionalRules;
    }

    /**
     * Scan new item
     *
     * @param object $item
     */
    public function scan($item)
    {
        $this->checkedItems[] = $item;
    }

    /**
     * Calculate total price of the basket including promotions
     * Returns price string in the £X.XX format  
     *
     * @return string 
     */
    public function total()
    {
        $total = $this->applyPromotions($this->checkedItems);

        return $total;
    }

    /**
     * Apply promotions prices on each items and get the total of the checkout
     * 
     * @param array     $checkedItems   items scanned
     * @return float    Total of the checkout
     */
    protected function applyPromotions($checkedItems)
    {
        $promotionCalculator = new PromotionCalculator($this->promotionalRules);

        return $promotionCalculator->getTotal($checkedItems);
    }

}
