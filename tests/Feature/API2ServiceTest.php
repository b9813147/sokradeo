<?php

namespace Tests\Feature;

use App\Services\Habook\Api2Service;

use Tests\TestCase;

class API2ServiceTest extends TestCase
{
    private $idToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJoYWJvb2tJZCI6Im51dHRhcGhhdCM1NzQ0IiwicHJvZmlsZVBpY3R1cmVVcmwiOiJodHRwczovL2NvcmVzdG9yYWdlc2VydmljZS5ibG9iLmNvcmUud2luZG93cy5uZXQvYWNjb3VudC9hdmF0YXIvMTU0ODAzOTgwOCIsIlRFQU1Nb2RlbElkIjoibnV0dGFwaGF0IzU3NDQiLCJuYW1lIjoiTnV0dGFwaGF0IEFydW5vcHJheW9jaCIsIm5iZiI6IjE1MjE2MDk2MjgiLCJzdGF0aW9uIjoiZ2xvYmFsIiwiaXNTdHVkZW50IjpmYWxzZSwiZG9jdW1lbnRJZCI6IjE1NDgwMzk4MDgiLCJpZCI6IjE1NDgwMzk4MDgifQ.b21PrpZ8s4eELlcFyNXnOolNJryf8XLhS98fgdva1ZU";

    public function testGetSsoTicketFromIdToken()
    {
        $api2Srv = app(Api2Service::class);
        $ssoTicket = $api2Srv->getSsoTicketFromIdToken($this->idToken);
        $this->assertIsString($ssoTicket);
    }

    public function testFetPersonalCommentTagTypes()
    {

        $api2Srv = app(Api2Service::class);
        $tagTypes = $api2Srv->getPersonalCommentTagTypes($this->idToken);
        $this->assertIsArray($tagTypes);
    }
}
