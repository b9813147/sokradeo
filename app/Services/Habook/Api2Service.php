<?php

namespace App\Services\Habook;

use App\Services\Habook\ApiService as ApiHabookService;
use App\Helpers\CoreService\CoreServiceApi;
use App\Types\App\IES5Type;

class Api2Service
{
    use CoreServiceApi;

    private $apiHabookSrv = null;

    //
    public function __construct(ApiHabookService $apiHabookSrv)
    {
        $this->apiHabookSrv = $apiHabookSrv;
    }

    /**
     * get Sso Ticket from idToken (API 1)
     * @param string $idToken
     */
    public function getSsoTicketFromIdToken($idToken)
    {
        // Get ticket from API 1 -> GetQuickLoginTicket
        $userInfo = $this->apiHabookSrv->getTicket($idToken);
        $ticket = $userInfo->ticket;

        // Exchange API 1's ticket to API 2's ticket
        // this is not required; however, exchanging ticket is recommended
        $newIdToken = $this->getIdToken($ticket);
        $ssoTicket = $this->getSso($newIdToken);

        return $ssoTicket;
    }

    /**
     * Get IES5 identity
     * Currently only supports teacher
     * @return string
     */
    public function getIES5Identity()
    {
        return IES5Type::TEACHER;
    }

    /**
     * get personal user tag types
     * @param string $idToken
     */
    public function getPersonalCommentTagTypes($idToken)
    {
        // Get ticket from API 1 -> GetQuickLoginTicket
        $userInfo = $this->apiHabookSrv->getTicket($idToken);
        $ticket = $userInfo->ticket;

        // Get idToken and accessToken
        $tokenData = $this->getToken($ticket);
        $idToken = $tokenData['id_token'];
        $accessToken = $tokenData['access_token'];

        // Get SOK5 tag types
        $tagTypes = $this->getUserInformation($idToken, $accessToken);

        return $tagTypes;
    }
}
