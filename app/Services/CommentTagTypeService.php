<?php

namespace App\Services;

use App\Models\TagType;
use App\Models\Tag;
use App\Services\Habook\Api2Service;
use App\Repositories\CommentTagTypeRepository;
use App\Types\Tba\CommentCategoricalType;

class CommentTagTypeService
{
    protected $repository;
    protected $api2Service;

    public function __construct(CommentTagTypeRepository $repository, Api2Service $api2Service)
    {
        $this->repository = $repository;
        $this->api2Service = $api2Service;
    }

    /**
     * Get comment tags and types for TbaPlayer
     * The data includes all default, school, and user tag types
     * @param int $groupId
     * @param string $idToken
     * @return array
     */
    public function getTbaCommentTagTypes(int $groupId, string $idToken): array
    {
        // Get tag types (in order of TM, School, Personal)
        $tmKey       = CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL;
        $schoolKey   = CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL;
        $personalKey = CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL;
        return [
            [
                'key'   => $tmKey,
                'name'  => CommentCategoricalType::getCommentCategoricalType($tmKey),
                'types' => $this->getDefaultCommentTagTypes()
            ],
            [
                'key'   => $schoolKey,
                'name'  => CommentCategoricalType::getCommentCategoricalType($schoolKey),
                'types' => $this->getSchoolCommentTagTypes($groupId)
            ],
            [
                'key'   => $personalKey,
                'name'  => CommentCategoricalType::getCommentCategoricalType($personalKey),
                'types' => $this->getPersonalCommentTagTypes($idToken) // This may contain tag types that are not in the DB yet
            ],
        ];
    }

    /**
     * Get comment tags and types
     * @return array
     */
    public function getDefaultCommentTagTypes(): array
    {
        $commentTagTypes = $this->repository->getDefaultCommentTagTypes();
        $commentTagTypes = $commentTagTypes->map(function ($comment) {
            return $this->convertComentTagType($comment);
        });
        return $commentTagTypes->toArray();
    }

    /**
     * Get school comment tags and types
     * @param int $groupId
     * @return array
     */
    public function getSchoolCommentTagTypes(int $groupId): array
    {
        $commentTagTypes = $this->repository->getCommentTagTypesFromGroupId($groupId);
        $commentTagTypes = $commentTagTypes->map(function ($comment) {
            return $this->convertComentTagType($comment);
        });
        return $commentTagTypes->toArray();
    }

    /**
     * Get user comment tags and types
     * @param int $userId
     * @return array
     */
    public function getUserCommentTagTypes(int $userId): array
    {
        $commentTagTypes = $this->repository->getCommentTagTypesFromUserId($userId);
        $commentTagTypes = $commentTagTypes->map(function ($comment) {
            return $this->convertComentTagType($comment);
        });
        return $commentTagTypes->toArray();
    }

    /**
     * Get user comment tags and types (from Blob)
     * @param string $idToken
     * @return
     */
    public function getPersonalCommentTagTypes(string $idToken)
    {
        try {
            $commentTagTypes = $this->api2Service->getPersonalCommentTagTypes($idToken);
            if (empty($commentTagTypes)) return [];
            $commentTagTypes = $commentTagTypes['tags'];
            $commentTagTypes = collect($commentTagTypes)->map(function ($comment) {
                return $this->convertPersonalCommentTagType($comment);
            });
        } catch (\Exception $e) {
            $commentTagTypes = [];
        }

        return $commentTagTypes;
    }

    /**
     * Convert comment tag type structure
     * @param CommentModel $comment
     * @return array
     */
    private function convertComentTagType(TagType $commentTagType): array
    {
        return [
            'id'        => $commentTagType->id,
            'group_id'  => $commentTagType->group_id,
            'habook_id' => $commentTagType->habook_id,
            'name'      => $this->getNameFromTagTypeContent($commentTagType->content),
            'tags'      => $commentTagType->tag->map(function ($tag) {
                return [
                    'id'          => $tag->id,
                    'is_positive' => $tag->is_positive,
                    'content'     => $this->getTagDataFromTagContent($tag->content),
                ];
            })->toArray(),
        ];
    }

    /**
     * Convert personal comment tag type structure (derived from blob)
     * @param CommentModel $comment
     * @return array
     */
    private function convertPersonalCommentTagType($personalCommentTagType)
    {
        return [
            'id' => $personalCommentTagType['typeId'],
            'group_id' => null,
            'habook_id' => auth()->user()->habook,
            'name' => $this->getNameFromTagTypeContent(json_encode($personalCommentTagType['typeName'])),
            'tags' => collect($personalCommentTagType['tagList'])->map(function ($tag) {
                return [
                    'id' => $tag['id'],
                    'is_positive' => 1,
                    'content' => $this->getTagDataFromTagContent(json_encode($tag)),
                ];
            })->toArray(),
        ];
    }

    /**
     * Get name from Tag Type content
     * @param string $content - ex.: {"cn": "Bloom认知分类 (2001 Revised)", "en": "Bloom‘s taxonomy (Revised)", "tw": "Bloom認知分類 (2001 Revised)", "customize": null}
     * @return string
     */
    public function getNameFromTagTypeContent(string $content): string
    {
        $content = json_decode($content, true);
        $locale  = !empty($content['customize'])
            ? 'customize'
            : \App::getLocale();

        return $content[$locale];
    }

    /**
     * Get Tag data from Tag content
     * @param string $content - ex.: {"name": {"cn": "记忆", "en": "Remember", "tw": "記憶", "customize": null}, "description": {"cn": "从长期记忆中提取相关知识", "en": "Retrieve related knowledge from long term memory", "tw": "從長期記憶中提取相關知識", "customize": null}}
     * @return array - ['name' => '', 'desc' => '']
     */
    public function getTagDataFromTagContent(string $content): array
    {
        $content = json_decode($content, true);
        $locale  = !empty($content['name']['customize']) || !empty($content['description']['customize'])
            ? 'customize'
            : \App::getLocale();

        return [
            'name' => $content['name'][$locale],
            'desc' => $content['description'][$locale],
        ];
    }

    /**
     * Migrate comment tag and type on creating comment
     * @param string $commentData
     */
    public function migrateTagTypeOnCommentCreate(array $commentData)
    {
        $userId = auth()->id();

        // Create Type
        $typeId = $commentData['typeId'];
        $typeName = $commentData['typeName'];
        if (!TagType::where('id', $typeId)->exists())
            $this->repository->createCommentTagType($typeId, $typeName, null, $userId);

        // Create Tag
        $tagId = $commentData['tagId'];
        $tagName = $commentData['tagName'];
        $tagDesc = $commentData['tagDesc'];
        if (!Tag::where('id', $tagId)->exists())
            $this->repository->createCommentTag($tagId, $tagName, $tagDesc, $typeId);
    }


    /**
     * Initialize comment tag types migration from Blob to DB
     * @param string $idToken
     */
    private function initTagTypeMigration(string $idToken)
    {
        $commentTagTypes = $this->api2Service->getPersonalCommentTagTypes($idToken);
        if (empty($commentTagTypes)) return;

        // Migration process
        // Note: As requested by supervisor, we only CREATE new comment types and tags for the first time
        //       without UPDATE existing comment types and tags
        //       (If UPDATE is needed) => simply use 'updateOrCreate'
        $commentTagTypes = $commentTagTypes['tags'];
        $commentTagTypes = collect($commentTagTypes)->each(function ($commentTagType) {
            // Migrate Tags and Types
            $typeId = $commentTagType['typeId'];
            if (!TagType::where('id', $typeId)->exists()) {
                $this->migrateTagType($typeId, $commentTagType);
                $this->migrateTypes($typeId, $commentTagType);
            }
        });
    }

    /**
     * Migrate comment tag type from Blob to DB
     * @param string $typeId
     * @param array $commentTagType
     */
    private function migrateTagType(string $typeId, array $commentTagType)
    {
        TagType::query()->create([
            'id' => $typeId,
            'group_id' => null,
            'user_id' => auth()->user()->id,
            'content' => json_encode($commentTagType['typeName']),
        ]);
    }

    /**
     * Migrate comment types from Blob to DB
     * @param string $typeId
     * @param array $commentTagType
     */
    private function migrateTypes(string $typeId, array $commentTagType)
    {
        collect($commentTagType['tagList'])->each(function ($commentTag) use ($typeId) {
            Tag::query()->create([
                'id' => $commentTag['id'],
                'type_id' => $typeId,
                'content' => json_encode([
                    'name' => $commentTag['name'],
                    'description' => $commentTag['description']
                ]),
            ]);
        });
    }
}
