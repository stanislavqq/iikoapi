<?php

namespace stanislavqq\iikoapi;

use \GuzzleHttp\Client;
use Exception;
use Throwable;

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
    public function createProduct($id = null, $properties = [])
    {
        $pid = !is_null($id) ? $id : $this->getGUID();
        $product = new Product($pid);
        if ($properties) {
            $product->setData($properties);
        }

        return $product;
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
    public function createOrganization($id = null)
    {
        $gid = !is_null($id) ? $id : $this->getGUID();
        $organization = new Organization($gid);
        $this->setOrganization($organization);

        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentTypes()
    {

        $params = [
            'access_token' => $this->accessToken,
            'organization' => $this->organization->id
        ];

        $res = $this->client->request('get', 'rmsSettings/getPaymentTypes?' . http_build_query($params));
        $json = json_decode((string)$res->getBody());

        $payments = [];

        if (isset($json->paymentTypes)) {
            foreach ($json->paymentTypes as $paymentType) {
                $payment = $this->createPayment($paymentType->id, [
                    'code' => $paymentType->code,
                    'name' => $paymentType->name,
                    'comment' => $paymentType->comment,
                    'combinable' => $paymentType->combinable,
                    'externalRevision' => $paymentType->externalRevision,
                    'applicableMarketingCampaigns' => $paymentType->applicableMarketingCampaigns,
                    'deleted' => $paymentType->deleted,
                ]);

                $payments[] = $payment;
            }
        }

        return $payments;
    }

    public function createPayment($id, $data = [])
    {
        return new Payment($id, $data);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
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
                $productItem = $this->createProduct($product->id, [
                    'code' => $product->code,
                    'name' => $product->name,
                    'carbohydrateAmount' => $product->carbohydrateAmount,
                    'carbohydrateFullAmount' => $product->carbohydrateFullAmount,
                    'energyAmount' => $product->energyAmount,
                    'energyFullAmount' => $product->energyFullAmount,
                    'fatAmount' => $product->fatAmount,
                    'fatFullAmount' => $product->fatFullAmount,
                    'fiberAmount' => $product->fiberAmount,
                    'fiberFullAmount' => $product->fiberFullAmount,
                    'groupId' => $product->groupId,
                    'order' => $product->order,
                    'price' => $product->price,
                ]);

                $products[] = $productItem;
            }

            return $products;
        }


    }

    public function sendOrder(Order $order)
    {
        if ($this->validateOrder($order) != true) {
            throw new Exception('Order data is not valid');
        }

        if ($this->validateCustomer($order->customer) != true) {
            throw new Exception('Customer data is not valid');
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
                'address' => $order->address
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
                $productsItems[] = [
                    'id' => $product->id,
                    'amount' => $product->amount,
                    'sum' => $product->price,
                    'name' => $product->name
                ];
            }
        }

        return $productsItems;
    }

    protected function validateCustomer(Customer $customer)
    {
        if (is_null($customer->phone) || $customer->phone == '') {
            throw new Exception('Customer->phone is not set!');
        }

        if (is_null($customer->name) || $customer->name == '') {
            throw new Exception('Customer->phone is not set!');
        }

        return true;
    }

    protected function validateOrder(Order $order)
    {

        if (is_null($order->date)) {
            throw new Exception('Order->date is not set!');
        }

        if (is_null($order->phone) || $order->phone == '') {
            throw new Exception('Order->phone is not set!');
        }

        if (is_null($order->address)) {
            throw new Exception('Order->address is not set!');
        }

        if (!is_array($order->address)) {
            throw new Exception('Order->address should be a array');
        }

        if (!is_array($order->products) && empty($order->productsItems)) {
            throw new Exception('Order->products is not set!');
        }

        if (is_null($order->isSelfService)) {
            throw new Exception('Order->isSelfService is not set!');
        }

        return true;
    }

    protected function validateProduct(Product $product)
    {

    }

}
