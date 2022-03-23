<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use App\Models\TagType;
use App\Models\Tag;

class CommentTagTypeRepository
{
    protected $model;
    protected $tagModel;

    public function __construct(TagType $model, Tag $tagModel)
    {
        $this->model = $model;
        $this->tagModel = $tagModel;
    }

    /**
     * Get default comment tags and types
     * @return TagType
     */
    public function getDefaultCommentTagTypes()
    {
        // Get base query
        $commentTagTypeQuery = $this->getBaseCommentTagTypeQuery()
            ->where('group_id', null)
            ->where('user_id', null);

        // Execute query
        $commentTagType = $commentTagTypeQuery->get();

        return $commentTagType;
    }

    /**
     * Get comment tags and types from groupId
     * @param int $groupId
     * @return TagType
     */
    public function getCommentTagTypesFromGroupId(int $groupId)
    {
        // Get base query
        $commentTagTypeQuery = $this->getBaseCommentTagTypeQuery()
            ->where('group_id', $groupId);

        // Execute query
        $commentTagType = $commentTagTypeQuery->get();

        return $commentTagType;
    }

    /**
     * Get comment tags and types from userId
     * @param int $userId [optional]
     * @return TagType
     */
    public function getCommentTagTypesFromUserId(int $userId)
    {
        // Get base query
        $commentTagTypeQuery = $this->getBaseCommentTagTypeQuery()
            ->where('user_id', $userId);

        // Execute query
        $commentTagType = $commentTagTypeQuery->get();

        return $commentTagType;
    }

    /**
     * Create tag types
     * @param string $typeId
     * @param string $typeName
     * @param int $groupId
     * @param int $userId
     */
    public function createCommentTagType(string $typeId, string $typeName, int $groupId = null, int $userId = null)
    {
        $this->model->query()->create([
            'id' => $typeId,
            'group_id' => $groupId,
            'user_id' => $userId,
            'content' => json_encode($this->getCustomizeContent($typeName)),
        ]);
    }

    /**
     * Create tag
     * @param string $tagId
     * @param string $tagName
     * @param string $tagDesc
     * @param string $typeId
     */
    public function createCommentTag(string $tagId, string $tagName, string $tagDesc, string $typeId)
    {
        $this->tagModel->query()->create([
            'id' => $tagId,
            'content' => json_encode([
                'name' => $this->getCustomizeContent($tagName),
                'description' => $this->getCustomizeContent($tagDesc),
            ]),
            'type_id' => $typeId,
        ]);
    }

    /**
     * Get the base comment model query
     * Only take the data whose status is 1 (active)
     * @return EloquentBuilder
     */
    private function getBaseCommentTagTypeQuery(): EloquentBuilder
    {
        $commentTagTypeQuery = $this->model
            ->select(['id', 'group_id', 'content', 'user_id', 'order', 'status'])
            ->where('status', 1)
            ->with([
                'tag' => function ($query) {
                    $query->select(['id', 'content', 'type_id', 'is_positive', 'status'])
                        ->where('status', 1);
                },
            ])
            ->orderby('order', 'asc');
        return $commentTagTypeQuery;
    }

    /**
     * Get Customize Content
     * @return array
     */
    private function getCustomizeContent($customize): array
    {
        return [
            'en' => null,
            'tw' => null,
            'cn' => null,
            'customize' => $customize,
        ];
    }
}
