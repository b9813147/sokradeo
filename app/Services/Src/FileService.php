<?php

namespace App\Services\Src;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Path\File as FilePath;
use App\Services\Src\SrcService;
use App\Repositories\FileRepository;

/*
 * 提醒:需求若是支援多個檔案系統 請比照VodService類組架構擴充設計 此檔案服務建議特化成LocalFileService
 * */
class FileService implements SrcService
{
    use FilePath;
    
    protected $fileRepo = null;
    
    //
    public function __construct(FileRepository $fileRepo)
    {
        $this->fileRepo = $fileRepo;
    }
    
    public function createSrc($resrcId, $src)
    {
        $file = $this->fileRepo->createFile($resrcId, [
                'name' => $src['name'],
                'ext'  => $src['ext'],
        ]);
        
        (is_string($src['file']))
        ? $this->moveFile  ($file->id, $src['file'])
        : $this->uploadFile($file->id, $src['file']); 
    }
    
    public function getDetail($src)
    {
        // 待實作
    }
    
    public function getExecuting($src)
    {
        $name = $src->name . '.' . $src->ext;
        return Response::download($this->pathFile($src->id, true), $name);
    }
    
    /**
     * @param  string  $file  local file path
     */
    protected function moveFile($fileId, $file)
    {
        if (! Storage::exists($file)) {
            return;
        }
        
        $tar = $this->pathFile($fileId);
        Storage::move($file, $tar);
    }
    
    /**
     * @param  \Illuminate\Http\UploadedFile  $file  uploaded file
     */
    protected function uploadFile($fileId, $file)
    {
        // 待實作
    }
    
}
