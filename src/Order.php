<?php

namespace stanislavqq\iikoapi;

class Order
{

    public $id;
    public $date;
    public $phone;
    public $isSelfService = 'false';
    public $customer;
    public $products = [];
    public $payment;
    public $address = [
        "city" => "",
        "street" => "",
        "home" => "",
        "housing" => "",
        "apartment" => "",
        "comment" => ""
    ];

    /**
     * Order constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        array_push($this->products, $product);
        return $this;
    }

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products)
    {
        foreach ($products as $key => $product) {
            $this->setProduct($product);
        }
        return $this;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param array $address
     */
    public function setAddress(array $address)
    {
        if (isset($address['city']))
            $this->address['city'] = $address['city'];

        if (isset($address['street']))
            $this->address['street'] = $address['street'];

        if (isset($address['home']))
            $this->address['home'] = $address['home'];

        if (isset($address['housing']))
            $this->address['housing'] = $address['housing'];

        if (isset($address['apartment']))
            $this->address['apartment'] = $address['apartment'];

    }

    /**
     * @param $comment
     */
    public function setComment($comment)
    {
        $this->address['comment'] = (string)$comment;
    }
}
