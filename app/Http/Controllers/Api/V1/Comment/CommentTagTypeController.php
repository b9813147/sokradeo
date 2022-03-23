<?php

namespace App\Http\Controllers\Api\V1\Comment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\CommentTagTypeService;

class CommentTagTypeController extends Controller
{
    protected $commentTagTypeService;

    public function __construct(CommentTagTypeService $commentTagTypeService)
    {
        $this->commentTagTypeService = $commentTagTypeService;
    }

    /**
     * Display all default comment tag types.
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            // Check user
            $userId = auth()->id();
            if (!$userId) throw new \Exception('User not logged in');

            $commentTagTypes = $this->commentTagTypeService->getDefaultCommentTagTypes();
            return Response::json([
                'status' => true,
                'data'   => $commentTagTypes
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display all default and groupId comment tag types.
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $req, int $groupId): \Illuminate\Http\JsonResponse
    {
        try {
            // Check user
            $userId = auth()->id();
            if (!$userId) throw new \Exception('User not logged in');

            // Get structured comment tags and types
            $idToken = $req->session()->get('idToken');
            $commentTagTypes = $this->commentTagTypeService->getTbaCommentTagTypes($groupId, $idToken);

            return Response::json([
                'status' => true,
                'data'   => $commentTagTypes
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
