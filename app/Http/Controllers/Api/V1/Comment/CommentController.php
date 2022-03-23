<?php

namespace App\Http\Controllers\Api\V1\Comment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\CommentService;

class CommentController extends Controller
{
    /**
     * @var CommentService
     */
    protected $commentService;

    /**
     * CommentController constructor.
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
        $this->commentSharingStatus = [0, 1]; // 1 for public, 0 for private
    }

    /**
     * Display all comments based on the given tbaId.
     * @param Request $req
     * @param int $tbaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $req, int $tbaId): \Illuminate\Http\JsonResponse
    {
        try {
            // Detemine comment sharing status
            $public = $req->input('public', null);
            if ($public && !in_array($public, $this->commentSharingStatus))
                throw new \Exception('Invalid public value');

            // Get comments
            $comments = $this->commentService->getComments($tbaId, $public);

            return Response::json([
                'status' => true,
                'data'   => $comments
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Create a comment based on the given tbaId.
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            // Check user
            $userId = auth()->id();
            if (!$userId) throw new \Exception('User not logged in');

            // Create comment
            $commentData = json_decode($req->input('commentData'), true);
            $fileData = isset($_FILES['fileData']) ? $_FILES['fileData'] : null;
            $this->commentService->createComment($userId, $commentData, $fileData);

            return Response::json([
                'status' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * Update the comment based on the given tbaId and commentId.
     * @param Request $req
     * @param int $tbaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $req, int $tbaId): \Illuminate\Http\JsonResponse
    {
        try {
            // Check user
            $userId = auth()->id();
            if (!$userId) throw new \Exception('User not logged in');

            // Update comment
            $commentData = json_decode($req->input('commentData'), true);
            $fileData = isset($_FILES['fileData']) ? $_FILES['fileData'] : null;
            $this->commentService->updateComment($tbaId, $userId, $commentData, $fileData);

            return Response::json([
                'status' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $req
     * @param int $tbaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $req, int $tbaId): \Illuminate\Http\JsonResponse
    {
        try {
            // Check user
            $userId = auth()->id();
            if (!$userId) throw new \Exception('User not logged in');

            // Delete comment
            $commentId = $req->input('commentId');
            $this->commentService->deleteComment($tbaId, $commentId, $userId);

            return Response::json([
                'status' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
