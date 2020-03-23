<?php

namespace App\Http\Controllers;

use App\Conversations\ExampleConversation;
use BotMan\BotMan\BotMan;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Mpociot\BotMan\Messages\Message;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->fallback(function($bot) {
            $bot->reply('Ketik "covid" untuk melihat statistik Berdasarkan Negara, ketik "check" untuk melakukan test mandiri, ketik "hospital" untuk Rumah sakit Rujukan di Indonesia'. PHP_EOL .'----------------------------'. PHP_EOL . 'Type "covid" to see statistics by country, type "check" to do an self-assesment, type "hospital" for referral hospitals in Indonesia' );
        });

        $botman->hears('Check', function (BotMan $bot) {
            // Make your bot feel and act more human, you can make it send “typing …”
            $bot->types();
            $bot->reply('Click Link Below to take self assesment '. PHP_EOL .'https://lawancovid-19.surabaya.go.id/self-assessment');
        });

        $botman->hears("hello, I'm {name}", function ($bot, $name) {
            $bot->reply('Hello '.$name.', nice to meet you!');
        });
        

        $botman->hears('covid', function ($bot) {
            $bot->types();
            //Get Data From API
            $results = $this->getGlobalData();
            $bot->reply($results);
        });
        
        $botman->hears('hospital', function ($bot) {
            $bot->types();
            $bot->reply('Click Link Below for Indonesia hospital support '. PHP_EOL .'>> https://gis-kawalcovid19.hub.arcgis.com/datasets/rs-rujukan-penanganan-covid19/data?geometry=110.957%2C-9.310%2C120.186%2C-7.408');
        });
        
        $botman->hears('infodetail', function ($bot) {
            $bot->types();
             $bot->types();
            $bot->reply('Click Link Below for detail information about covid-19 in Indonesia '. PHP_EOL .'>> https://kawalcovid19.blob.core.windows.net/viz/statistik_harian.html'. PHP_EOL .'>> https://kawalcorona.com/');
        });
        
        $botman->hears('/global', function ($bot) {
            $bot->types();
            //Get Data From API
            $results = $this->getGlobalData();
            $bot->reply($results);
        });
        
        $botman->hears('/global_{value}', function ($bot, $value) {
            $bot->types();
            //Get Data From API
            $results = $this->getRegionalData($value);
            $bot->reply($results);
        });
        
        $botman->hears('/medcare', function ($bot) {
            $bot->types();
            //Get Data From API
            $results = $this->getMedCareData();
            $bot->reply($results);
        });
        
        $botman->hears('/medcare_{value}', function ($bot, $value) {
            $bot->types();
            //Get Data From API
            $results = $this->getMedCareRegionalData($value);
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

        $botman->hears('get', function ($bot) {
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
                
                $data = "Berikut data keseluruhan COVID-19 di : ";
                $data .= "\n" . "Negara > " . $result->attributes->Country_Region;
                $data .= "\n======================";
                $data .= "\nTotal Positif : " . $result->attributes->Confirmed;
                $data .= "\nTotal Sembuh: " . $result->attributes->Recovered;
                $data .= "\nTotal Meninggal: " . $result->attributes->Deaths . "\n";
                $data .= "======================";
                $data .= "\nCovid Virtual Assistant made with love by kataback.com \nfrom Bali Indonesia";
                //english Version
                $data .= "\n-----------------------";
                $data .= "\nHere are covid-19 statistics at : ";
                $data .= "\n" . "Country > " . $result->attributes->Country_Region;
                $data .= "\n======================";
                $data .= "\nTotal Positive : " . $result->attributes->Confirmed;
                $data .= "\nTotal Recover: " . $result->attributes->Recovered;
                $data .= "\nTotal Death: " . $result->attributes->Deaths . "\n";
                $data .= "======================";
                $data .= "\nCovid Virtual Assistant made with love by kataback.com \nfrom Bali Indonesia";
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
        
        $data = "Silahkan Pilih Negara yang anda Ingin Tahu :  ";
        foreach ($results as $result) {
            if ($result->attributes->Country_Region == "Indonesia") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "China") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "Singapore") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "Malaysia") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Thailand") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Vietnam") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Australia") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
        }

        return $data;
    }

    public function getMedCareRegionalData($value)
    {
        $client = new Client();
        $uri = 'https://services8.arcgis.com/xkIJYiP5RSJttiLG/arcgis/rest/services/RS_Rujukan_Penanganan_COVID19/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=json';
        $response = $client->get($uri);
        $results = json_decode($response->getBody()->getContents());
        
        // $data = "Data rumah sakit" . $value;

        // foreach ($results as $result) {
        //     if ($result->features->attributes->Provinsi == $value) {
        //         $data .= "\n======================";
        //         $data .= "\nNama Rumah Sakit: " . $result->features->attributes->Nama_RS;
        //         $data .= "\nAlamat: " . $result->features->attributes->Address;
        //         $data .= "\nNo Telepon: " . $result->features->attributes->No_Telepon . "\n";
        //     }
        // }
        // $data .= "======================";
        // $data .= "\nCovid Virtual Assistant made with love by kataback.com \nfrom Bali Indonesia";

        return $results;
    }

    public function getMedCareData()
    {
        $client = new Client();
        $uri = 'https://services8.arcgis.com/xkIJYiP5RSJttiLG/arcgis/rest/services/RS_Rujukan_Penanganan_COVID19/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=json';
        $response = $client->get($uri);
        $results = json_decode($response->getBody()->getContents());
        
        $data = "Silahkan Pilih Negara yang anda Ingin Tahu :  ";
        foreach ($results as $result) {
            if ($result->attributes->Country_Region == "Indonesia") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "China") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "Singapore") {
                $data .= PHP_EOL . "/global_" . $result->attributes->Country_Region;
            }
            if ($result->attributes->Country_Region == "Malaysia") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Thailand") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Vietnam") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
            }
            if ($result->attributes->Country_Region == "Australia") {
            $data .= PHP_EOL . "/global_" . $result->attributes
                    ->Country_Region;
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
