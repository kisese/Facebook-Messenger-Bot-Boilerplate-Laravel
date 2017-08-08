<?php

namespace App\Jobs;

use App\Bot\Bot;
use App\Bot\Quoter;
use App\Bot\Webhook\Messaging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BotHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $messaging;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bot = new Bot($this->messaging);
        $custom = $bot->extractData();
        if ($custom["type"] == Quoter::$NEW) {
            $bot->reply(Quoter::getNew());
        } else if ($custom["type"] == Quoter::$NEXT) {
            $bot->reply(Quoter::addQuickReply());
        } else if ($custom["type"] == "get-started") {
            $bot->sendWelcomeMessage();
            $bot->reply(Quoter::getNew());
        } else {
            $bot->reply("I don't understand. Try \"new\" for a new quote");
        }
    }
}
