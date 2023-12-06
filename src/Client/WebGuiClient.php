<?php
declare(strict_types=1);

namespace Palmyr\WindtreWebApi\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class WebGuiClient implements WebGuiClientInterface
{

    protected string $username;

    protected string $password;

    protected Client $client;

    protected string $url;

    public function __construct(
        string $username,
        string $password,
        ?Client $client = null,
        string $url = "https://www.ho-mobile.it/leanfe"
    )
    {
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;

        if (is_null($client) ) {
            $client = new Client();
        }

        $this->client = $client;
    }


    public function checkAccount(): bool
    {

        $options = [
            "json" => [
                "email" => $this->username,
                "phone" => null,
                "channel" => "WEB"
            ],
        ];

        $result = $this->request("post", "/restAPI/LoginService/checkAccount", $options);

        var_dump((string)$result->getBody());
    }

    protected function request(string $method, string $uri, array $options = []): Response
    {
        $options["headers"]["Referer"] = "https://www.ho-mobile.it/";
        return $this->client->request($method, $this->url . $uri, $options);
    }

}