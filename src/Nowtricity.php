<?php

// Author: Peter Forret (toolstud-io, peter@forret.com)

namespace ToolstudIo\Nowtricity;

use Exception;
use ToolstudIo\Nowtricity\Exceptions\NotAuthorizedException;
use ToolstudIo\Nowtricity\Exceptions\ThrottledException;

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
        foreach ($data['countries'] ?? [] as $country) {
            $list[$country['id']] = $country['name'];
        }

        return $list;
    }

    public function current(string $country_id): ?array
    {
        $endpoint = "https://www.nowtricity.com/api/current-emissions/$country_id/";

        return $this->getAPI($endpoint);
    }

    public function last24(string $country_id): ?array
    {
        $endpoint = "https://www.nowtricity.com/api/current-emissions/$country_id/";

        return $this->getAPI($endpoint);
    }

    public function year(string $country_id, string $year = null): ?array
    {
        $year = $year ?? date('Y');
        $endpoint = "https://www.nowtricity.com/api/archive/$country_id/$year/";

        return $this->getAPI($endpoint);
    }

    //--------------------------------------------------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    //--------------------------------------------------------------------------------------------------------------

    /**
     * @throws NotAuthorizedException
     * @throws ThrottledException
     * @throws Exception
     */
    private function getAPI(string $endpoint): ?array
    {
        $url = $endpoint;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_HTTPHEADER => [
                "X-Api-Key: $this->api_key",
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        if ($response) {
            $response =  json_decode($response, true);
            if(isset($response['errors'])){
                $error_message = $response['errors']['detail'] ?? 'Unknown error';
                throw match ($error_message) {
                    'Not authorized' => new NotAuthorizedException($error_message),
                    'Too many requests, over quota' => new ThrottledException($error_message),
                    default => new Exception($error_message),
                }; // end
            }
            return $response;
        }

        return null;
    }
}
