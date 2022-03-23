<?php

namespace App\Services;

use App\Libraries\Azure\Blob;
use Yish\Generators\Foundation\Service\Service;

class TbaCommentBlobMediaService extends Service
{
    public $blobAllowedVideoExtentions = [];
    public $blobAllowedMiscExtentions = [];
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
        $this->blobAllowedVideoExtentions = ['mp3', 'mp4', 'wav', 'webm', 'mov', 'm4a'];
        $this->blobAllowedMiscExtentions = ['jpg', 'jpeg', 'png'];
        $this->blobAllowedExtensions = array_merge($this->blobAllowedVideoExtentions, $this->blobAllowedMiscExtentions);

        $this->blobAccount           = getenv('BLOB_ACCOUNT');
        $this->blobKey               = getenv('BLOB_KEY');
        $this->blobEndpoint          = getenv('ENDPOINT');
        $this->blobMediaContainer    = getenv('BLOB_MEDIA_CONTAINER');
        $this->blobService           = new Blob($this->blobAccount, $this->blobKey, $this->blobEndpoint);
    }

    // Get allowed video extensions
    public function blobAllowedVideoExtentions()
    {
        return $this->blobAllowedVideoExtentions;
    }

    // Get allowed misc. extensions
    public function blobAllowedMiscExtentions()
    {
        return $this->blobAllowedMiscExtentions;
    }

    // Get allowed extensions
    public function getBlobAllowedExtensions()
    {
        return $this->blobAllowedExtensions;
    }

    /**
     * Check if file extension is allowed and media
     * @param {string} $fileExtension
     * @return {boolean}
     */
    public function isExtMedia(string $fileExtension)
    {
        $fileExtension = strtolower($fileExtension);
        return in_array($fileExtension, $this->blobAllowedVideoExtentions);
    }

    /**
     * Check if file extension is allowed and image
     * @param {string} $fileExtension
     * @return {boolean}
     */
    public function isExtMisc(string $fileExtension)
    {
        $fileExtension = strtolower($fileExtension);
        return in_array($fileExtension, $this->blobAllowedMiscExtentions);
    }

    /**
     * Check if file extension is allowed
     * @param {string} $fileExtension
     * @return {boolean}
     */
    public function isExtAllowed(string $fileExtension)
    {
        $fileExtension = strtolower($fileExtension);
        return in_array($fileExtension, $this->blobAllowedExtensions);
    }

    // Upload media file to blob
    public function uploadMediaBlob(string $blobDestDir, string $fileSrcDir)
    {
        if (is_null($blobDestDir) || is_null($fileSrcDir)) return null;
        return $this->blobService->update($blobDestDir, $this->blobMediaContainer, $fileSrcDir);
    }

    // Delete media directory
    public function deleteMediaBlobDir(string $blobDestDir)
    {
        if (is_null($blobDestDir)) return null;
        return $this->blobService->deleteDir($blobDestDir, $this->blobMediaContainer);
    }

    // Create Blob URL with SAS from file data
    public function getBlobSASLink(int $dirId, string $blobName)
    {
        if (is_null($dirId) || is_null($blobName)) return null;

        // Get full link with SAS
        $blobPath = $dirId . "/" . $blobName; // dirId/123123123.mp4
        $requiredSAS = true;
        $blobLinkSAS = $this->getBlobLink($blobPath, $requiredSAS);

        return $blobLinkSAS;
    }

    // Create Blob URL without SAS from file data
    public function getPlainBlobLink(int $dirId, string $blobName)
    {
        if (is_null($dirId) || is_null($blobName)) return null;

        // Get full link without SAS
        $blobPath = $dirId . "/" . $blobName; // dirId/123123123.mp4
        $blobLinkSAS = $this->getBlobLink($blobPath);

        return $blobLinkSAS;
    }

    // Get Blob Link
    public function getBlobLink(string $blobPath, bool $requiredSAS = false)
    {
        $blobLink = null;
        if (is_null($blobPath)) return $blobLink;
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
