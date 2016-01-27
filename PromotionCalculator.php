<?php

/**
 * Class managing the promotion system including on the total and on each items
 *
 * @author cifren
 */
class PromotionCalculator
{

    /**
     *  Enabled rules that will be used to calculate the total
     * @var array 
     */
    protected $enabledRules;

    /**
     *  Rules that will apply the promotional price of each items
     * @var array
     */
    protected $onPriceRules;

    /**
     *  Rules that will apply promotion on the total
     * @var array 
     */
    protected $onTotalRules;

    /**
     * 
     * @param array $promotionalRules   rules of promotion
     */
    public function __construct(array $promotionalRules)
    {
        foreach ($promotionalRules['enabledRules'] as $item) {
            $this->enabledRules[$item] = $promotionalRules['rules'][$item];
        }

        //reduce the enabledRules list, select only onPriceRules
        $this->onPriceRules = array_filter($this->enabledRules, function($ary) {

            return isset($ary['priceChange']);
        });

        //reduce the enabledRules list, select only onTotalRules
        $this->onTotalRules = array_filter($this->enabledRules, function($ary) use ($item) {
            return isset($ary['totalChange']);
        });
    }

    /**
     * Apply promotional rules on each items, it will add up a new information
     * in the object Item
     * 
     * @param array of Item $checkedItems
     */
    protected function applyPriceChange($checkedItems)
    {
        foreach ($this->onPriceRules as $rule) {
            //select only item without yet promotion on it
            $checkedItemsWithoutPromotion = $this->getNoPromotionItems($checkedItems);

            //selected articles in the current rule
            $selectedIds = $rule['articles'];
            //reduce the list to have only items that will be affected by the rule
            $promotedItems = array_filter($checkedItemsWithoutPromotion, function($item) use ($selectedIds) {
                return in_array($item->getId(), $selectedIds);
            });

            //prepare $formulaVariable to be used in the eval function
            $formulaVariable = new formulaVariable($promotedItems);
            $conditionFormula = $rule['condition'];
            //get the result of the rule condition
            $conditionResult = $this->evalFormula($conditionFormula, $formulaVariable);
            //if TRUE apply the prices changes
            if ($conditionResult) {
                //update promotional prices
                $this->setPromotionPrices($promotedItems, $rule['priceChange']['price']);
            }
        }
    }

    /**
     * Calculate the checkout total executing all rules defined by the user
     * 
     * @param array $checkedItems   array of Item
     * @return float    checkout total
     */
    public function getTotal($checkedItems)
    {
        //apply price changes
        $this->applyPriceChange($checkedItems);

        //prepare formula variables in order to be used in eval function
        $formulaVariable = new formulaVariable($checkedItems);
        $total = $formulaVariable->total;

        //for each rules
        foreach ($this->onTotalRules as $rule) {
            $conditionFormula = $rule['condition'];
            $conditionResult = $this->evalFormula($conditionFormula, $formulaVariable);

            //if the condition is TRUE
            if ($conditionResult) {
                //Apply formula and get the new total
                $total = $this->evalFormula($conditionFormula, $formulaVariable);
            }
            //update the formulat variable context
            $formulaVariable->total = $total;
        }

        return $total;
    }

    /**
     * Get only Items without promotion price
     * 
     * @param array $checkedItems   Item array from the checkout
     * @return array    Item array
     */
    protected function getNoPromotionItems($checkedItems)
    {
        return array_filter($checkedItems, function($item) {
            return $item->getPromotionApplied() == false;
        });
    }

    /**
     * Use the eval function in order to get the result of the condition or
     * calculation
     * 
     * @param string $conditionFormula
     * @param FormulaVariable $formulaVariable
     * @return bool
     */
    protected function evalFormula($conditionFormula, $formulaVariable)
    {
        //get all expresion with "{example}"
        preg_match_all("/\{[^\}]*\}/", $conditionFormula, $matches);

        $replace = [];
        //prepare the replacement of strings by variable name
        foreach ($matches as $match) {
            $variable = str_replace(array('{', '}'), '', $match[0]);
            $search[] = "{{$variable}}";
            $replace[] = "\$formulaVariable->" . $variable;
        }

        //replace and execute the formula
        return eval('return ' . str_replace($search, $replace, $conditionFormula) . ';');
    }

    /**
     * go over all items and change the promotion price
     * 
     * @param array $items  all Items that need to be modified
     * @param float $price  new price
     */
    protected function setPromotionPrices($items, $price)
    {
        foreach ($items as $item) {
            $item->setPromotionalPrice($price);
            $item->setPromotionApplied(true);
        }
    }

}

/**
 * Has all variables used inside the formula, calculate checkout total
 * 
 */
class formulaVariable
{

    /**
     *  Checkout total
     * @var float 
     */
    public $total;

    /**
     *  articles list
     * @var array
     */
    public $articles;

    /**
     * 
     * @param array $articles
     */
    public function __construct($articles)
    {
        $this->articles = $articles;
        $this->total = $this->getTotal();
    }

    /**
     * 
     * @return float
     */
    protected function getTotal()
    {
        $sum = 0;
        foreach ($this->articles as $item) {
            $sum += $item->getPromotionalPrice();
        }

        return $sum;
    }

}
