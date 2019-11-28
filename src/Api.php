<?php

namespace stanislavqq\iikoapi;

use \GuzzleHttp\Client;
use Exception;
use Throwable;
use stanislavqq\iikoapi\Order;

class Api
{
    private $login;
    private $password;
    private $accessToken;
    private $baseUri = 'https://iiko.biz:9900/api/0/';
    private $client;
    private $organization;
    private $organizations = [];

    /**
     * IikoApi constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['login']) || !isset($options['password'])) {
            return print '"login" or "password" not be set!';
        }

        $this->login = $options['login'];
        $this->password = $options['password'];

        if (!is_null($this->baseUri))
            $this->client = new Client(['base_uri' => $this->baseUri]);

        $this->createToken();
    }

    /**
     * @return $this
     */
    public function createToken()
    {
        $params = [
            'user_id' => $this->login,
            'user_secret' => $this->password
        ];

        $res = $this->client->request('get', 'auth/access_token?' . http_build_query($params));

        if ($res->getStatusCode() === 200) {
            $token = (string)$res->getBody();

            $this->accessToken = str_replace('"', '', $token);
        }

        return $this;
    }

    /**
     * @param string $uri
     */
    public function setBaseUri(string $uri)
    {
        $this->baseUri = $uri;

        return $this;
    }

    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * @return Order
     */
    public function createOrder()
    {
        return new Order($this->getGUID());
    }

    /**
     * @return Product
     */
    public function createProduct()
    {
        $product = new \stdClass();
        $product->id = $this->getGUID();

        return new Product($product);
    }

    /**
     * @return Customer
     */
    public function createCustomer()
    {
        return new Customer($this->getGUID());
    }

    /**
     * @return string
     */
    public function getGUID()
    {
        if (function_exists("com_create_guid"))
            return com_create_guid();

        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charId = md5(uniqid(rand(), true));
        $hyphen = chr(45);// "-"
        $uuid = substr($charId, 0, 8) . $hyphen
            . substr($charId, 8, 4) . $hyphen
            . substr($charId, 12, 4) . $hyphen
            . substr($charId, 16, 4) . $hyphen
            . substr($charId, 20, 12);

        return $uuid;
    }

    public function getOrganizationList()
    {
        if (!is_null($this->accessToken)) {

            $params = [
                'access_token' => $this->accessToken,
                'request_timeout' => '00:02:00'
            ];

            $res = $this->client->request('get', 'organization/list?' . http_build_query($params));

            if ($res->getStatusCode() === 200) {
                $organizations = json_decode((string)$res->getBody());
                $this->organizations = $organizations;
                return $this->organizations;
            }

            return $res->getStatusCode() . ":" . $res->getHeaders();
        }
    }

    /**
     * @param object $object
     * @return bool|Organization
     */
    public function createOrganization($object, $setOrgAfterCreate = false)
    {

        if (isset($object->id)) {
            $organization = new Organization($object->id);
            $organization->isActive = $object->isActive;
            $organization->description = $object->description;
            $organization->phone = $object->contact->phone;
            $organization->email = $object->contact->email;
            $organization->name = $object->name;

            if ($setOrgAfterCreate === true) {
                $this->setOrganization($organization);
            }

            return $organization;
        }

        return false;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getNomenclature()
    {
        if (is_null($this->organization)) {
            throw new Exception('Для получения продукции необходимы данные о организации. Используйте Api->setOrganization(Organization $organization)');
        }

        $orgId = $this->organization->id;

        $params = [
            'access_token' => $this->accessToken,
            'organizationId' => $orgId
        ];

        $res = $this->client->request('get', 'nomenclature/' . $orgId . '?' . http_build_query($params));
        $json = json_decode((string)$res->getBody());

        $products = [];

        if (isset($json->products)) {
            foreach ($json->products as $product) {
                $products[] = new Product($product);
            }

            return $products;
        }
    }

    /**
     * @param \stanislavqq\iikoapi\Order $order
     * @return string
     * @throws Exception
     */
    public function sendOrder(Order $order)
    {
        if (empty($order->products)) {
            throw new Exception('Property "products" of class Order can\'t be empty!');
        }

        if (empty($order->customer)) {
            throw new Exception('Property "customer" of class Order can\'t be empty!');
        }

        $productsItems = $this->prepareProducts($order->products);

        $params = [
            'access_token' => $this->accessToken,
            'requestTimeout' => 10000
        ];

        $postParams = [
            'organization' => $this->organization->id,
            'customer' => (array)$order->customer,
            'order' => [
                'id' => $order->id,
                'date' => $order->date,
                'phone' => $order->phone,
                'isSelfService' => $order->isSelfService,
                'items' => $productsItems,
                'address' => (array)$order->address
            ],
        ];

        $res = $this->client->request('post', 'orders/add?' . http_build_query($params), [
            'form_params' => $postParams
        ]);

        return (string)$res->getBody();
    }

    public function prepareProducts($products)
    {
        $productsItems = [];
        foreach ($products as $product) {
            if ($product instanceof Product) {
                $productsItems[] = (array)$product;
            }
        }

        return $productsItems;
    }

}
