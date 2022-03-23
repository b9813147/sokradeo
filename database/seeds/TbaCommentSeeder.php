<?php

use App\Models\TbaComment;
use App\Models\TbaCommentAttach;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateEventFile;
use Illuminate\Database\Seeder;

class TbaCommentSeeder extends Seeder
{
    public function run()
    {
        $this->base_data();
        $this->tba_comment_attaches_data();
    }

    /**
     * tba_comment convert
     */
    public function base_data()
    {
        \DB::statement('set foreign_key_checks=0');
        TbaComment::query()->truncate();
        \DB::statement('set foreign_key_checks=1');
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

        TbaEvaluateEvent::with('tbaEvaluateEventMode', 'tbaEvaluateUser')->orderBy('id')->chunk(500, function ($chunks) use ($comments) {
            $result = [];
            $chunks->each(function ($q) use ($comments, &$result) {
                array_push($result, [
                    'id'           => $q->id,
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
                    'created_at'   => $q->created_at,
                    'updated_at'   => $q->updated_at,
                ]);
            });
            TbaComment::query()->insert($result);
        });
    }

    /**
     *  tbaCommentAttaches convert
     */
    public function tba_comment_attaches_data()
    {
        \DB::statement('set foreign_key_checks=0');
        TbaCommentAttach::query()->truncate();
        \DB::statement('set foreign_key_checks=1');
        TbaEvaluateEventFile::query()->orderBy('id')->chunk(200, function ($chunks) {
            $result = [];
            $chunks->each(function ($q) use (&$result) {
                array_push($result, [
                    'id'             => $q->id,
                    'tba_comment_id' => $q->tba_evaluate_event_id,
                    'name'           => $q->name,
                    'ext'            => $q->ext,
                    'image_url'      => $q->image_url,
                    'preview_url'    => $q->preview_url,
                    'created_at'     => $q->created_at,
                    'updated_at'     => $q->updated_at
                ]);
            });
            TbaCommentAttach::query()->insert($result);
        });;
    }
}
