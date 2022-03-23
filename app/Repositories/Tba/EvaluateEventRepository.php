<?php

namespace App\Repositories\Tba;

use App\Models\TbaVideoMap;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Path\Tba as TbaPath;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateEventFile;
use App\Models\TbaEvaluateEventMode;
use App\Models\TbaEvaluateUser;
use App\Services\TbaFileService;
use App\Services\BlobMediaService;

class EvaluateEventRepository
{
    use TbaPath;

    protected $tbaFileService;
    protected $blobMediaService;
    protected $blobAllowedExtensions = [];

    public function __construct()
    {
        $this->tbaFileService = new TbaFileService();
        $this->blobMediaService = new BlobMediaService();
    }

    //
    public function getEvents($tbaId, $conds = [], $userList = null)
    {
        $video_map = TbaVideoMap::where('tba_id', $tbaId)->first();
        $result = [];
        $result[] = [
            'user'       => [
                'id'     => 0,
                'userid' => 0,
                'name'   => ''
            ],
            'labels'     => [''],
            'labeltypes' => 1,
            'range'      => ['0', '0'],
            'datasets'   => [[
                'colors'     => [['']],
                'details'    => [[empty($video_map->tba_start) ? 0 : $video_map->tba_start]],
                'eventimgs'  => [['']],
                'eventmedia' => [['']],
                'eventtexts' => [['{firstRow}']],
                'ids'        => [[0]],
                'labelsmode' => [['']],
                'labelsmodeid' => [['']],
            ]],
        ];

        foreach ($userList as $user) {
            if (is_null($conds['tba_evaluate_events.user_id'])) {
                $conds['tba_evaluate_events.tba_evaluate_user_id'] = $user->id;
            }

            $events = DB::table('tba_evaluate_events')
                ->where('tba_id', $tbaId)
                ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', '=', 'tba_evaluate_event_modes.id')
                ->where($conds)
                ->orderBy('order', 'asc')
                ->select('time_point', 'event')
                ->get();

            $pattern = collect();
            $events->unique('event')->each(function ($v) use($pattern) {
                $pattern->push($v->event);
            });

            // labels
            $labels = $this->parseEventsToLabels($pattern);

            // range
            $range = ['min' => $events->min('time_point'), 'max' => $events->max('time_point')];

            // dataset
            $dataset = $this->getDataset($tbaId, $pattern, $conds);

            $labelsmode   = [];
            $labelsmodeid = [];
            $labeltypes   = [];
            $eventtexts   = [];
            $eventimages  = [];
            $eventmedia   = [];

            foreach ($dataset['ids'] as $index_events => $events) {
                $labelsmode[$index_events]  = [];
                $eventtexts[$index_events]  = [];
                $eventimages[$index_events] = [];
                foreach ($events as $index_eventID => $eventID) {
                    $eventData = $this->getEvent($eventID);
                    if ($index_eventID === 0) {
                        $labeltypes[] = $eventData['type'];
                    }
                    $labelsmode[$index_events][]   = $eventData['tag'];
                    $labelsmodeid[$index_events][] = $eventData['mode'];
                    $eventtexts[$index_events][]   = ($eventData['text'] === null) ? "" : $eventData['text'];
                    $eventimages[$index_events][]  = $eventData['image'];
                    $eventmedia[$index_events][]   = $eventData['media'];
                }
            }
            $dataset['labelsmode']   = $labelsmode;
            $dataset['labelsmodeid'] = $labelsmodeid;
            $dataset['eventtexts']   = $eventtexts;
            $dataset['eventimgs']    = $eventimages;
            $dataset['eventmedia']   = $eventmedia;

            $result[] = [
                'user'        => [
                    'id'     => $user->id,
                    'userid' => $user->user_id,
                    'name'   => $user->name
                ],
                'labels'      => $labels,
                'labeltypes'  => $labeltypes,
                'range'       => $range,
                'datasets'    => [$dataset],
            ];

            // release
            $events  = null;
            $pattern = null;
            $cond_dataset = null;
            unset($events);
            unset($pattern);
            unset($cond_dataset);
        }

        return $result;
    }

    //
    public function getEvent($eventId)
    {
        $event = TbaEvaluateEvent::with('tbaEvaluateEventMode')->findOrFail($eventId);
        $files = TbaEvaluateEventFile::where('tba_evaluate_event_id', $eventId)->first();

        return [
                'id'    => $event->id,
                'event' => Lang::get('app/tba/evaluate-event'     )[$event->tbaEvaluateEventMode->event],
                'mode'  => is_null($event->tbaEvaluateEventMode->mode) ? $event->tbaEvaluateEventMode->event : $event->tbaEvaluateEventMode->mode,
                'tag'   => Lang::get('app/tba/evaluate-event-mode')[is_null($event->tbaEvaluateEventMode->mode) ? $event->tbaEvaluateEventMode->event : $event->tbaEvaluateEventMode->mode],
                'type'  => $event->tbaEvaluateEventMode->type,
                'time'  => $event->time_point,
                'text'  => $event->text,
                'image' => (!is_null($files) && $files->image_url) ? $files->image_url : null,
                'media' => (!is_null($files) && $files->name && $files->ext)
                                ? $this->blobMediaService->createBlobSASLinkFromBlobName($event, $files->name.".".$files->ext) // abc.mp4
                                : null
        ];
    }

    // Create Event
    public function createEvent($tbaId, & $event, & $files = null)
    {
        $event['tba_id'] = $tbaId;
        $event = TbaEvaluateEvent::create($event);

        // File Handling
        if (!is_null($files)) {
            foreach ($files as $v) {
                if ($v['image_url'] === null && $v['media_url'] === null && is_file($v['path'])) {
                    $this->tbaFileService->eventFileUpload($event, $v);
                } else {
                    $event->tbaEvaluateEventFiles()->create($v);
                }
            }
        }

        return $event;
    }

    // Update Event
    public function updateEvent($eventId, & $event, & $files = null)
    {
        $event = TbaEvaluateEvent::where('id', $eventId)->update($event);
        $event = TbaEvaluateEvent::findOrFail($eventId);
        $event->image_url = null;
        $event->media_url = null;

        if (!is_null($files)) {
            foreach ($files as $v) {
                $eventFile = $event->tbaEvaluateEventFiles()->where('tba_evaluate_event_id', $eventId)->first();
                $filePath = $v['path'];
                // File Handling
                if (is_null($eventFile) && is_file($filePath)) {
                    $this->tbaFileService->eventFileUpload($event, $v); // Create
                } else if (!is_null($eventFile) && is_file($filePath)) {
                    $this->tbaFileService->eventFileUpdate($event, $eventFile, $v); // Update
                }
            }
        }

        return $event;
    }

    // Delete Event
    public function deleteEvent($eventId)
    {
        $event = TbaEvaluateEvent::findOrFail($eventId);
        $eventFilePath = $this->pathTbaEvalEventFile($event->tba_id);
        $this->tbaFileService->eventFileDelete($event, $eventFilePath);
        $event->delete();
    }

    //
    public function createUsersEventGroups($tbaId, & $users)
    {
        foreach ($users as $userInfo) {

            $this->createUserEventGroups($tbaId, $userInfo);

        }
    }

    //
    public function createUserEventGroups($tbaId, & $user)
    {
        try {

            $evalUser = TbaEvaluateUser::firstOrCreate([
                    'tba_id'   => $tbaId,
                    'user_id'  => $user['id'],
                    'identity' => $user['identity'],
            ]);

        } catch(Exception $e) {
            return;
        }

        $timestamp         = date("Y-m-d H:i:s");
        $evalEventFilePath = $this->pathTbaEvalEventFile($tbaId);

        foreach ($user['events'] as $eventInfo) {

            $eventMode = TbaEvaluateEventMode::where(['event' => $eventInfo['event'], 'mode' => $eventInfo['mode']])->first();
            if (is_null($eventMode)) {
                continue;
            }

            $events = [];
            foreach ($eventInfo['data'] as $data) {

                $event = [
                        'tba_evaluate_user_id'       => $evalUser->id,
                        'tba_id'                     => $tbaId,
                        'tba_evaluate_event_mode_id' => $eventMode->id,
                        'time_point'                 => $data['time'],
                        'text'                       => $data['text'],
                        'evaluate_type'              => 1,
                ];

                if (isset($data['files']) && !empty($data['files'])) {
                    $this->createEvent($tbaId, $event, $data['files']);
                    continue;
                }

                $event['created_at'] = $event['updated_at'] = $timestamp;
                array_push($events, $event);
            }

            DB::table('tba_evaluate_events')->insert($events);
        }
    }

    //
    public function deleteUsersEvent($tbaId)
    {
        $query = DB::table('tba_evaluate_events')
            ->join('tba_evaluate_users', 'tba_evaluate_users.id', 'tba_evaluate_events.tba_evaluate_user_id')
            ->leftJoin('tba_evaluate_event_files', 'tba_evaluate_event_files.tba_evaluate_event_id', 'tba_evaluate_events.id')
            ->where('tba_evaluate_events.tba_id', $tbaId)
            ->where('tba_evaluate_events.evaluate_type', 1)
            ->select('tba_evaluate_events.id as evaluate_event_id', 'tba_evaluate_users.id as evaluate_user_id', 'tba_evaluate_event_files.id as evaluate_file_id')
            ->get();
        $events = $query->toArray();
        $evaluate_userIds = [];
        $evaluate_eventIds = [];
        $evaluate_fileIds = [];
        foreach ($events as $event) {
            if (!in_array($event->evaluate_user_id, $evaluate_userIds)) {
                array_push($evaluate_userIds, $event->evaluate_user_id);
            }
            if (!in_array($event->evaluate_event_id, $evaluate_eventIds)) {
                array_push($evaluate_eventIds, $event->evaluate_event_id);
            }
            if (!in_array($event->evaluate_file_id, $evaluate_fileIds) && !empty($event->evaluate_file_id)) {
                array_push($evaluate_fileIds, $event->evaluate_file_id);
            }
        }
        if (!empty($evaluate_fileIds)) {
            DB::table('tba_evaluate_event_files')->whereIn('id', $evaluate_fileIds)->delete();
        }
        if (!empty($evaluate_eventIds)) {
            DB::table('tba_evaluate_events')->whereIn('id', $evaluate_eventIds)->delete();
        }
        if (!empty($evaluate_userIds)) {
            DB::table('tba_evaluate_users')->whereIn('id', $evaluate_userIds)->delete();
        }
    }

    //
    private function parseEventsToLabels(& $events)
    {
        $mapEventLabel = Lang::get('app/tba/evaluate-event');
        return $events->map(function ($v) use ($mapEventLabel) {
            return $mapEventLabel[$v];
        });
    }

    //
    private function getDataset($tbaId, & $pattern, & $conds = [])
    {
        $dataset = [
                'ids'     => [],
                'colors'  => [],
                'details' => [],
        ];

        $events = DB::table('tba_evaluate_events')
            ->where('tba_id', $tbaId)
            ->where($conds)
            ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', '=', 'tba_evaluate_event_modes.id')
            ->orderBy('order',      'asc')
            ->orderBy('time_point', 'asc')
            ->select('tba_evaluate_events.id', 'time_point', 'text', 'event', 'mode', 'type', 'color', 'style', 'order')
            ->get();

        foreach ($pattern as $i => $event) {

            array_push($dataset['ids'    ], []);
            array_push($dataset['colors' ], []);
            array_push($dataset['details'], []);

            $tmpEvents = $events->where('event', $event);
            foreach ($tmpEvents as $v) {
                array_push($dataset['ids'    ][$i], $v->id        );
                array_push($dataset['colors' ][$i], $v->color     );
                array_push($dataset['details'][$i], $v->time_point);
            }
        }

        return $dataset;
    }

    /* 註解:參考使用
    //
    private function getDatasets($tbaId, & $pattern)
    {
        $datasets = collect();

        $tbaEvalUsers = Tba::findOrFail($tbaId)->tbaEvaluateUsers()->orderBy('identity', 'asc')->with('user')->get();
        foreach ($tbaEvalUsers as $tbaEvalUser) {
            $user = $tbaEvalUser->user;
            $dataset = $this->getDataset($tbaId, $tbaEvalUser->id, $pattern);
            $dataset['evalUser'] = [
                    'id'       => $tbaEvalUser->id,
                    'identity' => $tbaEvalUser->identity,
                    'email'    => $user->email,
                    'name'     => $user->name,
            ];
            $datasets->push($dataset);
        }

        return $datasets;
    }

    //
    private function getDataset($tbaId, $tbaEvalUserId, & $pattern)
    {
        $dataset = [
                'ids'     => [],
                'colors'  => [],
                'details' => [],
        ];

        $events = DB::table('tba_evaluate_events')
                ->where('tba_id',               $tbaId)
                ->where('tba_evaluate_user_id', $tbaEvalUserId)
                ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', '=', 'tba_evaluate_event_modes.id')
                ->orderBy('order',      'asc')
                ->orderBy('time_point', 'asc')
                ->select('tba_evaluate_events.id', 'time_point', 'text', 'event', 'mode', 'type', 'color', 'style', 'order')
                ->get();

        foreach ($pattern as $i => $event) {

            array_push($dataset['ids'    ], []);
            array_push($dataset['colors' ], []);
            array_push($dataset['details'], []);

            $tmpEvents = $events->where('event', $event);
            foreach ($tmpEvents as $v) {
                array_push($dataset['ids'    ][$i], $v->id        );
                array_push($dataset['colors' ][$i], $v->color     );
                array_push($dataset['details'][$i], $v->time_point);
            }
        }

        return $dataset;
    }
    */
}
