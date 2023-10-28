<?php

// Author: Peter Forret (toolstud-io, peter@forret.com)

namespace ToolstudIo\Nowtricity;

use PhpParser\Node\Expr\Array_;

class Nowtricity
{
    private string $api_key;
    private string $user_agent;

    public function __construct(string $api_key, string $user_agent = null)
    {
        $this->api_key = $api_key;
        $this->user_agent = $user_agent ?? 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) toolstud.io WebAgent';
    }

    public function countries(): array
    {
        $endpoint = 'https://www.nowtricity.com/api/countries/';
        $data = $this->getAPI($endpoint);
        $list = [];
        foreach($data['countries'] ?? [] as $country){
            $list[$country['id']] = $country['name'];
        }
        return $list;
    }

    private function getAPI(string $endpoint, array $params = []): array
    {
        $url = $endpoint;
        if($params){
            $url .= '?' . http_build_query($params);
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_HTTPHEADER => array(
                "X-Api-Key: $this->api_key"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
}
