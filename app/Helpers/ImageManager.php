<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;

class ImageManager
{
    // Uploads an image to the specified directory and resizes it
    public static function upload(string $dir, string $format, $image = null, $check = null)
    {
        if ($image != null) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            
            // Create directory if it does not exist
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            // Resize and save the image
            $img = Image::make($image->path());
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save('storage/app/public/'.$dir.'/'.$imageName);
            
            return $imageName;
        }

        return null;
    }

    // Updates an image by deleting the old one and uploading the new one
    public static function imgUpdate(string $dir, $old_image, string $format, $image = null)
    {
        if (Storage::disk('public')->exists($dir . '/' . $old_image)) {
            Storage::disk('public')->delete($dir . '/' . $old_image);
        }

        return ImageManager::move($dir, $image, $format);
    }

    // Deletes an image from storage
    public static function imgDelete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
    }

    // Moves the image from the temporary folder to the specified directory
    public static function move(string $dir, $image, string $format)
    {
        if ($image != null) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            
            // Check if the directory exists; if not, create it
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            
            // Move the image to the specified directory
            $image->storeAs('public/'.$dir, $imageName);

            return $imageName;
        }

        return null;
    }
}
