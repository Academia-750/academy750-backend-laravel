<?php
namespace App\Core\Resources\Storage\Services;

use App\Core\Resources\Storage\Interfaces\StorageInterface;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;

use Cloudinary\Configuration\Configuration;

Configuration::instance();

class CloudinaryStorage implements StorageInterface
{
    private $api;

    public function __construct()
    {
        $this->api = new Cloudinary();
    }

    private function urlToPublicId($material)
    {
        // Get the path from the URL
        $path = parse_url($material->url, PHP_URL_PATH);
        // Get the filename from the path
        $publicId = urldecode(pathinfo($path, PATHINFO_FILENAME));

        return "workspace_{$material->workspace_id}/" . $publicId;
        // return $publicId;
    }

    public function deleteFolder($workspace)
    {
        $workspace_folder = "workspace_{$workspace->id}";


        try {
            // If the folder doesnot exists first API will return deleted: [] second will launch an error not found
            $this->api->adminApi()->deleteAssetsByPrefix($workspace_folder);
            $this->api->adminApi()->deleteFolder($workspace_folder);
            return ['status' => 200];

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "Can't find folder with") === 0) {
                return ['status' => 404];
            }

            return ['status' => 424, 'error' => $e->getMessage()];
        }
    }
    public function deleteFile($material)
    {
        $publicId = $this->urlToPublicId($material);

        /**
         * On not found this API throws and exception instead of returning the error
         */
        try {
            $detail = $this->api->adminApi()->asset($publicId);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Resource not found') !== -1) {
                return ['status' => 404];
            }
            return ['status' => 424, 'error' => $e->getMessage()];
        }

        try {

            $result = $this->api->uploadApi()->destroy($publicId, ['resource_type' => $detail['resource_type'], "type" => $detail['type']]);

            if ($result['result'] === 'ok') {
                return ['status' => 200];
            }

            return ['status' => 424, 'error' => $result['result']];

        } catch (\Exception $e) {
            return ['status' => 424, 'error' => $e->getMessage()];

        }
    }


}