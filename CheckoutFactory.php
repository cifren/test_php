<?php

include __DIR__ . "/CheckoutFactoryInterface.php";
include __DIR__ . "/Checkout.php";

/**
 * creation of the new checkout object
 */
class CheckoutFactory implements CheckoutFactoryInterface
{

    /**
     * Create new checkout object
     *
     * @return CheckoutInterface
     */
    public static function create()
    {
        return new Checkout(self::getPromotionalRules());
    }

    /**
     * Define rules for promotional events, based on an array
     * 
     * @return array
     */
    protected function getPromotionalRules()
    {
        //can be used in a YAML file, or XML or other storage system
        $rules = array(
            'enabledRules' => array(//you can easily enable or disable promotion
                'lav_heart',
                '60_10%',
            ),
            'rules' => array(
                'lav_heart' => array(
                    'articles' => array(//select the articles you want to activate the promotion
                        '001'
                    ),
                    'condition' => 'count({articles}) >= 2', //key words are defined in PromotionCalculator in the class formulaVariable
                    'priceChange' => array(
                        'price' => 8.5  //set up the new price if the condition is TRUE
                    )
                ),
                '60_10%' => array(
                    'condition' => '{total} >= 60',
                    'totalChange' => '{total} * 0.9' //will apply this formula if condition is TRUE
                ),
            // This can be an amelioration
//                '5bought_2free' => array(
//                    'articles' => array('003', '002'),
//                    'priceChange' => array(
//                        'package' => 5,
//                        'effectOn' => '2',
//                        'price' => 0
//                    )
//                )
            )
        );

        return $rules;
    }

}
