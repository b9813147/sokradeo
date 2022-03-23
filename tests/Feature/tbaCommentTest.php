<?php

namespace Tests\Feature;

use App\Models\TbaComment;
use App\Models\TbaCommentAttach;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateEventFile;
use App\Services\BlobMediaService;
use App\Services\TbaCommentBlobMediaService;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class tbaCommentTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // tbaComment convert
    public function test_run_base_data()
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
        $this->assertTrue(true);
    }

    /**
     *  tbaCommentAttaches convert
     */
    public function test_run_tba_comment_attaches()
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
        });
        $this->assertTrue(true);
    }

    public function test_get_comment($id)
    {
        $model = TbaComment::with('tbaCommentAttaches', 'tag')->findOrFail($id);
        \App::setLocale('tw');
        $file    = $model->tbaCommentAttaches->first();
        $content = json_decode($model->tag->content, true);

        return [
            "id"    => $model->id,
            "event" => $model->tag_id,
            "mode"  => $model->tag_id,
            "tag"   => $content['customize'] ?: $content[\App::getLocale()],
            "type"  => $model->tag->is_positive,
            "time"  => $model->time_point,
            "text"  => $model->text,
            'image' => (!is_null($file) && $file->image_url) ? $file->image_url : null,
            'media' => (!is_null($file) && $file->name && $file->ext)
                ? app(TbaCommentBlobMediaService::class)->getBlobSASLink($model->id, $file->name . "." . $file->ext) // abc.mp4
                : null
        ];

//        $tbaComment = TbaComment::query()->join('tba_tags', 'tba_comments.tba_tag_id', 'tba_tags.id')->findOrFail(930);

    }

    public function test_getEvent($eventId = 930)
    {
        $event = TbaEvaluateEvent::with('tbaEvaluateEventMode')->findOrFail($eventId);
        $files = TbaEvaluateEventFile::where('tba_evaluate_event_id', $eventId)->first();

//        dd(Lang::get('app/tba/evaluate-event-mode')[is_null($event->tbaEvaluateEventMode->mode) ? $event->tbaEvaluateEventMode->event : $event->tbaEvaluateEventMode->mode]);
        $data = [
            'id'    => $event->id,
            'event' => Lang::get('app/tba/evaluate-event')[$event->tbaEvaluateEventMode->event],
            'mode'  => is_null($event->tbaEvaluateEventMode->mode) ? $event->tbaEvaluateEventMode->event : $event->tbaEvaluateEventMode->mode,
            'tag'   => Lang::get('app/tba/evaluate-event-mode')[is_null($event->tbaEvaluateEventMode->mode) ? $event->tbaEvaluateEventMode->event : $event->tbaEvaluateEventMode->mode],
            'type'  => $event->tbaEvaluateEventMode->type,
            'time'  => $event->time_point,
            'text'  => $event->text,
            'image' => (!is_null($files) && $files->image_url) ? $files->image_url : null,
            'media' => (!is_null($files) && $files->name && $files->ext)
                ? app(BlobMediaService::class)->createBlobSASLinkFromBlobName($event, $files->name . "." . $files->ext) // abc.mp4
                : null
        ];
        dd($data);
    }

    public function test_get_events()
    {
//        app(EvaluateEventRepository::class)->getEvents($tbaId, ['tba_evaluate_events.user_id' => NULL], $evalUsers)
    }
}

