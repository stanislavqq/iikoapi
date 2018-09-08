<?php

namespace stanislavqq\iikoapi;

class Product
{
    public $id;
    public $name = '';
    public $amount = 1;
    public $code;
    public $price = 0;
    public $isDeleted;
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

    public function __construct($object)
    {
        if (!isset($object->id))
            return false;

        $this->id = isset($object->id) ? $object->id : null;
        $this->name = isset($object->name) ? $object->name : null;
        $this->code = isset($object->code) ? $object->code : null;
        $this->price = isset($object->price) ? $object->price : null;
        $this->isDeleted = isset($object->isDeleted) ? $object->isDeleted : null;
        $this->description = isset($object->description) ? $object->description : null;
        $this->groupId = isset($object->groupId) ? $object->groupId : null;
        $this->energyAmount = isset($object->energyAmount) ? $object->energyAmount : null;
        $this->energyFullAmount = isset($object->energyFullAmount) ? $object->energyFullAmount : null;
        $this->fatAmount = isset($object->fatAmount) ? $object->fatAmount : null;
        $this->fatFullAmount = isset($object->fatFullAmount) ? $object->fatFullAmount : null;
        $this->fiberAmount = isset($object->fiberAmount) ? $object->fiberAmount : null;
        $this->fiberFullAmount = isset($object->fiberFullAmount) ? $object->fiberFullAmount : null;
        $this->measureUnit = isset($object->measureUnit) ? $object->measureUnit : null;
        $this->isIncludedInMenu = isset($object->isIncludedInMenu) ? $object->isIncludedInMenu : null;
        $this->type = isset($object->type) ? $object->type : null;
        $this->doNotPrintInCheque = isset($object->doNotPrintInCheque) ? $object->doNotPrintInCheque : null;
        $this->useBalanceForSell = isset($object->useBalanceForSell) ? $object->useBalanceForSell : null;
        $this->weight = isset($object->weight) ? $object->weight : null;
        $this->images = isset($object->images) ? $object->images : null;
        $this->order = isset($object->order) ? $object->order : null;
        $this->parentGroup = isset($object->parentGroup) ? $object->parentGroup : null;
        $this->additionalInfo = isset($object->additionalInfo) ? $object->additionalInfo : null;
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
