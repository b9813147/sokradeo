<?php


namespace App\Libraries\Azure;


use Illuminate\Support\Carbon;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;

class Blob
{
    protected $account = null;
    protected $key = null;
    protected $endpoint = null;
    protected $connectionsString = null;

    public function __construct(string $account, string $key, string $endpoint)
    {
        $this->account           = $account;
        $this->key               = $key;
        $this->endpoint          = $endpoint;
        $this->connectionsString = "DefaultEndpointsProtocol=https;AccountName=$account;AccountKey=$key;EndpointSuffix=$endpoint";
    }

    /**
     * 上傳檔案 重複及更新
     * @param string $blob
     * @param string $container
     * @param string $resourceFilePath
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $blob, string $container, string $resourceFilePath): \Illuminate\Http\JsonResponse
    {
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($this->connectionsString);

        $adapter = new AzureBlobStorageAdapter($blobClient, $container);
        $filesystem = new Filesystem($adapter);
        $putFile    = $filesystem->put($blob, fopen($resourceFilePath, 'r'));


        if (!$putFile) {
            return response()->json(['message' => 'fail'], 400);
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * 上傳檔案 重複及更新
     * @param string $blob
     * @param string $container
     * @return \Illuminate\Http\JsonResponse
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function delete(string $blob, string $container): \Illuminate\Http\JsonResponse
    {
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($this->connectionsString);

        $adapter = new AzureBlobStorageAdapter($blobClient, $container);;
        $filesystem = new Filesystem($adapter);
        $deletedFile = $filesystem->delete($blob);

        if (!$deletedFile) {
            return response()->json(['message' => 'fail'], 400);
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * 上傳檔案 重複及更新
     * @param $blob
     * @param $container
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDir(string $directory, string $container): \Illuminate\Http\JsonResponse
    {
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($this->connectionsString);

        $adapter = new AzureBlobStorageAdapter($blobClient, $container);;
        $filesystem = new Filesystem($adapter);
        $deletedDir = $filesystem->deleteDir($directory);

        if (!$deletedDir) {
            return response()->json(['message' => 'fail'], 400);
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Get plain Blob URL
     *
     * @param string $blob
     * @param string $container
     * @return array
     */
    public function GetUrl(string $blob, string $container): array
    {
        $blobClient                       = BlobRestProxy::createBlobService($this->connectionsString);
        $adapter                          = new AzureBlobStorageAdapter($blobClient, $container);
        $filesystem                       = new Filesystem($adapter);

        $blobUrl = $blobClient->getBlobUrl($container, $blob);

        return $filesystem->has($blob)
            ? $message = [
                'url'     => $blobUrl,
                'message' => 'success'
            ] :
            $message = [
                'url'     => '',
                'message' => 'File does not exist'
            ];
    }

    /**
     * 取得授權URL
     *
     * @param string $blob
     * @param string $container
     * @param int $expiryHour
     * @return array
     */
    public function GetUrlByToken(string $blob, string $container, int $expiryHour = 1): array
    {
        $blobClient                       = BlobRestProxy::createBlobService($this->connectionsString);
        $adapter                          = new AzureBlobStorageAdapter($blobClient, $container);
        $filesystem                       = new Filesystem($adapter);
        $tableSharedAccessSignatureHelper = new BlobSharedAccessSignatureHelper($this->account, $this->key);

        $expiryDateTime = Carbon::now()->addHour($expiryHour)->toDate();
        $startDateTime = Carbon::now()->subHour(1)->toDate();

        $generaToken = $tableSharedAccessSignatureHelper->generateBlobServiceSharedAccessSignatureToken(
            'b',
            "$container/$blob",
            'r',
            $expiryDateTime,
            $startDateTime
        );

        $blobUrl = $blobClient->getBlobUrl($container, $blob) . "?$generaToken";

        return $filesystem->has($blob)
            ? $message = [
                'url'     => $blobUrl,
                'message' => 'success'
            ] :
            $message = [
                'url'     => '',
                'message' => 'File does not exist'
            ];
    }
}
