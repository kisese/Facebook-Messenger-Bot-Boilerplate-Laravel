<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 8/7/2017
 * Time: 4:38 PM
 */

namespace App\Bot\Webhook;


class Postback
{
    private $payload;
    private $referral;
    public function __construct(array $data)
    {
        $this->payload = $data["payload"];
        $this->referral = isset($data["referral"]) ? $data["referral"] : [];
    }
    public function getPayload()
    {
        return $this->payload;
    }
    public function getReferral()
    {
        return $this->referral;
    }
}