<?php

namespace App\Services;

use App\Services\BlobMediaService;
use App\Models\TbaEvaluateEventFile;

use File;
use Illuminate\Support\Facades\Storage;
use Yish\Generators\Foundation\Service\Service;

class TbaFileService extends Service
{
    protected $blobMediaService;
    protected $blobAllowedExtensions = [];

    // construct
    public function __construct()
    {
        $this->blobMediaService = new BlobMediaService();
        $this->blobAllowedExtensions = $this->blobMediaService->blobAllowedExtensions;
    }

    // event file upload
    public function eventFileUpload(Object $event, array $file)
    {
        if (is_null($event) || is_null($file)) return;

        // Create dir
        try {
            $eventFile = $event->tbaEvaluateEventFiles()->create();
            $subDir = $event->tba_id . '/evaluate_event_file/' . $eventFile->id;
            Storage::makeDirectory('public/tba/' . $subDir); // create dir
            $this->eventFileUploadHandler($event, $file, $file['path'], $subDir);
        } catch (Exception $e) {
            return null;
        }
    }

    // event file update
    public function eventFileUpdate(Object $event, Object $eventOldFile, array $file)
    {
        if (is_null($event) || is_null($eventOldFile) || is_null($file)) return;

        // Clear dir and upload
        try {
            $subDir = $event->tba_id . '/evaluate_event_file/' . $eventOldFile->id;
            File::cleanDirectory(storage_path('app/public/tba/' . $subDir)); // clear files in folder
            $this->eventFileUploadHandler($event, $file, $file['path'], $subDir);
        } catch (Exception $e) {
            return null;
        }
    }

    // event file delete
    public function eventFileDelete(Object $event, string $eventFilePath)
    {
        if (is_null($event) || is_null($eventFilePath)) return;

        try {
            // Delete file locally
            $eventFileIds  = collect();
            TbaEvaluateEventFile::where('tba_evaluate_event_id', $event->id)->get()->each(function ($v) use ($eventFilePath, $eventFileIds) {
                Storage::delete($eventFilePath . $v->id);
                $eventFileIds->push($v->id);
            });
            TbaEvaluateEventFile::whereIn('id', $eventFileIds)->delete();
            // Delete file on Azure Blob
            $this->blobMediaService->deleteMediaBlobDir($event->id);
        } catch (Exception $e) {
            return null;
        }
    }

    // event file upload handler
    private function eventFileUploadHandler(Object $event, array $file, string $filePath, string $subDir)
    {
        $fileNamePlain = time(); // Ex.) 1615964864
        $fileExtension = strtolower($file['ext']);
        $fileNameWithExt = $fileNamePlain . '.' . $fileExtension; // Ex.) 1615964864.mp4, 1615964864.jpg

        // If file is media, create plainFile as a reference (to Azure Blob)
        // else copy and store raw file locally
        if (in_array($fileExtension, $this->blobAllowedExtensions)) {
            // Upload file to Blob
            $blobUploadFilePath = $event->id . '/' . $fileNameWithExt; // event_id/filename
            $this->blobMediaService->deleteMediaBlobDir($event->id); // clear files dir (event_id)
            $this->blobMediaService->uploadMediaBlob($blobUploadFilePath, $filePath);
            // Update table
            $event->tbaEvaluateEventFiles()->update(['name' => $fileNamePlain, 'ext' => $fileExtension, 'image_url' => null, 'preview_url' => null]);
            // Set up link
            $event->image_url = null;
            $event->media_url = $this->blobMediaService->createBlobSASLinkFromBlobName($event, $fileNameWithExt);
        } else {
            // Copy file to local storage
            copy($filePath, storage_path('app/public/tba/' . $subDir) . '/' . $fileNameWithExt);
            $imageUrl = url('storage/tba/' . $subDir . '/' . $fileNameWithExt);
            // Update table
            $event->tbaEvaluateEventFiles()->update(['name' => null, 'ext' => null, 'image_url' => $imageUrl, 'preview_url' => $imageUrl]);
            // Set up link
            $event->image_url = $imageUrl;
            $event->media_url = null;
        }
    }
}
