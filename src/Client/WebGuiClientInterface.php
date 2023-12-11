<?php

declare(strict_types=1);

namespace Palmyr\WindtreWebApi\Client;

use GuzzleHttp\Cookie\CookieJar;
use Palmyr\WindtreWebApi\Model\DataUsageModel;

interface WebGuiClientInterface
{
    public function checkAccount(): bool;

    public function login(): void;

    public function getResidualCredit(): float;
}
