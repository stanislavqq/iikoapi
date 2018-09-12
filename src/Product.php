<?php

namespace stanislavqq\iikoapi;

class Product
{
    /**
     * @var
     */
    public $id;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $amount = 1;

    /**
     * @var
     */
    public $code;

    /**
     * @var int
     */
    public $price = 0;

    /**
     * @var
     */
    public $isDeleted;

    /**
     * @var string
     */
    public $description = '';

    public $groupId;
    public $energyAmount;
    public $energyFullAmount;
    public $fatAmount;
    public $fatFullAmount;
    public $fiberAmount;
    public $fiberFullAmount;
    public $measureUnit;
    public $isIncludedInMenu;
    public $type;
    public $doNotPrintInCheque;
    public $useBalanceForSell;
    public $weight;
    public $images;
    public $order;
    public $parentGroup;
    public $additionalInfo;

    public $isGift = false;

    /**
     * Product constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param $properties
     * @return bool
     */
    public function setData($properties)
    {
        if (is_array($properties) && !empty($properties)) {
            foreach ($properties as $key => $value) {

                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }

            }
            return true;
        }

        return false;
    }

    /**
     * @return float|int
     */
    public function getTotalSum()
    {
        if ($this->price !== null && $this->amount !== null)
            return (float)$this->price * (float)$this->amount;

        return 0;
    }
}
