<?php 


namespace App;

class HttpClient {

    protected $client;

    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    public function get($endPoint, array $params = [])
    {

        $queryString = '';
        if(count($params)){
            $queryString = '?' . http_build_query($params);
        }

        $url = $endPoint . $queryString ;

        $response = $this->client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                // 'Authorization' => "Bearer $token"
            ]
        ]);

        $responseContents = $response->getBody()->getContents();

        return json_decode($responseContents, true);
    }

}