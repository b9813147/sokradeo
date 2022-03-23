<?php

namespace App\Services;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\TbaCommentAttach;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateEventFile;
use App\Services\CommentTagTypeService;
use App\Models\TbaComment as CommentModel;
use App\Models\TbaCommentAttach as CommentAttachModel;
use App\Repositories\CommentRepository;
use App\Repositories\GroupRepository;
use App\Repositories\TbaRepository;
use App\Types\Group\DutyType;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CommentService extends BaseService
{
    protected $repository;
    protected $commentTagTypeService;
    protected $tbaCommentBlobMediaService;
    protected $localAllowedExtensions = [];

    public function __construct(
        CommentRepository          $commentRepository,
        GroupRepository            $groupRepository,
        TbaRepository              $tbaRepository,
        CommentTagTypeService      $commentTagTypeService,
        TbaCommentBlobMediaService $tbaCommentBlobMediaService
    ) {
        // Comment Repository
        $this->repository = $commentRepository;

        // Group Repository
        $this->groupRepository = $groupRepository;

        // Tba Repository
        $this->tbaRepository = $tbaRepository;

        // Comment Tag Type Service
        $this->commentTagTypeService = $commentTagTypeService;

        // Comment Blob Media Service and its properties
        $this->tbaCommentBlobMediaService = $tbaCommentBlobMediaService;

        $this->localAllowedExtensions     = []; // currently empty
    }

    public function getComment($id): array
    {
        $comment = $this->repository->getComment($id);
        $comment = $this->convertComment($comment);
        return $comment;
    }

    /**
     * Create a comment and its attachment
     * @param int $userId
     * @param array $commentData
     * @param array $fileData
     */
    public function createComment(int $userId, array $commentData, array $fileData = null)
    {
        // Migrate tags and types if needed on create
        // Note: If sync process is needed, please use the sync method 'initTagTypeMigration' on CommentTagTypeService
        $this->commentTagTypeService->migrateTagTypeOnCommentCreate($commentData);

        // Create comment in db
        $comment = $this->repository->createComment($userId, $commentData);

        // Upload file and create data in db
        if (!empty($fileData) && is_file($fileData['tmp_name']) && $comment) {
            $attachmentData = $this->getAttachmentData($comment->tba_id, $comment->id, $fileData);

            // Media type
            if ($this->tbaCommentBlobMediaService->isExtAllowed($attachmentData['ext']))
                $this->commentMediaFileHandler($attachmentData);

            // Types for local storage
            else if (in_array($attachmentData['ext'], $this->localAllowedExtensions))
                $this->commentLocalFileHandler($attachmentData);
        }

        // Create tba evaluate event
        $this->createEvalEventFromComment($comment->id);
    }

    /**
     * Create Tba Eval Event from comment
     * This function will be called when a comment is created
     * to create eval user and event to be used on TbaPlayer's chart
     * @param int $commentId
     */
    private function createEvalEventFromComment(int $commentId)
    {
        $commentData = $this->repository->getComment($commentId);
        $tagData = $commentData->tag;
        $evalUser = $this->tbaRepository->createEvalUserFromComment($commentData);
        $this->tbaRepository->createEvalEventFromComment($commentData, $tagData, $evalUser);
    }

    /**
     * Update a comment and its attachment
     * @param int $tbaId
     * @param int $userId
     * @param array $commentData
     * @param array $fileData
     */
    public function updateComment(int $tbaId, int $userId, array $commentData, array $fileData = null)
    {
        $commentId = $commentData['id'];
        $text      = $commentData['text'];

        // Update comment in db
        $this->repository->updateComment($tbaId, $commentId, $userId, $text);

        // Update file and db
        if (!empty($fileData) && is_file($fileData['tmp_name'])) {
            $attachmentData = $this->getAttachmentData($tbaId, $commentId, $fileData);

            // Media type
            if ($this->tbaCommentBlobMediaService->isExtAllowed($attachmentData['ext']))
                $this->commentMediaFileHandler($attachmentData);
                
            // Types for local storage
            else if (in_array($attachmentData['ext'], $this->localAllowedExtensions))
                $this->commentLocalFileHandler($attachmentData);
        }

        // Update tba evaluate event
        $this->tbaRepository->updateEvalEventFromComment($commentId, $text);
    }

    /**
     * Delete a comment and its attachment
     * @param int $tbaId
     * @param int $commentId
     * @param int $userId
     */
    public function deleteComment(int $tbaId, int $commentId, int $userId)
    {
        $commentData = $this->repository->getComment($commentId);
        if (!$commentData) return;

        // Determine user authority (admin or video owner)
        $groupId = $commentData->group_id;
        $userDuties = $this->groupRepository->getDutiesByUserIdAndGroupIds($userId, [$groupId]);
        $isGroupAdmin = false;
        foreach ($userDuties as $duty) {
            if ($duty->member_duty == DutyType::Admin) {
                $isGroupAdmin = true;
                break;
            }
        }
        $tba = $this->tbaRepository->getTba($tbaId);
        $isVideoOwner = $tba->user_id == $userId;

        // Delete comment based on user authority
        if ($isGroupAdmin || $isVideoOwner) {
            $this->repository->deleteComment($tbaId, $commentId);
        } else {
            $this->repository->deleteCommentByUser($tbaId, $commentId, $userId);
        }

        $this->clearCommentFileDirs($tbaId, $commentId);
    }

    /**
     * Display all comments based on the given tbaId.
     * @param int $tbaId
     * @param null|int $public - [optional] 1 for public, 0 for private
     * @return array
     */
    public function getComments(int $tbaId, int $public = null): array
    {
        $comments = $this->repository->getComments($tbaId, $public);
        $comments = $comments->map(function ($comment) {
            return $this->convertComment($comment);
        });
        return $comments->toArray();
    }

    /**
     * TbaEvaluateEventEvent data convert to TbaComment
     * TbaEvaluateEventFile data convert to TbaCommentAttach
     * todo 尚待修改及優化
     * @param int $tba_id
     * @param int $group_id
     */
    public function convertCreateComment(int $tba_id, int $group_id)
    {
        $comments = [
            1  => 'STD001',
            2  => 'STD002',
            3  => 'STD003',
            4  => 'STD004',
            5  => 'STD005',
            6  => 'STD005',
            7  => 'STD001',
            8  => 'STD002',
            9  => 'STD003',
            10 => 'STD004',
            11 => 'STD005',
            12 => 'STD005',
            13 => 'STD005',
            14 => 'STD001',
            15 => 'STD002',
            16 => 'STD003',
            17 => 'STD004',
            18 => 'STD005',
        ];
        // 先刪除舊資料
        $this->repository->deleteWhere(['tba_id' => $tba_id, 'comment_type' => 1]);
        $tbaEvalEvent_id = TbaEvaluateEvent::query()->where('tba_id', $tba_id)->where('evaluate_type', 1)->pluck('id');
        // 先刪除舊資料
        TbaCommentAttach::query()->whereIn('tba_comment_id', $tbaEvalEvent_id)->delete();

        TbaEvaluateEvent::with('tbaEvaluateEventMode', 'tbaEvaluateUser')->where('tba_id', $tba_id)
            ->where('evaluate_type', 1)
            ->orderBy('id')->chunk(1000, function ($chunks) use ($comments) {
                $chunks->each(function ($q) use ($comments) {
                    $result     = [
                        'nick_name'    => $q->tbaEvaluateUser
                            ? ($q->tbaEvaluateUser->identity === 'G')
                                ? $q->tbaEvaluateUser->user->name
                                : null
                            : null,
                        'tba_id'       => $q->tba_id,
                        'group_id'     => $q->group_id,
                        'tag_id'       => $comments[$q->tba_evaluate_event_mode_id],
                        'user_id'      => $q->user_id === null ? $q->tbaEvaluateUser->identity !== 'G' ? $q->tbaEvaluateUser->user_id : $q->user_id : $q->user_id,
                        'time_point'   => $q->time_point ?? 0,
                        'text'         => $q->text,
                        'comment_type' => $q->evaluate_type,
                        'public'       => $q->tbaEvaluateUser !== null ? 1 : !(boolean)$q->user_id,
                    ];
                    $tbaComment = $this->repository->create($result);

                    TbaEvaluateEventFile::query()->where('tba_evaluate_event_id', $q->id)->orderBy('id')->chunk(200, function ($chunks) use ($tbaComment) {
                        $chunks->each(function ($q) use ($tbaComment) {
                            $result = [
                                'tba_comment_id' => $tbaComment->id,
                                'name'           => $q->name,
                                'ext'            => $q->ext,
                                'image_url'      => $q->image_url,
                                'preview_url'    => $q->preview_url,
                            ];
                            TbaCommentAttach::query()->create($result);
                        });
                    });
                });
            });


    }

    /**
     * Display user comments
     * @param int $userId
     * @param string $mode - [optional] 'isMark' for received comments, 'public' for public comments, 'private' for private comments
     * @param bool|string $filter
     * @param int $size
     * @return Illuminate\\Pagination\\LengthAwarePaginator
     */
    public function getCommentsByUser(int $userId, string $mode, $filter = '', int $size = 100)
    {
        if (in_array($mode, ['public', 'private'])) {
            $commentMode = $mode == 'public' ? 1 : 0;
            $comments    = $this->repository->getCommentsByUser($userId, $commentMode, $filter);
        } else if ($mode == 'isMark') {
            $comments = $this->repository->getReceivedCommentsByUser($userId, $filter);
        }

        $comments = $comments->map(function ($comment) {
            return $this->convertComment($comment);
        });

        return $comments->paginate($size);
    }

    /**
     * Get Tba comments for Tba Player
     * @param int $tbaId
     * @param int $userId
     * @param bool $includePrivate - [optional] true to include owned private comments
     * @param bool $includeGuests - [optional] true to include guest comments (no userId, has nick_name)
     * @return array
     */
    public function getTbaCommentsByUser(int $tbaId, int $userId, bool $includePrivate = false, bool $includeGuests = false)
    {
        // Get all comments for the given tbaId
        $comments = $this->repository->getComments($tbaId);

        // If includePrivate is NOT true,
        // Remove all private comments
        // Else, remove all private comments except owned private comments
        if (!$includePrivate) {
            $comments = $comments->where('public', 1);
        } else {
            $myPrivateComments = $comments->where('public', 0)->where('user_id', $userId);
            $comments          = $comments->where('public', 1)->merge($myPrivateComments)->sortBy('time_point');
        }

        // If includeGuests is NOT true,
        // select only comments that DO have a userId
        if (!$includeGuests) {
            $comments = $comments->whereNotNull('user_id');
        }

        // Convert to comment structure
        $arrComments = [];
        foreach ($comments as $comment) {
            $arrComments[] = $this->convertComment($comment);
        }
        return $arrComments;
    }

    /**
     * Get Tba comments for watch-as-open
     * @param int $tbaId
     * @return array
     */
    public function getTbaComments(int $tbaId): array
    {
        // Get all comments for the given tbaId
        $comments = $this->repository->getComments($tbaId)->where('public', 1);

        // Convert to comment structure
        $arrComments = [];
        foreach ($comments as $comment) {
            $arrComments[] = $this->convertComment($comment);
        }

        return $arrComments;
    }

    /**
     * Update Tba comment timepoints
     * Desc: this will update all timepoints of the given tbaId
     * @param int $tbaId
     * @param array $timepoints
     * @param string $mode
     * @return bool
     */
    public function updateTbaCommentTimePoints(int $tbaId, int $timePoint, string $mode = 'inc'): bool
    {
        $isSuccessful = false;
        
        try {
            $comments = $this->repository->getCommentsByTbaIdAsQuery($tbaId);
            $evalEvents =  $this->tbaRepository->getEvalEventsByTbaIdAsQuery($tbaId);
            switch ($mode) {
                case 'inc':
                    $comments->increment('time_point', $timePoint);
                    $evalEvents->increment('time_point', $timePoint);
                    break;
                case 'dec':
                    $comments->decrement('time_point', $timePoint);
                    $evalEvents->decrement('time_point', $timePoint);
                    break;
                default:
                    break;
            }
            $isSuccessful = true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $isSuccessful;
    }

    /**
     * Check whether if the video has any guests' comments by the given tbaId
     * @param int $tbaId
     * @return bool
     */
    public function hasGuestComments(int $tbaId): bool
    {
        $comments = $this->repository->getCommentsByTbaIdAsQuery($tbaId)
            ->where('public', 1)
            ->whereNull('user_id')
            ->get();

        return $comments->isNotEmpty();
    }

    /**
     * Convert new structure to old structure
     * @param CommentModel $comment
     * @return array
     */
    private function convertComment(CommentModel $comment): array
    {
        $file = $comment->tbaCommentAttaches->first();

        return [
            'id'          => $comment->id,
            'type'        => (!empty($comment->tag->tagType->content))
                ? $this->commentTagTypeService->getNameFromTagTypeContent($comment->tag->tagType->content)
                : null,
            'tag'         => (!empty($comment->tag->content))
                ? $this->commentTagTypeService->getTagDataFromTagContent($comment->tag->content)
                : null,
            'is_positive' => $comment->tag->is_positive,
            'time'        => $comment->time_point,
            'text'        => $comment->text,
            'nick_name'   => $comment->nick_name,
            'user'        => $comment->user,
            'attachment'  => $this->convertCommentAttachToAttachmentData($file),
            'tba'         => $comment->tba,
            'group_id'    => (!empty($comment->group_id))
                ? $comment->group_id
                : null,
            'channel_id'  => (!empty($comment->group_id))
                ? GlobalPlatform::convertGroupIdToChannelId($comment->group_id)
                : null,
            'created_at'  => $comment->created_at,
            'updated_at'  => $comment->updated_at,
        ];
    }

    /**
     * Convert file to attachment data
     * @param CommentAttachModel $commentAttach
     * @return array - ['src' => url ir null, 'ext' => ext or null, 'type' => 'image' or 'media' or null]
     */
    private function convertCommentAttachToAttachmentData($commentAttach): array
    {
        $attachmentData = ['src' => null, 'ext' => null, 'type' => null];

        // If no attachment, return attachment null
        if (empty($commentAttach))
            return $attachmentData;

        // If local image, return image url
        // Assuming image_url is always local image
        if ($commentAttach->image_url) {
            $attachmentData['src'] = $commentAttach->image_url;
            $attachmentData['ext'] = pathinfo($commentAttach->image_url, PATHINFO_EXTENSION);
        }

        // If blob media, return blob url with SAS
        if ($commentAttach->name && $commentAttach->ext) {
            $attachmentData['src'] = $this->tbaCommentBlobMediaService->getBlobSASLink(
                $commentAttach->tba_comment_id,
                $commentAttach->name . "." . $commentAttach->ext // abc.mp4
            );
            $attachmentData['ext'] = $commentAttach->ext;
        }

        // Set up type
        // If Media types, set up type to 'media'
        // Else if Misc types, set up type to 'image'
        if ($this->tbaCommentBlobMediaService->isExtMedia($attachmentData['ext'])) {
            $attachmentData['type'] = 'media';
        } else if ($this->tbaCommentBlobMediaService->isExtMisc($attachmentData['ext'])) {
            $attachmentData['type'] = 'image';
        }

        return $attachmentData;
    }

    /**
     * Create a comment attachment data structure for file handler
     * @param int $tbaId
     * @param int $commentId
     * @param array $fileData - [name, size, tmp_name, type]
     * @return array - [tbaId, commentId, name, ext, size, path]
     */
    private function getAttachmentData(int $tbaId, int $commentId, array $fileData): array
    {
        return [
            'tbaId'     => $tbaId,
            'commentId' => $commentId,
            'name'      => time(), // file name is timestamp
            'ext'       => pathinfo($fileData['name'], PATHINFO_EXTENSION),
            'size'      => $fileData['size'],
            'path'      => $fileData['tmp_name'],
        ];
    }

    /**
     * Remove all uploaded files in local storage and blob based on comment dir
     * @param int $tbaId
     * @param int $commentId
     */
    private function clearCommentFileDirs(int $tbaId, int $commentId)
    {
        $this->clearCommentMediaDir($commentId);
        $this->clearCommentLocalFileDir($tbaId, $commentId);
    }

    /**
     * Remove all uploaded files in blob storag
     * @param int $commentId
     */
    private function clearCommentMediaDir(int $commentId)
    {
        $this->tbaCommentBlobMediaService->deleteMediaBlobDir($commentId);
    }

    /**
     * Remove all uploaded files in local storag
     * @param int $tbaId
     * @param int $commentId
     */
    private function clearCommentLocalFileDir(int $tbaId, int $commentId)
    {
        $subImgDir  = $tbaId . '/evaluate_event_file/' . $commentId;
        $fullImgDir = storage_path('app/public/tba/' . $subImgDir);
        if (File::exists($fullImgDir))
            File::cleanDirectory($fullImgDir);
    }

    /**
     * Handle comment file upload
     * @param array $attachmentData - [tbaId, commentId, name, ext, size, path]
     */
    private function commentMediaFileHandler(array $attachmentData)
    {
        try {
            // Extract essential data
            $blobDestDir     = $attachmentData['commentId'];
            $fileNameWithExt = $attachmentData['name'] . '.' . $attachmentData['ext']; // 6666666.mp4
            $blobDestPath    = $blobDestDir . '/' . $fileNameWithExt; // 123523/6666666.mp4
            $fileSrcDir      = $attachmentData['path'];

            // Execute methods
            $this->clearCommentFileDirs($attachmentData['tbaId'], $attachmentData['commentId']); // clear files
            $this->tbaCommentBlobMediaService->uploadMediaBlob($blobDestPath, $fileSrcDir); // upload file

            // Update comment attachment
            $this->repository->upsertCommentMediaAttachment($attachmentData['commentId'], $attachmentData);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Handle comment local file upload
     * @param array $attachmentData - [tbaId, commentId, name, ext, size, path]
     */
    private function commentLocalFileHandler(array $attachmentData)
    {
        try {
            // Extract essential data
            $subImgDir  = $attachmentData['tbaId'] . '/evaluate_event_file/' . $attachmentData['commentId'];
            $fullImgDir = storage_path('app/public/tba/' . $subImgDir);

            $fileNameWithExt = $attachmentData['name'] . '.' . $attachmentData['ext']; // 6666666.mp4
            $fileSrcDir      = $attachmentData['path'];

            // Execute methods
            $this->clearCommentFileDirs($attachmentData['tbaId'], $attachmentData['commentId']); // clear files
            Storage::makeDirectory('public/tba/' . $subImgDir); // create dir
            copy($fileSrcDir, $fullImgDir . '/' . $fileNameWithExt);

            // Update comment attachment
            $imageUrl = url('storage/tba/' . $subImgDir . '/' . $fileNameWithExt);
            $this->repository->upsertCommentImgAttachment($attachmentData['commentId'], $imageUrl);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
