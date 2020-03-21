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
            $bot->reply('Sorry, I did not understand these commands.'. PHP_EOL . 'Try these commands' . PHP_EOL . '/positif' . PHP_EOL . '/sembuh' . PHP_EOL . '/meninggal');
        });

        $botman->hears('Hello', function (BotMan $bot) {
            // Make your bot feel and act more human, you can make it send “typing …”
            $bot->types();
            $bot->reply('Hi there :)');
        });

        $botman->hears("hello, I'm {name}", function ($bot, $name) {
            $bot->reply('Hello '.$name.', nice to meet you!');
        });
        
        // $botman->hears("/help", function (BotMan $bot) {
        //     $bot->reply(ButtonTemplate::create('Do you want to know more about BotMan?')
        //     	->addButton(ElementButton::create('Tell me more')
        //     	    ->type('postback')
        //     	    ->payload('tellmemore')
        //     	)
        //     	->addButton(ElementButton::create('Show me the docs')
        //     	    ->url('http://botman.io/')
        //     	)
        //     );
        // });

        $botman->hears('/global', function ($bot) {
            $bot->types();
            //Get Data From API
            $results = $this->getGlobalData();
            $bot->reply($results);
        });

        $botman->hears('/global {value}', function ($bot, $value) {
            $bot->types();
            //Get Data From API
            $results = $this->getRegionalData($value);
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

    public function getRegionalData($value)
    {
        $client = new Client();
        $uri = 'https://api.kawalcorona.com';
        $response = $client->get($uri);
        $results = json_decode($response->getBody()->getContents());
        

        foreach ($results as $result) {
            if ($result->attributes->Country_Region == $value) {
                
                $data = "Berikut data keseluruhan COVID-19: ";
                $data .= "\n" . "> " . $result->attributes->Country_Region;
                $data .= "\nJumlah kasus: " . $result->attributes->Confirmed;
                $data .= "\nAktif: " . $result->attributes->Active;
                $data .= "\nSembuh: " . $result->attributes->Recovered;
                $data .= "\nMeninggal: " . $result->attributes->Deaths . "\n";
            }
        }

        return $data;
    }

    public function getGlobalData()
    {
        $client = new Client();
        $uri = 'https://api.kawalcorona.com';
        $response = $client->get($uri);
        $results = json_decode($response->getBody()->getContents());
        
        $data = "Berikut data keseluruhan COVID-19: ";
        foreach ($results as $result) {
            if ($result->attributes->Country_Region == "Indonesia") {
                $data .= PHP_EOL . "/global " . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "China") {
                $data .= PHP_EOL . "/global " . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "Korea, South") {
                $data .= PHP_EOL . "/global " . $result->attributes->Country_Region;
            }
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
