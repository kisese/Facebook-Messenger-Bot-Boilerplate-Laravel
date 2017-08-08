<?php

namespace App\Bot;


use App\Quote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use function sizeof;

class Quoter
{
    public static $IMAGE= "image";
    public static $NEW = "new";
    public static $NEXT= "next";

    private $quote;
    private $author;

    public function __construct(array $data)
    {
        $this->quote = $data["quote"];
        $this->author = $data["author"];
    }

    public static function getNew()
    {
        $quote_data = Quote::all()->random(1)[0]->toArray();



        return new Quoter($quote_data);
    }

    public static function addQuickReply()
    {
        $quote_data = Quote::all()->random(1)[0]->toArray();

        $quote = $quote_data["quote"];
        //string filter
        $str = str_replace("\n","",$quote);
        $str1 = str_replace("\"","",$str);
        $str2 = str_replace("+","",$str1);

        $quote = $str2;

        return [
            "text" => $quote." \n- ".$quote_data["author"],
            "quick_replies" => [
                [
                    "content_type" => "text",
                    "title" => "Another One",
                    "payload" => "next"
                ]
            ]
        ];
    }

    public function toMessage()
    {

        $quote = $this->quote;
        //string filter
        $str = str_replace("\n","",$quote);
        $str1 = str_replace("\"","",$str);
        $str2 = str_replace("+","",$str1);

        $quote = $str2;

        //compose message
        $text = htmlspecialchars_decode("$quote. \n- .$this->author", ENT_QUOTES | ENT_HTML5);
        Log::info(print_r($text, true));

        $response =  [
            "text" => $text,
            "quick_replies" => [
                [
                    "content_type" => "text",
                    "title" => "Another One",
                    "payload" => "next"
                ]
            ]
        ];

//        $response = [
//            "attachment" => [
//                "type" => "template",
//                "payload" => [
//                    "template_type" => "button",
//                    "text" => $text,
//                    "buttons" => []
//                ]
//            ]
//        ];
//
//        $letters = ["new", "next"];
//        for ($i = 0; $i < count($letters); $i++) {
//            $response["attachment"]["payload"]["buttons"][] = [
//                "type" => "postback",
//                "title" => "{$letters[$i]}:",
//                "payload" => "{$letters[$i]}"
//            ];
//        }

        return $response;
    }
}