<?php

namespace stanislavqq\iikoapi;

use \GuzzleHttp\Client;

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
    public function createOrganization(object $object, $setOrgAfterCreate = false)
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

    public function getNomenclature()
    {
        if (!is_null($this->organization)) {

            $orgId = $this->organization->id;

            $params = [
                'access_token' => $this->accessToken,
                'organizationId' => $orgId
            ];

            $res = $this->client->request('get', 'nomenclature/' . $orgId . '?' . http_build_query($params));
            $json = json_decode((string)$res->getBody());
            return $json->products;
        }

        return false;
    }

    public function sendOrder(Order $order)
    {
        if (empty($order->products)) {
            throw new \Exeption('Property "products" of class Order can\'t be empty!');
        }

        if (empty($order->customer)) {
            throw new \Exeption('Property "customer" of class Order can\'t be empty!');
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
                'address' => (array) $order->address
            ],
        ];
//
//        pre($postParams);
//        die();

        $postParams = (array) json_decode("{
  \"organization\": \"e464c693-4a57-11e5-80c1-d8d385655247\",
  \"customer\": {
    \"id\": \"94ba8ebb-8e43-7a1f-d4a8-190ed5a0c457\",
    \"name\": \"Иван\",
    \"phone\": \"71235678901\"
  },
  \"order\": {
    \"id\": \"582566d1-a121-0fb1-4d97-46ab77012b56\",
    \"date\": \"2018-09-02 18:56:20\",
    \"phone\": \"71235678901\",
    \"isSelfService\": \"false\",
    \"items\": [
      {
        \"id\": \"8b6acd19-e9c5-479d-8d98-09a434869b1a\",
        \"name\": \"7 UP\",
        \"amount\": 1,
        \"code\": \"0026\",
        \"sum\": 90
      },
      {
        \"id\": \"75c14ee6-bccd-4f52-8410-3806a9d592dd\",
        \"name\": \"Паста с говядиной\",
        \"amount\": 2,
        \"code\": \"0003\",
        \"sum\": 200
      },
      {
        \"id\": \"8842b207-1546-483b-945a-5eed6279139d\",
        \"name\": \"Салат из свежих помидоров и огурцов\",
        \"amount\": 3,
        \"code\": \"0029\",
        \"sum\": 420
      },
      {
        \"id\": \"e42a4866-9a06-4ad6-b341-76e06e0dc882\",
        \"name\": \"Укроп\",
        \"amount\": 4,
        \"code\": \"0016\",
        \"sum\": 52
      },
      {
        \"id\": \"03190633-77b4-4d02-b3bb-e5d691faf29d\",
        \"name\": \"Сидр\",
        \"amount\": 3,
        \"code\": \"0006\",
        \"sum\": 936
      },
      {
        \"id\": \"4837993d-9194-4dd1-80a4-27296c283cad\",
        \"name\": \"Борщ\",
        \"amount\": 3,
        \"code\": \"00030\",
        \"sum\": 240
      },
      {
        \"id\": \"a44dcab4-89ef-469a-8299-6f71e8838e0a\",
        \"name\": \"Солянка\",
        \"amount\": 4,
        \"code\": \"0001\",
        \"sum\": 320
      },
      {
        \"id\": \"846bbcb7-13bd-4e82-bfc4-80958a68918e\",
        \"name\": \"Салат Коул-слоу\",
        \"amount\": 1,
        \"code\": \"0027\",
        \"sum\": 100
      },
      {
        \"id\": \"72472e5b-7946-4b2f-ab1f-fa47d096e464\",
        \"name\": \"Сыр\",
        \"amount\": 1,
        \"code\": \"0014\",
        \"sum\": 45
      },
      {
        \"id\": \"39940018-2cc3-4bd1-8bf2-1371177fdd24\",
        \"name\": \"Манты\",
        \"amount\": 4,
        \"code\": \"0019\",
        \"sum\": 880
      }
    ],
    \"address\": {
      \"city\": \"Москва\",
      \"street\": \"Красная площадь\",
      \"home\": \"1\",
      \"housing\": \"\",
      \"apartment\": \"14\",
      \"comment\": \"Комментарий к заказу\"
    }
  }
}");
//        pre($postParams);
//        die();

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
