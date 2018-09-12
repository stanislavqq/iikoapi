<?php
/**
 * Created by PhpStorm.
 * User: Stas
 * Date: 12.09.2018
 * Time: 11:35
 */

namespace stanislavqq\iikoapi;


class Payment
{
    public $id;
    public $code;
    public $name;
    public $comment;
    public $combinable;
    public $externalRevision;
    public $applicableMarketingCampaigns;
    public $deleted;

    /**
     * Payment constructor.
     * @param $id
     * @param array $data
     */
    public function __construct($id, $data = [])
    {
        $this->id = $id;

        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * @param $data
     */
    public function setData($data)
    {

        if (is_array($data) && !empty($data)) {

            foreach ($data as $key => $value) {

                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }
    }
}
