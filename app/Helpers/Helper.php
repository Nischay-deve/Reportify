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

    public static function similarIncidentCount($chapterReportData, $teamId, $moduleId, $chapterId, $scopedCounts = [])
    {
        if (empty($chapterId)) {
            return '';
        }

        // "Find similar" opens all matching historical news (no date filter),
        // so prefer the all-time team + module + chapter count.
        if (!empty($teamId) && !empty($moduleId)) {
            $compositeKey = $teamId . '_' . $moduleId . '_' . $chapterId;
            if (isset($chapterReportData[$compositeKey])) {
                return $chapterReportData[$compositeKey];
            }
        }

        if (isset($chapterReportData[$chapterId])) {
            return $chapterReportData[$chapterId];
        }

        if (!empty($scopedCounts) && isset($scopedCounts[$chapterId])) {
            return $scopedCounts[$chapterId];
        }

        return '';
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