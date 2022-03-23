<?php

namespace Tests\Feature;

use App\Services\BbLicenseService;
use App\Types\App\BbLicenseType;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BbLicenseTest extends TestCase
{
    /**
     * Test Get All
     */
    public function testGetAll()
    {
        $res = app(BbLicenseService::class)->getAll()->toArray();
        $this->assertIsArray($res);
    }

    /**
     * Test Get by Id
     */
    public function testGetById()
    {
        $repo = app(BbLicenseService::class);

        $id = BbLicenseType::OBSRV_CLASS_LIMIT_ID;
        $item = $repo->getById($id);
        $this->assertIsObject($item);

        $nonExistId = 0;
        $nullItem = $repo->getById($nonExistId);
        $this->assertIsNotObject($nullItem);
    }

    /**
     * Test Get Obsrv Class BB license 
     */
    public function testGetObsrvClassBbLicense()
    {
        $repo = app(BbLicenseService::class);
        $id = BbLicenseType::OBSRV_CLASS_LIMIT_ID;
        $item = $repo->getById($id);
        $this->assertIsObject($item);
    }
}
