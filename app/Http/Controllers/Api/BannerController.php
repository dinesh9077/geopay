<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class BannerController extends Controller
{
    //
    use ApiResponseTrait;
    public function create(Request $request)
    {
        $user = App::make("authUser");

        if ($user->role != 'admin') {
            return $this->errorResponse('You are not authorized for this request.');
        }
        // Decode the base64 encoded data
        $decodedData = json_decode(base64_decode($request->input('data')), true);

        if ($decodedData === null || !is_array($decodedData)) {
            return $this->errorResponse('Invalid input data');
        }



        // Validate the input data
        $validator = Validator::make($decodedData, [
            'app_image' => 'required|string',
            'web_image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        DB::beginTransaction();

        try {

            // Process the app_image
            $appImageData = base64_decode($decodedData['app_image']);
            if ($appImageData === false) {
                return $this->errorResponse('Invalid web_image data.');
            }

            // Generate a unique filename for app_image
            $appImageName = Str::random(40) . '.png'; // Adjust the extension based on your actual image format
            $appImagePath = 'banners/' . $appImageName;
            Storage::disk('public')->put($appImagePath, $appImageData);

            // Process the web_image
            $webImageData = base64_decode($decodedData['web_image']);
            if ($webImageData === false) {
                return $this->errorResponse('Invalid web_image data.');
            }

            // Generate a unique filename for web_image
            $webImageName = Str::random(40) . '.png'; // Adjust the extension based on your actual image format
            $webImagePath = 'banners/' . $webImageName;
            Storage::disk('public')->put($webImagePath, $webImageData);


            $bannerData = [
                'name' => $decodedData['name'],
                'app_image' => $appImagePath,
                'web_image' => $webImagePath,
                'text' => $decodedData['text'],
            ];


            // Create the banner
            $banner = Banner::create($bannerData);

            DB::commit();

            return $this->successResponse('banner', $banner, 'Banner created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getBannerData()
    {
        $banners = Banner::get();

        $bannersWithUrls = $banners->map(function ($banner) {
            if ($banner->app_image) {
                $banner->app_image = asset('storage/' . $banner->app_image);
            }
            if ($banner->web_image) {
                $banner->web_image = asset('storage/' . $banner->web_image);
            }
            return $banner;
        });

        return $this->successResponse('banner_list', $bannersWithUrls, 'Banner data fetch successfully');
    }
}
