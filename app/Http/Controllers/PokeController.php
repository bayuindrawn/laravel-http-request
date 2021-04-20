<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redis;

class PokeController extends Controller
{
    public function __construct()
    {
        Redis::connection();
    }

    public function index(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://pokeapi.co/api/v2/pokemon";
        
        try {
            $req = $client->request('GET', $url);
            $res = json_decode($req->getBody()->getContents());
            Redis::set('pokemon', json_encode($res));
        } catch (RequestException $e) {
            $err =(is_null($e->getResponse()))? $e->getResponse() : json_decode($e->getResponse()->getBody()->getContents());
            $res = (is_null($err))? $err : json_encode($err);
        }

        $dataRedis = Redis::get('pokemon');

        $response = [
            'message'=> 'success get pokemon',
            'data'=> json_decode($dataRedis)
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
