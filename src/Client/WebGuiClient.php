<?php

declare(strict_types=1);

namespace Palmyr\WindtreWebApi\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Psr7\Response;
use Palmyr\WindtreWebApi\Model\DataUsageModel;

class WebGuiClient implements WebGuiClientInterface
{
    protected string $email;

    protected string $number;

    protected string $password;

    protected Client $client;

    protected string $url;

    protected CookieJar $cookieJar;

    public function __construct(
        string $password,
        string $email,
        string $number,
        ?Client $client = null,
        CookieJarInterface $cookieJar = null,
        string $url = "https://www.ho-mobile.it/leanfe"
    ) {
        $this->email = $email;
        $this->number = $number;
        $this->password = $password;
        $this->url = $url;

        if (is_null($client)) {
            $client = new Client();
        }

        if (is_null($cookieJar)) {
            $cookieJar = new CookieJar();
        }

        $this->client = $client;
        $this->cookieJar = $cookieJar;
    }


    public function checkAccount(): bool
    {

        $options = [
            "json" => [
                "email" => $this->email,
                "phone" => null,
                "channel" => "WEB",
                "cookies" => new CookieJar(),
            ],
            "headers" => [
                "Referer" => "https://www.ho-mobile.it/",
            ]
        ];

        $response = $this->client->request("post", $this->url . "/restAPI/LoginService/checkAccount", $options);

        $body = $this->phaseResponseBody($response);

        return ($body["accountStatus"] ?: null) === "LOGGABLE";
    }

    public function login(): void
    {

        if (!$this->cookieJar->getCookieByName("LEANFESESSIONID")->isExpired()) {
            return;
        }

        $options = [
            "json" => [
                "email" => $this->email,
                "phone" => $this->number,
                "password" => $this->password,
                "channel" => "WEB",
                "isRememberMe" => false
            ],
        ];

        $body = $this->request("post", "/restAPI/LoginService/login", $options);

        if (($body["operationStatus"]["status"] ?: null) === "OK") {
            return;
        }

        throw new \HttpResponseException("Unable to login");
    }

    public function getResidualCredit(): float
    {
        $options = [
            "json" => [
                "channel" => "WEB",
                "phoneNumber" => $this->number,
            ]
        ];

        $body = $this->request("post", "/custom/restAPI/getResidualCredit", $options);

        return $body["balance"] ?: 0;
    }

    public function getDataUsage(): DataUsageModel
    {
        $options = [
            "json" => [
                "channel" => "WEB",
                "phoneNumber" => $this->number,
            ]
        ];

        $body = $this->request("post", "/restAPI/CountersService/getCounters", $options);

        $details = $body["countersList"][0]["countersDetailsList"][2];

        return new DataUsageModel(
            description: $details["description"] ?: "",
            nextResetDate: $details["nextResetDate"] ?: "",
            residual: $details["residual"] ?: 0,
            residualUnit: $details["residualUnit"] ?: "GB",
        );
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        $options["headers"]["Referer"] = "https://www.ho-mobile.it/";
        $options["cookies"] = $this->cookieJar;
        $response = $this->client->request($method, $this->url . $uri, $options);

        return $this->phaseResponseBody($response);
    }

    protected function phaseResponseBody(Response $response): array
    {
        return json_decode((string)$response->getBody(), true);
    }
}
