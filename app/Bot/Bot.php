<?php

namespace App\Bot;


use App\Bot\Webhook\Messaging;
use Illuminate\Support\Facades\Log;

class Bot
{
    private $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function extractData()
    {
        $type = $this->messaging->getType();
        if ($type == "message") {
            return $this->extractDataFromMessage();
        } else if ($type == "postback") {
            return $this->extractDataFromPostback();
        }
        return [];
    }

    public function extractDataFromMessage()
    {
        $matches = [];
        $qr = $this->messaging->getMessage()->getQuickReply();
        if (!empty($qr)) {
            $text = $qr["payload"];
        } else {
            $text = $this->messaging->getMessage()->getText();
        }
        //single letter message means a text quote
        if (preg_match("/^start$/i", $text, $matches)) {
            return [
                "type" => Quoter::$NEW,
                "data" => [
                    "text" => $matches[0]
                ]
            ];
        } else if (preg_match("/^new|next\$/i", $text, $matches)) {
            return [
                "type" => Quoter::$NEXT,
                "data" => [
                    "text" => $matches[0]
                ]
            ];
        } else if (preg_match("/^pic|picture|image\$/i", $text, $matches)) {
            return [
                "type" => Quoter::$IMAGE,
                "data" => [
                    "image" => $matches[0]
                ]
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function extractDataFromPostback()
    {
        $payload = $this->messaging->getPostback()->getPayload();

        if (preg_match("/^new|next$/i", $payload)) {
            return [
                "type" => Quoter::$NEXT,
                "data" => [
                    "text" => $payload
                ]
            ];
        } else if ($payload === "get-started") {
            return [
                "type" => "get-started",
                "data" => []
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function reply($data)
    {
        if (method_exists($data, "toMessage")) {
            $data = $data->toMessage();
        } else if (gettype($data) == "string") {
            $data = ["text" => $data];
        }
        $id = $this->messaging->getSenderId();
        $this->sendMessage($id, $data);
    }

    public function sendWelcomeMessage()
    {
        $name = $this->getUserDetails()["first_name"];
        $this->reply("Hi there, $name! Welcome to Quote101! Type \"next\" to start");

    }

    private function getUserDetails()
    {
        $id = $this->messaging->getSenderId();
        $ch = curl_init("https://graph.facebook.com/v2.6/$id?access_token=" . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        return json_decode(curl_exec($ch), true);
    }

    private function sendMessage($recipientId, $message)
    {
        $messageData = [
            "recipient" => [
                "id" => $recipientId
            ],
            "message" => $message
        ];
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        Log::info(print_r(curl_exec($ch), true));
    }
}