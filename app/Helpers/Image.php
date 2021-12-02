<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Image
{
    protected const BASE_PATH = 'public/images/';

    public static function getImage()
    {
        return 1;
    }

    public static function getImageUrl($className, $imageFileName): string
    {
        $imageSize = getimagesize(storage_path('app/' . self::BASE_PATH . $className . '/' . $imageFileName));
        return url(Storage::url(
                self::BASE_PATH . $className . '/' . $imageFileName
            )) . "?w={$imageSize[0]}&h={$imageSize[1]}";
    }

    /**
     * @param $className
     * @param $image
     * @return string imageFileName
     */
    public static function saveImage($className, $image): string
    {
        $path = Storage::put(self::BASE_PATH . $className, $image);
        return basename($path);
    }

    public static function deleteImage($className, $imageFileName): bool
    {
        $imagePath = self::BASE_PATH . $className . '/' . $imageFileName;
        if ($imageFileName && Storage::exists($imagePath)) {
            return Storage::delete($imagePath);
        }
        return true;
    }
}
