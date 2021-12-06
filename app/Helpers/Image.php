<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Image
{
    protected const BASE_PATH = 'public/images/';

    /**
     * @param string $className
     * @param string $imageFileName
     * @return string
     */
    public static function getImageUrl(string $className, string $imageFileName): string
    {
        $imageSize = getimagesize(storage_path('app/' . self::BASE_PATH . $className . '/' . $imageFileName));
        return url(
                Storage::url(
                    self::BASE_PATH . $className . '/' . $imageFileName
                )
            ) . "?w={$imageSize[0]}&h={$imageSize[1]}";
    }

    /**
     * @param string $className
     * @param $image
     * @return string imageFileName
     */
    public static function saveImage(string $className, $image): string
    {
        $path = Storage::put(self::BASE_PATH . $className, $image);
        return basename($path);
    }

    /**
     * @param string $className
     * @param string $imageFileName
     * @return bool
     */
    public static function deleteImage(string $className, string $imageFileName): bool
    {
        $imagePath = self::BASE_PATH . $className . '/' . $imageFileName;
        if ($imageFileName && Storage::exists($imagePath)) {
            return Storage::delete($imagePath);
        }
        return true;
    }
}
