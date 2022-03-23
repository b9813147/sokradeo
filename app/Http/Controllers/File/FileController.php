<?php

namespace App\Http\Controllers\File;

use App\Services\Tba\StatisticService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * @var StatisticService
     */
    protected $statisticService;

    /**
     * FileController constructor.
     */
    public function __construct(StatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }


    /**
     * 判斷圖片存不存在
     *
     * @param string $folderType
     * @param string $fileType
     * @param int $tbaId
     * @return array
     */
    public function exists(int $tbaId, string $folderType = 'tba', string $fileType = 'report.png'): array
    {

        $checkTPNumber = $this->statisticService->checkTPNumber($tbaId, [47, 48]);
        $path          = Storage::path("public/$folderType/$tbaId/$fileType");

        $exists = is_file($path);
        if ($checkTPNumber && $exists) {
            return [
                'status' => $exists
            ];
        }

        return [
            'status' => false
        ];

    }
}
