<?php

namespace stanislavqq\iikoapi;

class Organization
{
    public $isActive;
    public $id;
    public $name;
    public $phone;
    public $description;
    public $email;
    private $menu = [];

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
}
