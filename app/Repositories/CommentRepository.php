<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use App\Models\Tba as TbaModel;
use App\Models\TbaComment;
use App\Models\TbaCommentAttach;

class CommentRepository extends BaseRepository
{
    protected $model;
    protected $tbaCommentAttachModel;

    public function __construct(TbaComment $tbaComment, TbaCommentAttach $tbaCommentAttachModel)
    {
        $this->commentModes = [0, 1]; // 0: private, 1: public
        $this->dtFormat = 'Y-m-d H:i:s';
        $this->model = $tbaComment;
        $this->tbaCommentAttachModel = $tbaCommentAttachModel;
    }

    /**
     * Get a comment based on the given pk.
     * @param int $id
     */
    public function getComment($commentId)
    {
        return $this->getBaseCommentQuery()
            ->where('id', $commentId)
            ->first();
    }

    /**
     * Create a comment based on tbaId, pk, and userId.
     * @param int $userId
     * @param array $commentData
     * @return TbaComment
     */
    public function createComment($userId, $commentData)
    {
        $dtNow = date($this->dtFormat);
        $comment = $this->model->create([
            'nick_name' => null,
            'tag_id' => $commentData['tagId'],
            'tba_id' => $commentData['tbaId'],
            'user_id' => $userId,
            'group_id' => $commentData['groupId'],
            'comment_type' => 2, // 2: comment from tbaPlayer
            'public' => $commentData['public'],
            'time_point' => $commentData['timepoint'],
            'text' => $commentData['text'],
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
        return $comment->fresh();
    }

    /**
     * Update a comment based on tbaId, pk, userId, and text.
     * @param int $tbaId
     * @param int $commentId
     * @param int $userId
     * @param string $text
     */
    public function updateComment(int $tbaId, int $commentId, int $userId, string $text)
    {
        $conditions = [
            'id' => $commentId,
            'tba_id' => $tbaId,
            'user_id' => $userId
        ];
        if ($this->model->where($conditions)->exists()) {
            $this->model->where($conditions)->update(
                [
                    'text' => $text,
                    'updated_at' => date($this->dtFormat)
                ]
            );
        }
    }

    /**
     * Create or Update a comment attachment (Media) based on commentId.
     * @param int $commentId
     * @param array $attachmentData
     */
    public function upsertCommentMediaAttachment(int $commentId, array $attachmentData)
    {
        $conditions = ['tba_comment_id' => $commentId];
        $dtNow = date($this->dtFormat);
        if ($this->tbaCommentAttachModel->where($conditions)->exists()) {
            $this->tbaCommentAttachModel->where($conditions)->update(
                [
                    'name' => $attachmentData['name'],
                    'ext' => $attachmentData['ext'],
                    'image_url' => null,
                    'preview_url' => null,
                    'updated_at' => $dtNow
                ]
            );
        } else {
            $this->tbaCommentAttachModel->create(
                [
                    'tba_comment_id' => $commentId,
                    'name' => $attachmentData['name'],
                    'ext' => $attachmentData['ext'],
                    'image_url' => null,
                    'preview_url' => null,
                    'created_at' => $dtNow,
                    'updated_at' => $dtNow,
                ]
            );
        }
    }

    /**
     * Create or Update a comment attachment (Img) based on commentId.
     * @param int $commentId
     * @param string $imageUrl
     */
    public function upsertCommentImgAttachment(int $commentId, string $imageUrl)
    {
        $conditions = ['tba_comment_id' => $commentId];
        $dtNow = date($this->dtFormat);
        if ($this->tbaCommentAttachModel->where($conditions)->exists()) {
            $this->tbaCommentAttachModel->where($conditions)->update(
                [
                    'name' => null,
                    'ext' => null,
                    'image_url' => $imageUrl,
                    'preview_url' => $imageUrl,
                    'updated_at' => $dtNow
                ]
            );
        } else {
            $this->tbaCommentAttachModel->create(
                [
                    'tba_comment_id' => $commentId,
                    'name' => null,
                    'ext' => null,
                    'image_url' => $imageUrl,
                    'preview_url' => $imageUrl,
                    'created_at' => $dtNow,
                    'updated_at' => $dtNow,
                ]
            );
        }
    }

    /**
     * Delete a comment based on tbaId, pk, and userId.
     * @param int $tbaId
     * @param int $commentId
     * @param int $userId
     */
    public function deleteCommentByUser(int $tbaId, int $commentId, int $userId)
    {
        $conditions = [
            'id' => $commentId,
            'tba_id' => $tbaId,
            'user_id' => $userId
        ];
        if ($this->model->where($conditions)->exists())
            $this->model->where($conditions)->delete();
    }

    /**
     * Delete a comment based on tbaId, pk
     * @param int $tbaId
     * @param int $commentId
     */
    public function deleteComment(int $tbaId, int $commentId)
    {
        $conditions = [
            'id' => $commentId,
            'tba_id' => $tbaId
        ];
        if ($this->model->where($conditions)->exists())
            $this->model->where($conditions)->delete();
    }

    /**
     * Get all comments based on the given tbaId.
     * @param int $tbaId
     * @param null|int $public - [optional] 1 for public, 0 for private
     */
    public function getComments($tbaId, $public = null)
    {
        // Set up based query
        $comments = $this->getBaseCommentQuery()
            ->where('tba_id', $tbaId);

        // If public is set, filter by public
        if ($public !== null)
            $comments = $comments->where('public', $public);

        // Execute query
        $comments = $comments
            ->orderBy('updated_at', 'desc')
            ->get();

        return $comments;
    }

    /**
     * Get user comments
     * @param int $userId
     * @param null|int $commentMode - [optional] 1 for public, 0 for private
     * @param bool|string $filter
     * @return array
     */
    public function getCommentsByUser(int $userId, $commentMode = null, $filter = '')
    {
        // Set up based query
        $comments = $this->getBaseCommentQuery()->where('user_id', $userId);

        // If public is set, filter by public
        if (in_array($commentMode, $this->commentModes))
            $comments = $comments->where('public', $commentMode);

        // If filter is set, filter by filter
        if (!empty($filter))
            $comments = $comments->where('text', 'like', '%' . $filter . '%');

        return $comments->get();
    }

    /**
     * Get a comment based on tbaId as query builder
     * @param int $tbaId
     * @return EloquentBuilder
     */
    public function getCommentsByTbaIdAsQuery(int $tbaId): EloquentBuilder
    {
        return $this->getBaseCommentQuery()->where('tba_id', $tbaId);
    }

    /**
     * Get comments by tbaIdList
     * @param array $tbaIdList
     * @param null|int $commentMode - [optional] 1 for public, 0 for private
     * @param bool|string $filter
     * @return array
     */
    public function getCommentsByTbaIdList(array $tbaIdList, $commentMode = null, $filter = '')
    {
        // Set up based query
        $comments = $this->getBaseCommentQuery()->whereIn('tba_id', $tbaIdList);

        // If public is set, filter by public
        if (in_array($commentMode, $this->commentModes))
            $comments = $comments->where('public', $commentMode);

        // If filter is set, filter by filter
        if (!empty($filter))
            $comments = $comments->where('text', 'like', '%' . $filter . '%');

        return $comments->get();
    }

    /**
     * Get received comments by user
     * Condition for received comments
     *  1.) User-owned video
     *  2.) Comment is public
     * @param int $userId
     * @param bool|string $filter
     * @return array
     */
    public function getReceivedCommentsByUser(int $userId, $filter = '')
    {
        // Get owned videos tbaIdList
        $tbaIdList = TbaModel::query()->where('user_id', $userId)->pluck('id')->toArray();

        // Set up based query
        $comments = $this->getBaseCommentQuery()
            ->whereIn('tba_id', $tbaIdList)
            ->where('public', 1);

        // If filter is set, filter by filter
        if (!empty($filter))
            $comments = $comments->where('text', 'like', '%' . $filter . '%');

        return $comments->get();
    }


    /**
     * Get the base comment model query
     * @return EloquentBuilder
     */
    private function getBaseCommentQuery(): EloquentBuilder
    {
        $commentQuery = $this->model
            ->with([
                'tag' => function ($query) {
                    $query->select('id', 'content', 'type_id', 'is_positive');
                    $query->with(['tagType' => function ($query) {
                        $query->select('id', 'content');
                    }]);
                },
                'tbaCommentAttaches',
                'user' => function ($query) {
                    $query->select('id', 'name', 'habook');
                },
                'tba' => function ($query) {
                    $query->select('id', 'name', 'teacher', 'habook_id');
                },
            ])
            ->orderBy('updated_at', 'desc');
        return $commentQuery;
    }
}
