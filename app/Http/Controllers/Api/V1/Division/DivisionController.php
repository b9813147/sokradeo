<?php

namespace App\Http\Controllers\Api\V1\Division;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\Division\DivisionCollection;
use App\Services\DivisionService;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * @var DivisionService
     */
    protected $divisionService;

    /**
     * DivisionController constructor.
     */
    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $divisionInfo = $this->divisionService->all();

            return $this->success(new DivisionCollection($divisionInfo));
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $channel_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $channel_id): \Illuminate\Http\JsonResponse
    {
        try {
            $group_id     = GlobalPlatform::convertChannelIdToGroupId($channel_id);
            $divisionInfo = $this->divisionService->findByUsers($group_id, auth()->id());

            return $this->success(new DivisionCollection($divisionInfo));
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
