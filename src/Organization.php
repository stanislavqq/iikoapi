<?php

namespace stanislavqq\iikoapi;

class Organization
{
    public $isActive;
    public $id;
    public $name;
    public $phone;
    public $description;
    private $menu = [];

    public $address;
    public $averageCheque;
    public $contact;
    public $currencyIsoName;
    public $homePage;
    public $latitude;

    public $logo;
    public $logoImage;

    public $longitude;

    public $maxBonus;
    public $minBonus;

    public $networkId;
    public $organizationType;
    public $timezone;
    public $website;
    public $workTime;

    public function __construct($object)
    {
        $this->id = $object->id;
        $this->name = $object->name;
        $this->phone = $object->phone;
        $this->isActive = $object->isActive;
        $this->description = $object->description;
        $this->address = $object->address;

        $this->averageCheque = $object->averageCheque;
        $this->contact = $object->contact;
        $this->currencyIsoName = $object->currencyIsoName;
        $this->homePage = $object->homePage;
        $this->latitude = $object->latitude;
        $this->logo = $object->logo;
        $this->logoImage = $object->logoImage;
        $this->longitude = $object->longitude;
        $this->maxBonus = $object->maxBonus;
        $this->minBonus = $object->minBonus;
        $this->networkId = $object->networkId;
        $this->organizationType = $object->organizationType;
        $this->timezone = $object->timezone;
        $this->website = $object->website;
        $this->workTime = $object->workTime;

    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function setMenuItem(Product $product)
    {
        $this->menu[$product->id] = $product;
        return $this;
    }

    public function setMenuItems(array $products)
    {
        foreach ($products as $key => $product) {
            $this->setMenuItem($product);
        }

        return $this;
    }
}
