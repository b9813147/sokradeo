<?php

namespace Tests\Feature;

use App\Models\Tba;
use App\Models\TbaAnnex;
use Maatwebsite\Excel\Files\Filesystem;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRemoveRedundantFile()
    {
        $tbas     = Tba::with('tbaAnnexs')->take(500)->get();
        $tbaAnnex = collect();
        $tbas->each(function ($tba) use ($tbaAnnex) {
            if ($tba->tbaAnnexs()->get()->isNotEmpty()) {
                $selectSql = "
                    COUNT(CASE WHEN type = 'HiTeachNote'  THEN tba_id END) AS HiTeachNote,
                    COUNT(CASE WHEN type = 'LessonPlan'  THEN tba_id END) AS LessonPlan,
                    COUNT(CASE WHEN type = 'Material'  THEN tba_id END) AS Material,
                    tba_id
                    ";

                $tbaAnnex = $tba->tbaAnnexs()->selectRaw($selectSql)->orderBy('created_at', 'ASC')
                    ->having('HiTeachNote', '>', 1)
                    ->having('LessonPlan', '>', 1)
                    ->having('Material', '>', 1)
                    ->get();

                if ($tbaAnnex->isNotEmpty()) {
                    $tbaAnnexes = TbaAnnex::query()->selectRaw('group_concat(id) as ids')->where('tba_id', $tbaAnnex->first()->tba_id)->groupBy('type')->orderBy('created_at', 'ASC')->get();
                    $tbaAnnexes->each(function ($q) {
                        $explode = explode(',', $q->ids);
                        $annex   = TbaAnnex::query()->find($explode[0]);

//                        dump($annex->resource->id);

//                        dd(\Storage::deleteDirectory(storage_path('test/app')));
//                        dd(\Storage::delete(storage_path('app/file/1')),storage_path('app/file/1'));
//                        dump($annex->resource->id);
                        $annex->resource->delete();
                        $annex->delete();
//                       dump(TbaAnnex::query()->find($explode[0]));
//                        dump();
                    });


//                    $tbaAnnex->push($toArray);
                }
            }
        });
////        dd($tbaAnnex);
//        $tbaAnnex->flatMap(function ($tba) {
//            dd($tba[0]->tba_id);
//        $collection = TbaAnnex::query()->where('tba_id', $tba['tba_id'])->get();
//            dd($collection);
//        });
        dd();
//        $toArray = TbaAnnex::query()->where('type','HiTeachNote')->take(50)->get()->toArray();
    }
}
