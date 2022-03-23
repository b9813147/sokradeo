<?php

namespace App\Services;

use App\Libraries\Azure\Blob;
use Yish\Generators\Foundation\Service\Service;

class BlobMediaService extends Service
{
    public $blobAllowedExtensions = [];

    protected $blobAccount = null;
    protected $blobKey = null;
    protected $blobEndpoint = null;
    protected $blobContainer = null;
    protected $blobMediaContainer = null;
    protected $blobService;

    // Construction
    public function __construct()
    {
        $this->blobAllowedExtensions = ['mp3', 'mp4', 'wav', 'webm', 'mov', 'm4a'];

        $this->blobAccount           = getenv('BLOB_ACCOUNT');
        $this->blobKey               = getenv('BLOB_KEY');
        $this->blobEndpoint          = getenv('ENDPOINT');
        $this->blobMediaContainer    = getenv('BLOB_MEDIA_CONTAINER');
        $this->blobService           = new Blob($this->blobAccount, $this->blobKey, $this->blobEndpoint);
    }

    // Upload media file to blob
    public function uploadMediaBlob(string $blobUploadFilePath, string $resourceFilePath)
    {
        if (is_null($blobUploadFilePath) || is_null($resourceFilePath)) return null;
        return $this->blobService->update($blobUploadFilePath, $this->blobMediaContainer, $resourceFilePath);
    }

    // Delete media directory
    public function deleteMediaBlobDir(string $blobDirName)
    {
        if (is_null($blobDirName)) return null;
        return $this->blobService->deleteDir($blobDirName, $this->blobMediaContainer);
    }

    // Create Blob URL with SAS from file data
    public function createBlobSASLinkFromBlobName(Object $event, string $blobName)
    {
        if (is_null($event) || is_null($blobName)) return null;

        // Get full link with SAS
        $blobPath = $event->id . "/" . $blobName; // event_id/123123123.mp4
        $requiredSAS = true;
        $blobLinkSAS = $this->getBlobLink($blobPath, $requiredSAS);

        return $blobLinkSAS;
    }

    // Create Blob URL without SAS
    public function createBlobLinkFromBlobName(Object $event, string $blobName)
    {
        if (is_null($event) || is_null($blobName)) return null;

        // Get full link without SAS
        $blobPath = $event->id . "/" . $blobName; // event_id/123123123.mp4
        $blobLink = $this->getBlobLink($blobPath);

        return $blobLink;
    }

    // Get Blob Link
    public function getBlobLink(string $blobPath, bool $requiredSAS = false)
    {
        if (is_null($blobPath)) return null;

        $blobLink = null;
        try {
            $blobLinkResult = ($requiredSAS === true)
                ? $this->blobService->GetUrlByToken($blobPath, $this->blobMediaContainer, 24)
                : $this->blobService->GetUrl($blobPath, $this->blobMediaContainer);
            $blobLink = $blobLinkResult["url"];
        } catch (Exception $e) {
            return null;
        }
        return $blobLink;
    }
}
