<?php

namespace App\Types\Tba;

use App\Helpers\Enum\Item;
use Illuminate\Support\Facades\Lang;

abstract class CommentCategoricalType
{
    use Item;

    const COMMENT_CATEGORICAL_TYPE_SCHOOL = 'school';
    const COMMENT_CATEGORICAL_TYPE_TEAM_MODEL = 'tm';
    const COMMENT_CATEGORICAL_TYPE_PERSONAL = 'personal';

    public static function check($type)
    {
        switch ($type) {
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL:
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL:
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL:
                return true;
            default:
                return false;
        }
    }

    public static function getCommentCategoricalTypeList()
    {
        $mapTbaCommentCategoricalType = Lang::get('app/tba/comment-categorical-type');
        return [
            CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL => $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL],
            CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL => $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL],
            CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL => $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL],
        ];
    }

    public static function getCommentCategoricalType($type)
    {
        $mapTbaCommentCategoricalType = Lang::get('app/tba/comment-categorical-type');
        switch ($type) {
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL:
                return $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_SCHOOL];
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL:
                return $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_TEAM_MODEL];
            case CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL:
                return $mapTbaCommentCategoricalType[CommentCategoricalType::COMMENT_CATEGORICAL_TYPE_PERSONAL];
            default:
                return '';
        }
    }
}
