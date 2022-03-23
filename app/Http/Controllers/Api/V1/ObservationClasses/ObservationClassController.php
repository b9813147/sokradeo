<?php

namespace App\Http\Controllers\Api\V1\ObservationClasses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\ObservationClassService;
use App\Types\ObservationClass\StatusType;

class ObservationClassController extends Controller
{
    /**
     * @var ObsrvClassService
     */
    private $obsrvClassService;

    /**
     * ObservationClassController constructor.
     *
     * @param ObsrvClassService $obsrvClassService
     */
    public function __construct(ObservationClassService $obsrvClassService)
    {
        $this->obsrvClassService = $obsrvClassService;
    }

    /**
     * Get the currently standby or starting observation class by user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $authUser = auth()->user();
            if (empty($authUser)) throw new \Exception('User not logged in');
            return Response::json([
                'status' => true,
                'data' => $this->obsrvClassService->getCurrentUserObsrvClass($authUser),
            ], 200);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $authUser = auth()->user();
            if (empty($authUser)) throw new \Exception('User not logged in');
            $obsrvData = $request->all();
            $obsrvClass = $this->obsrvClassService->createObsrvClass($authUser, $obsrvData);
            return Response::json([
                'status' => true,
                'data'   => $obsrvClass
            ], 201);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $authUser = auth()->user();
            if (empty($authUser)) throw new \Exception('User not logged in');

            // Update Obsrv Class
            $updateMethod = $request->get('action', StatusType::ENDED_ACTION);
            if ($updateMethod == StatusType::STARTING_ACTION)
                $obsrvClass = $this->obsrvClassService->startObsrvClass($authUser, $id);
            else if ($updateMethod == StatusType::ADD_EXTRA_TIME_ACTION)
                $obsrvClass = $this->obsrvClassService->addExtraTime($authUser, $id, $request->get('extraSeconds', 0));
            else if ($updateMethod == StatusType::ENDED_ACTION)
                $obsrvClass = $this->obsrvClassService->endObsrvClass($authUser, $id);
            else
                throw new \Exception('Invalid action');

            return Response::json([
                'status' => true,
                'data'   => $obsrvClass,
            ], 200);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
