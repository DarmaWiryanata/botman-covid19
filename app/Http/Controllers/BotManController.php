<?php

namespace App\Http\Controllers;

use App\Conversations\ExampleConversation;
use BotMan\BotMan\BotMan;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->fallback(function($bot) {
            $bot->reply('Sorry, I did not understand these commands.');
        });

        $botman->hears('Hello', function (BotMan $bot) {
            // Make your bot feel and act more human, you can make it send “typing …”
            $bot->types();
            $bot->reply('Hi there :)');
        });

        $botman->hears("hello, I'm {name}", function ($bot, $name) {
            $bot->reply('Hello '.$name.', nice to meet you!');
        });

        $botman->hears('/global', function ($bot) {
            $bot->types();
            //Get Data From API
            $results = $this->getGlobalData();
            $bot->reply($results);
        });

        $botman->hears('/positif', function ($bot) {
            $bot->types();
            //Get Data From API
            $result = $this->getIndividualData('/positif');
            $bot->reply($result);
        });

        $botman->hears('/sembuh', function ($bot) {
            $bot->types();
            //Get Data From API
            $result = $this->getIndividualData('/sembuh');
            $bot->reply($result);
        });

        $botman->hears('/meninggal', function ($bot) {
            $bot->types();
            //Get Data From API
            $result = $this->getIndividualData('/meninggal');
            $bot->reply($result);
        });

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function getGlobalData()
    {
        $client = new Client();
        $uri = 'https://api.kawalcorona.com';
        $response = $client->get($uri);
        $results = json_decode($response->getBody()->getContents());
        
        $data = "Berikut data keseluruhan COVID-19: ";

        foreach ($results as $result) {
            // $data .= "</br>" . "> " . $result->attributes->Country_Region;
            // $data .= "</br>Jumlah kasus: " . $result->attributes->Confirmed;
            // $data .= "</br>Aktif: " . $result->attributes->Active;
            // $data .= "</br>Sembuh: " . $result->attributes->Recovered;
            // $data .= "</br>Meninggal: " . $result->attributes->Deaths . "</br>";

            $data .= "\n" . "> " . $result->attributes->Country_Region;
            $data .= "\nJumlah kasus: " . $result->attributes->Confirmed;
            $data .= "\nAktif: " . $result->attributes->Active;
            $data .= "\nSembuh: " . $result->attributes->Recovered;
            $data .= "\nMeninggal: " . $result->attributes->Deaths . "\n";
        }

        return $data;
    }

    public function getIndividualData($value)
    {
        $client = new Client();
        $uri = 'https://api.kawalcorona.com' . $value;
        $response = $client->get($uri);
        $result = json_decode($response->getBody()->getContents());

        $data = $result->name . ': ' . $result->value . PHP_EOL;

        return $data;
    }
}
