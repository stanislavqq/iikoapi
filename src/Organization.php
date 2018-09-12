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

    public function __construct($id)
    {
        $this->id = $id;
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

    public function isValid() {

    }
}
