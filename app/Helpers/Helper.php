<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function getImageUrl($path)
    {
        if (config('app.active_disk_for_images') == "public") {
            return $path ? Storage::disk(config('app.active_disk_for_images'))->url($path) : null;
        } else {
            return $path ? config('app.aws_bucket_images_url') . $path : null;
        }
    }

    public static function interpolateQuery($sql, $bindings) {
    foreach ($bindings as $binding) {
        // Escape the binding depending on its type
        if (is_numeric($binding)) {
            $binding = $binding;
        } else {
            $binding = "'".addslashes($binding)."'";
        }
        $sql = preg_replace('/\?/', $binding, $sql, 1);
    }
    return $sql;
}

}

?>