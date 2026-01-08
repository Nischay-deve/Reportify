<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait MediaManager
{

    /**
     * @param $file - The resource to upload
     * @param $resizeNeeded - Do we need to resize the rouce - Boolean - true / false
     * @param $basePath - The basePath where the file has to be uploaded
     * @return $path - Uploaded path
     */
    public function uploadMedia($basePath = '', $sourceFile, $resizeNeeded = true, $resizeWidth = 1500, $basePathIncludeImage = false)
    {
        Log::info('set memory | ' . config('app.memory_limit_media_manager'));
        
        @ini_set("memory_limit", config('app.memory_limit_media_manager'));

        if($basePathIncludeImage){
            $this->storeDirect($basePath, $sourceFile, $resizeNeeded, $resizeWidth);
        }else{
              
            if(is_object($sourceFile)){
                Log::info('upload filename is_object');
                $file = $sourceFile;
                $filename = $this->generateCleanFileName($file->getClientOriginalName());
            }else{
                Log::info('upload filename is path');
                $file = $sourceFile['path'];
                $filename = $this->generateCleanFileName($sourceFile['originalName']);
            }        

            Log::info('upload filename | ' . $filename);
            Log::info('upload basePath | ' . $basePath);

            if ($resizeNeeded) {
                try {
                    $img = Image::make($file)
                        ->orientate()
                        ->resize($resizeWidth, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                } catch (\Exception $e) {
                    Log::error('Failed to compress | ' . $e->getMessage());
                    
                    Log::info('use original request image');
                    $path = $this->storeAsRequest($basePath, $filename, $file);
                }

                if (isset($img)) {
                    $path = $this->storeAsStream($basePath, $filename, $img);
                } else {
                    $path = $this->storeAsRequest($basePath, $filename, $file);
                }
            } else {
                $path = $this->storeAsRequest($basePath, $filename, $file);
            }

            Log::info('uploaded Path | ' . $path);

            return $path;

        }
    }

    private function storeAsRequest($basePath, $filename, $file){

        Log::info('storeAsRequest active_disk_for_images '.config('app.active_disk_for_images'));
        Log::info('storeAsRequest upload_to_public_storage '.config('app.upload_to_public_storage'));

        $path = Storage::disk(config('app.active_disk_for_images'))->putFileAs(
            $basePath,
            $file,
            $filename
        );

        if(config('app.active_disk_for_images') != "public" && config('app.upload_to_public_storage')){
            Storage::disk("public")->putFileAs(
                $basePath,
                $file,
                $filename
            );
        }

        return $path;
    }

    private function storeAsStream($basePath, $filename, $img){
        $path = $basePath . '/' . $filename;
        Storage::disk(config('app.active_disk_for_images'))->put($path, $img->stream()->__toString());

        if(config('app.active_disk_for_images') != "public" && config('app.upload_to_public_storage')){
            Storage::disk("public")->put($path, $img->stream()->__toString());
        }        

        return $path;
    }

    private function storeDirect($path, $contents, $resizeNeeded, $resizeWidth){
       
        if($resizeNeeded){
                try {
                    $img = Image::make($contents)
                        ->orientate()
                        ->resize($resizeWidth, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                } catch (\Exception $e) {
                    Log::error('Failed to compress | ' . $e->getMessage());
                }

                if($img){
                    Storage::disk(config('app.active_disk_for_images'))->put($path, $contents);
                } else {
        
                    Storage::disk(config('app.active_disk_for_images'))->put(
                        $path,
                        $contents            
                    );              
                }    
        } else {
            Storage::disk(config('app.active_disk_for_images'))->put($path, $contents);
        }

        if(config('storeDirect app.active_disk_for_images') != "public" && config('app.upload_to_public_storage')){
            Storage::disk("public")->put($path, $contents);
        }        

    }    

    /**
     * @param $path - The resource to deleted
     * @param $isDirectory - Boolean - true / false - If true all files under it will be deleted
     */
    public function removeMedia($path, $isDirectory = false, $isList = false, $seperator = "")
    {
        Log::info('removeMedia isDirectory | ' . $isDirectory);
        Log::info('removeMedia Path | ' . $path);

        if (isset($path)) {
            if ($isDirectory) {
                Storage::disk(config('app.active_disk_for_images'))->deleteDirectory($path);
            } else {
                if(!$isList){
                    Storage::disk(config('app.active_disk_for_images'))->delete($path);
                } else {
                    $files = explode($seperator, $path);
                    foreach ($files as $key => $file) {
                        Storage::disk(config('app.active_disk_for_images'))->delete($file);
                    }                    
                }
            }
        }
    }

    /**
     * @param $path - The resource for which the path is to be returned
     * @return $path - The path
     */
    public function getUrlMedia($path)
    {
        if (config('app.active_disk_for_images') == "public") {
            return $path ? Storage::disk(config('app.active_disk_for_images'))->url($path) : null;
        } else {
            return $path ? config('app.aws_bucket_images_url') . $path : null;
        }
    }

    public function generateCleanFileName($sourceFileName){

        $cleanName = strtolower(str_replace(str_split('&$@=;:+?\{^}%`]>[~<#()| '), '_', $sourceFileName));
        $cleanName = preg_replace('![' . preg_quote("_") . '\s]+!u', "_", $cleanName);

        return rand(1000, 9999) . '_' . $cleanName;
    }
}
