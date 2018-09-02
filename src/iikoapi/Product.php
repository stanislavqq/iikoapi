<?php

namespace stanislavqq\iikoapi;

class Product
{
    public $id;
    public $name;
    public $amount = 1;
    public $code;
    public $sum;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return float|int
     */
    public function getTotalSum()
    {
        if ($this->sum !== null && $this->amount !== null)
            return (float)$this->sum * (float)$this->amount;

        return 0;
    }
}
