<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Requests\MassDestroyReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

use PDF;
use DB;
use Exception;
use View;
use PhpOfficePhpWordPhpWord;
use Carbon;
use Image;
use Dompdf\Dompdf;
use Mpdf\Mpdf;
use ZipArchive;
use File;
use DateTime;
use App\Helpers\Helper;

use App\Models\Report;
use App\Models\ReportTag;
use App\Models\ReportKeypoint;
use App\Models\ReportVideolink;
use App\Models\ReportImagelink;
use App\Models\Chapter;
use App\Models\Module;
use App\Models\Team;
use App\Models\RoleUser;
use App\Models\ReportScreenshot;
use App\Models\ReportFeaturedimage;
use App\Models\Location;
use App\Models\Language;
use App\Models\ReportTeamTag;
use App\Models\LocationState;
use App\Models\TeamTag;
use App\Models\ReportDocument;
use App\Models\SavedReport;
use App\Models\ReportSource;
use App\Models\Faq;
use App\Models\Document;

use Illuminate\Support\Facades\Log;
use App\Models\ReportFollowup;

use App\Traits\CommonMethodsTraits;
use Illuminate\Contracts\View\View as ViewView;

class DownloadController extends Controller
{
    use CommonMethodsTraits;

    public function loadCenter($slug)
    {
        // dd("in");
        if (empty($slug)) {
            $slug = "team3";
        }

        // dump($slug);
        $centerData = DB::connection('setfacts')->select("select * from websites where domain = '" . $slug . "'");
        // dd($centerData[0]);
        if ($centerData) {
            // echo "Website Found";

            $arr = array($centerData[0]->id);
            $this->loadCenterWebsiteFromRequest($arr);

            // Session::put('website_id', $centerData[0]->id);
        } else {
            // echo "Invalid Website";
            Session::put('website_id', 1);
            Session::put('website_slug', 'team3');
        }
        // dd($centerData);
    }


    public function viewSavedReport(Request $request, $name)
    {
        $savedReport = SavedReport::where('name', $name)->get();
        if ($savedReport) {
            foreach ($savedReport as $key => $data) {
                $params = $data->params;
                $website_id = $data->website_id;
                if (empty($website_id)) {
                    $website_id = 1;
                }
                $website = DB::connection('setfacts')->select("select * from websites where active=1 and id = " . $website_id . ";");
                $slug = $website[0]->domain;

                $http_pos = strpos($params, "://");
                if ($http_pos === false) {
                    $pos = strpos($params, "sm-daily-monitoring-report");
                    if ($pos === false) {
                        // echo route('report.pdfview',$slug)."/?".$params;
                        $url = route('report.pdfview', $slug) . "/?" . $params;
                        return redirect($url);
                    } else {
                        $app_domain = config('app.app_domain') . $slug . "/";
                        $viewDailyPopupUrl = $app_domain . $params;
                        // return redirect()->route($params);
                        return redirect($viewDailyPopupUrl);
                    }
                } else {
                    return redirect($params);
                }
            }
        }
    }




    public function pdfview(Request $request, $slug)
    {
        if (empty(session()->get('website_id'))) {
            $this->loadCenter($slug);
        }

        // dd($request->all());
        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000"); 





        $roleId = 0;

        $mode = "download";
        if ($request->mode) {
            $mode = $request->mode;
        } else if ($request->view_reports) {
            $mode = "view";
        }

        $report_data_type = $request->report_data_type;
        // dd($report_data_type);
        // dd($request->from_year,$request->to_year);

        if (
            $report_data_type == "daily_report" &&
            $request->team_name != '' &&
            empty($request->from_date) &&
            empty($request->to_date) &&
            empty($request->from_year) &&
            empty($request->to_year)
        ) {
            // dd("Condition matched");
            $report_data_type = "base_report";
            $request->from_date = "";
            $request->to_date = "";
        }








        if ($report_data_type == "daily_report" && !empty($request->tags)) {
            $report_data_type = "tag_report";
        }

        // dd($report_data_type);

        $from = "";
        if ($request->from_date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $request->from_date);
            $from = $fromdate->format('Y-m-d');
        }

        $to = "";
        if ($request->to_date) {
            $todate = DateTime::createFromFormat('d-m-Y', $request->to_date);
            $to = $todate->format('Y-m-d');
        }


        if (empty($from) && !empty($to)) {
            $from = $to;
        }

        if (empty($to) && !empty($from)) {
            $to = $from;
        }

        $To_Year = $request->to_year;
        $From_Year = $request->from_year;




        // Code Added By Nischay 




        // dump($report_data_type);
        // echo "<br>From ".$from." TO ".$to;
        // exit();

        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }

        $report_from = $request->report_from;
        $allLocationIds = "";
        $locationData = "";
        if (count($location_id) == 0 && !empty($report_from)) {
            if ($report_from == "within_bharat") {
                $locationData = DB::connection('setfacts')->select(DB::connection('setfacts')->Raw("SELECT group_concat(locations.id) AS all_ids FROM `locations` where parent='India' and locations.deleted_at IS NULL"));
                $allLocationIds = $locationData[0]->all_ids;
            } else if ($report_from == "outside_bharat") {
                $locationData = DB::connection('setfacts')->select(DB::connection('setfacts')->Raw("SELECT group_concat(locations.id) AS all_ids FROM `locations` where parent='World' and locations.deleted_at IS NULL"));
                $allLocationIds = $locationData[0]->all_ids;
            }

            // dd($locationData);

            // echo $locationIds;
            // exit();
        }


        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        // dd($moduleListByChapter);
        // dd($report_data_type);
        // dd($moduleNameArr);

        // daily_report: all news of selected date publish_at
        // custom_report: all news of selected date created_at with only whats included

        // dd($mode);

        if ($report_data_type == "daily_report" || $report_data_type == "custom_report") {
            // dd("Under Working");

            // dd("Condition matched");
            $fileName = $this->downloadReportAllTeam($request, $from, $to, $To_Year, $From_Year, $allLocationIds, $roleId, $moduleNameArr, $mode);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        } else if ($report_data_type == "hashtag_report") {
            // hashtag_report: all news of selected date publish_at
            // postfix with hashtag
            // different folders all combined in 1 single zip file
            $fileName = $this->downloadHashtagReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        } else if ($report_data_type == "work_report") {
            // work_report: format Team name | User name | No. NEWS 
            $fileName = $this->downloadWorkReport($request, $from, $to, $allLocationIds, $mode, $roleId);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        } else if ($report_data_type == "social_media") {
            // social_media_report: Team | User | Posts Count
            $fileName = $this->downloadSocialMediaReport($request, $from, $to, $allLocationIds, $mode, $roleId);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        } else if ($report_data_type == "table_report") {
            // table_report: format State | Issue | Category
            $fileName = $this->downloadTableReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        } else {


            // base_report: it should have no date option, once it is selected , date filter gets disabled, 
            // it download all the data since starting as per selected filters.

            // upload_date_report: all news uploaded on selected dates created_at         

            // tag_report: the report should show selected news of tags inputed

            // document_report


            $fileName = $this->downloadReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            }
        }
    }

    // base_report & upload_date_report , Tag_report, document_report
    // wordview, googledoc, inline pdf
    public function downloadReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode)
    {

        // dd("Mischay");
        // webview.blade
        // dd(request()->fullUrl());
        // echo "<br>Inside downloadReport() From ".$from." TO ".$to;
        // dd($request->all());
        // exit();
        // $loveZihad = null;
        // if ($request['team_name'][0] == '11' && $request['module'][0] == '104') {
        //     $loveZihad = 1;
        // }

        $loveZihad = 0;
        if (!empty($request['team_name'][0]) && !empty($request['module'][0])) {
            if ($request['team_name'][0] == '11' && $request['module'][0] == '104') {
                $loveZihad = 1;
            }
        }

        // dd($loveZihad);





        $from_year = $request->from_year;
        $to_year = $request->to_year;



        $page = 1;

        $showReportForUser = "";

        $team_ids_arr = array();
        if ($request->team_name) {
            $team_ids_arr = $request->team_name;
        }

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }

        // dd($request->team_name);

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }
        // dd($chapter_ids_arr);

        // --------------------------------------------------------------
        // TEAM → MODULE → CHAPTER DEPENDENCY LOGIC (ADD THIS BLOCK HERE)
        // --------------------------------------------------------------

        // CASE A: If ONLY Teams selected → auto-load Modules under teams
        if (!empty($team_ids_arr) && empty($module_ids_arr)) {
            $module_ids_arr = Module::whereIn('team_id', $team_ids_arr)
                ->pluck('id')
                ->toArray();
        }

        // CASE B: If Teams + Modules selected but NO Chapters → load Chapters under modules
        if (!empty($module_ids_arr) && empty($chapter_ids_arr)) {
            $chapter_ids_arr = Chapter::whereIn('module_id', $module_ids_arr)
                ->pluck('id')
                ->toArray();
        }

        // CASE C: Validate modules belong to selected teams
        if (!empty($module_ids_arr) && !empty($team_ids_arr)) {
            $validModules = Module::whereIn('team_id', $team_ids_arr)
                ->pluck('id')
                ->toArray();

            $module_ids_arr = array_values(array_intersect($module_ids_arr, $validModules));
        }

        // CASE D: Validate chapters belong to selected modules
        if (!empty($chapter_ids_arr) && !empty($module_ids_arr)) {
            $validChapters = Chapter::whereIn('module_id', $module_ids_arr)
                ->pluck('id')
                ->toArray();

            $chapter_ids_arr = array_values(array_intersect($chapter_ids_arr, $validChapters));
        }

        $find_more = '';
        if (!empty($request->find_more)) {
            $find_more = $request->find_more;
        }

        // $tName = $request->team_name;
        $tName = implode(",", $team_ids_arr);
        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;
        $document_report_tags = $request->document_report_tags;
        $show_report_type = $request->show_report_type;

        // dd($show_report_type);

        $report_data_type = $request->report_data_type;

        if ($report_data_type == "daily_report" && !empty($request->tags)) {
            $report_data_type = "tag_report";
        }

        $group_by_language = '';
        if ($request->group_by_language) {
            $group_by_language = $request->group_by_language;
        }
        $group_by_location = '';
        if ($request->group_by_location) {
            $group_by_location = $request->group_by_location;
        }

        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }
        // dd($location_id);

        $location_state_id = array();
        if ($request->location_state_id) {
            $location_state_id = $request->location_state_id;
        }
        // dd($location_state_id);        

        $language_id = array();
        if ($request->language_id) {
            $language_id = $request->language_id;
        }
        // dd($language_id);

        $fir_documents = 0;
        if ($request->fir_documents) {
            $fir_documents = $request->fir_documents;
        }

        $tagArr = array();
        if ($report_data_type == "tag_report") {
            $tags = $request->tags;
            if (!empty($tags)) {
                $tagArr = explode("\r\n", $tags);
            }
        }
        // dd($tagArr);

        // combined team name
        // $teamName = $teamNameData->name;
        $teamNameData = Team::select('name')->wherein('id', $team_ids_arr)->where('active', 1)->get();
        $teamName = new \stdClass();
        $teamName->name = "";
        foreach ($teamNameData as $index => $teamData) {
            if ($index == count($teamNameData) - 1) {
                $teamName->name .= $teamData->name;
            } else {
                $teamName->name .= $teamData->name . " / ";
            }
        }

        $moduleName = "";
        $chapterName = "";

        $reportTitle = ucwords(strtolower($teamName->name)) . " | Base Report";

        // $from = "";
        // $to = "";        
        if ($report_data_type == "upload_date_report") {
            $reportTitle = ucwords(strtolower($teamName->name)) . " | Upload Date Report";
            if (!empty($from && $to)) {
                $from = $from . " 00:00:00";
                $to = $to . " 23:59:59";
            }
            $dateRangeAttribute = "reports.created_at";
        } else if ($report_data_type == "tag_report") {
            $reportTitle = ucwords(strtolower($teamName->name)) . " | Tag Report";
            if (!empty($from && $to)) {
                $from = $from . " 00:00:00";
                $to = $to . " 23:59:59";
            }
            $dateRangeAttribute = "reports.publish_at";
        } else if ($report_data_type == "document_report") {
            $reportTitle = ucwords(strtolower($teamName->name)) . " | Document Report";
            if (!empty($from && $to)) {
                $from = $from . " 00:00:00";
                $to = $to . " 23:59:59";
            }
            $dateRangeAttribute = "reports.created_at";
        }

        // echo "<br>From ".$from." TO ".$to;
        // echo "<BR>report_data_type ". $report_data_type;   
        // echo "<BR>reportTitle ". $reportTitle;   
        // echo "<BR>dateRangeAttribute ". $dateRangeAttribute;             
        // exit();
        $newsOrderByOrder = "desc";
        if (!empty($request->news_order_by_order)) {
            $newsOrderByOrder = $request->news_order_by_order;
        }
        // echo $newsOrderByOrder;

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $data = Report::with(['chapter'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select(
                'reports.chapter_id',
                DB::connection('setfacts')->Raw('COUNT(DISTINCT(`reports`.`id`)) AS chapter_count')
            );

        $data =  $data->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $data =  $data->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });


        $dataModule = Report::with(['module'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)

            ->select('reports.module_id', DB::connection('setfacts')->Raw('COUNT(modules.id) AS module_count'));

        $dataModule =  $dataModule->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });
        $dataModule =  $dataModule->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $firDocumentReportIdArr = array();
        if ($fir_documents == 1) {
            $firDocumentReportIdArr = $this->getFirDocumentReportIds();
            if (count($firDocumentReportIdArr) > 0) {
                $data = $data->wherein('reports.id', $firDocumentReportIdArr);
                $dataModule = $dataModule->wherein('reports.id', $firDocumentReportIdArr);
            }
        }

        if (!empty($from && $to)) {
            $data = $data->whereBetween($dateRangeAttribute, [$from, $to]);
            $dataModule = $dataModule->whereBetween($dateRangeAttribute, [$from, $to]);
        }


        // if (!empty($tName)) {
        //     $data = $data->where('reports.team_id', $tName);
        //     $dataModule = $dataModule->where('reports.team_id', $tName);
        // }

        if (count($team_ids_arr) > 0) {
            $data = $data->wherein('reports.team_id', $team_ids_arr);
            $dataModule = $dataModule->wherein('reports.team_id', $team_ids_arr);
        }

        if (count($module_ids_arr) > 0) {
            $data = $data->wherein('reports.module_id', $module_ids_arr);
            $dataModule = $dataModule->wherein('reports.module_id', $module_ids_arr);
        }
        if (count($chapter_ids_arr) > 0) {
            $data = $data->wherein('reports.chapter_id', $chapter_ids_arr);
            $dataModule = $dataModule->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        if (!empty($request->first_time)) {
            $first_time = $request->first_time;
            $data = $data->where('first_time', $first_time);
            $dataModule = $dataModule->where('first_time', $first_time);
        }

        if (!empty($request->followup)) {
            $followup = $request->followup;
            $data = $data->where('has_followup', $followup);
            $dataModule = $dataModule->where('has_followup', $followup);
        }

        if (!empty($request->calendar_date)) {
            $calendar_date = $request->calendar_date;
            $data = $data->where('calendar_date', $calendar_date);
            $dataModule = $dataModule->where('calendar_date', $calendar_date);
        }

        if (count($language_id) > 0) {
            $data = $data->wherein('reports.language_id', $language_id);
            $dataModule = $dataModule->wherein('reports.language_id', $language_id);
        }

        if (count($location_id) > 0) {
            $data = $data->wherein('location_id', $location_id);
            $dataModule = $dataModule->wherein('location_id', $location_id);
        }

        if (count($location_state_id) > 0) {
            $data = $data->wherein('location_state_id', $location_state_id);
            $dataModule = $dataModule->wherein('location_state_id', $location_state_id);
        }

        if (!empty($allLocationIds)) {
            $locationIdsArr = explode(',', $allLocationIds);

            $data = $data->whereIn('location_id', $locationIdsArr);
            $dataModule = $dataModule->whereIn('location_id', $locationIdsArr);
        }

        if (!empty($showReportForUser)) {
            $data = $data->where('user_id', $showReportForUser);
            $dataModule = $dataModule->where('user_id', $showReportForUser);
        }

        if (count($tagArr) > 0) {

            $data = $data->leftjoin('report_team_tags', function ($join) use ($team_ids_arr) {
                $join->on('report_team_tags.report_id', 'reports.id');
                $join->wherein('report_team_tags.team_id', $team_ids_arr);
            });
            $data = $data->leftjoin('team_tags', function ($join) use ($team_ids_arr) {
                $join->on('team_tags.id', 'report_team_tags.tag_id');
                $join->wherein('team_tags.team_id', $team_ids_arr);
            });
            $data = $data->leftjoin('report_keypoints', 'report_keypoints.report_id', 'reports.id');


            $dataModule = $dataModule->leftjoin('report_team_tags', function ($join) use ($team_ids_arr) {
                $join->on('report_team_tags.report_id', 'reports.id');
                $join->wherein('report_team_tags.team_id', $team_ids_arr);
            });
            $dataModule = $dataModule->leftjoin('team_tags', function ($join) use ($team_ids_arr) {
                $join->on('team_tags.id',  'report_team_tags.tag_id');
                $join->wherein('team_tags.team_id', $team_ids_arr);
            });
            $dataModule = $dataModule->leftjoin('report_keypoints', 'report_keypoints.report_id', 'reports.id');



            $data->where(function ($internalQuery) use ($tagArr) {
                foreach ($tagArr as $tag) {
                    $internalQuery->orWhere('reports.heading', 'like', '%' . $tag . '%')
                        ->orWhere('reports.link', 'like', '%' . $tag . '%')
                        ->orWhere('team_tags.tag', 'like', '%' . $tag . '%')
                        ->orWhere('report_keypoints.keypoint', 'like', '%' . $tag . '%');
                }
            });

            $dataModule->where(function ($internalQuery) use ($tagArr) {
                foreach ($tagArr as $tag) {
                    $internalQuery->orWhere('reports.heading', 'like', '%' . $tag . '%')
                        ->orWhere('reports.link', 'like', '%' . $tag . '%')
                        ->orWhere('team_tags.tag', 'like', '%' . $tag . '%')
                        ->orWhere('report_keypoints.keypoint', 'like', '%' . $tag . '%');
                }
            });
        }


        if ($report_data_type == "document_report" && !empty($document_report_tags)) {

            $data = $data->leftjoin('report_team_tags', 'report_team_tags.report_id', 'reports.id');
            $data = $data->leftjoin('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
            $data = $data->where('team_tags.id', $document_report_tags);

            $dataModule = $dataModule->leftjoin('report_team_tags', 'report_team_tags.report_id', 'reports.id');
            $dataModule = $dataModule->leftjoin('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
            $dataModule = $dataModule->where('team_tags.id', $document_report_tags);
        }

        if (!empty($show_report_type)) {
            $data = $data->where('reports.report_type', $show_report_type);
            $dataModule = $dataModule->where('reports.report_type', $show_report_type);
        }

        // echo $from."<BR>";
        // echo $to;


        $repcountData = clone $data;
        // $repcountData = $repcountData->groupBy('reports.chapter_id');

        $repcountData = $repcountData->get()->sum('chapter_count');
        // dd($repcountData);







        // Log::info(
        //     $repcountData->toSql(),
        //     $repcountData->getBindings()
        // );

        // $sql = $data->toSql();
        // $bindings = $data->getBindings();
        // dd(Helper::interpolateQuery($sql, $bindings));



        // $repcountData = $repcountData->get()->count();
        $repcount = $repcountData;
        // dump($repcountData);

        if ($mode == "getcount") {
            return $repcount;
        }

        $keyNews = [];
        $news = [];
        // if ($repcount > 0) {

        //     //////////////////////////////////
        //     // group by module_id
        //     //////////////////////////////////
        //     $dataModule = $dataModule->wherenull('modules.deleted_at');
        //     $dataModule = $dataModule->orderBy('reports.publish_at', $newsOrderByOrder);
        //     $dataModule = $dataModule->groupBy('reports.module_id');
        //     // $dataModule = $dataModule->toSql();
        //     // dd($dataModule);

        //     $dataModule = $dataModule->get()->toArray();
        //     // dd($dataModule);


        //     //////////////////////////////////
        //     // group by chapter_id
        //     //////////////////////////////////
        //     $data = $data->wherenull('chapters.deleted_at');

        //     if ($report_data_type == "document_report") {

        //         if ($group_by_location == 1) {
        //             $data = $data->orderBy('reports.location_id');
        //         }
        //         if ($group_by_language == 1) {
        //             $data = $data->orderBy('reports.language_id');
        //         }

        //         // $data = $data->orderBy('orderByData', 'DESC');
        //     } else {

        //         if ($group_by_location == 1) {
        //             $data = $data->orderBy('reports.location_id');
        //         }
        //         if ($group_by_language == 1) {
        //             $data = $data->orderBy('reports.language_id');
        //         }


        //         $data = $data->orderBy('reports.publish_at', $newsOrderByOrder);
        //     }

        //     $data = $data->groupBy('reports.chapter_id');
        //     // $data = $data->toSql();
        //     // dd($data);



        //     $data = $data->get()->toArray();
        //     // dd($data);

        //     // loop for chapters          
        //     $capterIds = array();
        //     foreach ($data as $datas) {
        //         $capterIds[] = $datas['chapter_id'];
        //     }
        //     // dd($capterIds);

        //     $per_page_reports = config('app.per_page_reports');
        //     $page = 1;
        //     $count = 0;
        //     if (!empty($request->page)) {
        //         $page = $request->page;
        //         $count = $per_page_reports * ($page - 1);
        //     }

        //     // dump($count);
        //     DB::connection('setfacts')->select(DB::raw('SET @count:=' . $count . ';'));
        //     $news = Report::with(
        //         ['keypoint', 'chapter', 'module', 'tag', 'videolink', 'imagelink', 'screenshot', 'feateredimage', 'document', 'followup', 'reportmedias']
        //     )->select(
        //         'reports.*',
        //         'report_sources.source',
        //         'languages.name as language_name',
        //         'locations.name as location_name',
        //         'location_states.name as location_state_name',
        //         'report_screenshots.screenshot as news_screenshot',
        //         DB::raw('(@count:=@count+1) AS serial_number'),
        //     )
        //         ->join('report_sources', 'report_sources.id', 'reports.report_source_id');

        //     $news = $news->join('languages', 'reports.language_id', 'languages.id');
        //     $news = $news->leftjoin('locations', 'reports.location_id', 'locations.id');
        //     $news = $news->leftjoin('location_states', 'reports.location_state_id', 'location_states.id');

        //     $news = $news->whereIn('chapter_id', $capterIds);
        //     $news = $news->where('is_deleted', 0)->where('reports.active', 1);

        //     if (!empty($show_report_type)) {
        //         $news = $news->where('reports.report_type', $show_report_type);
        //     }


        //     $firDocumentReportIdArr = array();
        //     if ($fir_documents == 1) {
        //         $firDocumentReportIdArr = $this->getFirDocumentReportIds();
        //         if (count($firDocumentReportIdArr) > 0) {
        //             $news = $news->wherein('reports.id', $firDocumentReportIdArr);
        //         }
        //     }

        //     if (!empty($from && $to)) {
        //         $news = $news->whereBetween($dateRangeAttribute, [$from, $to]);
        //     }

        //     if (count($language_id) > 0) {
        //         $news = $news->wherein('language_id', $language_id);
        //     }


        //     if (!empty($request->first_time)) {
        //         $first_time = $request->first_time;
        //         $news = $news->where('first_time', $first_time);
        //     }

        //     if (!empty($request->followup)) {
        //         $followup = $request->followup;
        //         $news = $news->where('has_followup', $followup);
        //     }

        //     if (!empty($request->calendar_date)) {
        //         $calendar_date = $request->calendar_date;
        //         $news = $news->where('calendar_date', $calendar_date);
        //     }

        //     if (count($location_id) > 0) {
        //         $news = $news->wherein('reports.location_id', $location_id);
        //     }

        //     if (count($location_state_id) > 0) {
        //         $news = $news->wherein('reports.location_state_id', $location_state_id);
        //     }

        //     if (!empty($allLocationIds)) {
        //         $locationIdsArr = explode(',', $allLocationIds);

        //         $news = $news->whereIn('reports.location_id', $locationIdsArr);
        //     }

        //     if (!empty($showReportForUser)) {
        //         $news = $news->where('user_id', $showReportForUser);
        //     }
        //     // code Written By Nischay 

        //     // dd($module_ids_arr);
        //     if (!empty($module_ids_arr)) {
        //         $news = $news->wherein('reports.module_id', $module_ids_arr);
        //     }
        //     // code ends here

        //     if (count($tagArr) > 0) {
        //         $news = $news->leftjoin('report_team_tags', 'report_team_tags.report_id', 'reports.id');
        //         $news = $news->leftjoin('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
        //         $news = $news->leftjoin('report_keypoints', 'report_keypoints.report_id', 'reports.id');

        //         $news->where(function ($internalQuery) use ($tagArr) {
        //             foreach ($tagArr as $tag) {
        //                 $internalQuery->orWhere('reports.heading', 'like', '%' . $tag . '%')
        //                     ->orWhere('reports.link', 'like', '%' . $tag . '%')
        //                     ->orWhere('team_tags.tag', 'like', '%' . $tag . '%')
        //                     ->orWhere('report_keypoints.keypoint', 'like', '%' . $tag . '%');

        //                 // $internalQuery->orWhere('reports.heading', 'like', '% ' . $tag . ' %')
        //                 // ->orWhere('reports.link', 'like', '% ' . $tag . ' %')
        //                 // ->orWhere('team_tags.tag', 'like', '% ' . $tag . ' %')
        //                 // ->orWhere('report_keypoints.keypoint', 'like', '% ' . $tag . '% ');


        //                 // RLIKE "[[:<:]]foo[[:>:]]"
        //                 // $internalQuery->orWhere('reports.heading', 'RLIKE', '[[:<:]]'.$tag.'[[:>:]]')
        //                 // ->orWhere('reports.link', 'RLIKE', '[[:<:]]'.$tag.'[[:>:]]')
        //                 // ->orWhere('team_tags.tag', 'RLIKE', '[[:<:]]'.$tag.'[[:>:]]')
        //                 // ->orWhere('report_keypoints.keypoint', 'RLIKE', '[[:<:]]'.$tag.'[[:>:]]');

        //             }
        //         });
        //         $news = $news->groupBy('reports.id');
        //     }

        //     if ($report_data_type == "document_report" && !empty($document_report_tags)) {
        //         $news = $news->join('report_team_tags', 'report_team_tags.report_id', 'reports.id');
        //         $news = $news->join('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
        //         $news = $news->where('team_tags.id', $document_report_tags);
        //         $news = $news->groupBy('reports.id');
        //     }

        //     if ($report_data_type == "document_report") {

        //         //Group by Location and Language
        //         if ($group_by_location == 1) {
        //             $news = $news->orderBy('reports.location_id');
        //         }
        //         if ($group_by_language == 1) {
        //             $news = $news->orderBy('reports.language_id');
        //         }

        //         // $news = $news->orderBy('orderByData', 'DESC');
        //     } else {
        //         //Group by Location and Language
        //         if ($group_by_location == 1) {
        //             $news = $news->orderBy('reports.location_id');
        //         }
        //         if ($group_by_language == 1) {
        //             $news = $news->orderBy('reports.language_id');
        //         }

        //         $news = $news->orderBy('reports.publish_at', $newsOrderByOrder);
        //     }

        //     if ($reportType = "pdf_desc") {
        //         $news = $news->leftjoin('report_screenshots', 'report_screenshots.report_id', 'reports.id');
        //     }


        //     // $news = $news->toSql();
        //     // dd($news);

        //     // $sql = $news->toSql();
        //     // $bindings = $news->getBindings();
        //     // $finalSql = Helper::interpolateQuery($sql, $bindings);

        //     // dd($finalSql);



        //     if ($mode == "view") {
        //         $news = $news->paginate(config('app.per_page_reports'))->withQueryString();
        //     } else {
        //         $news = $news->get()->toArray();
        //     }
        //     // dd($news);
        //     // dump($news[0]);

        // }

        // if ($repcount > 0) {

        //     // ----------------------------
        //     // Group by module_id
        //     // ----------------------------
        //     $dataModule = $dataModule->whereNull('modules.deleted_at')
        //         ->orderBy('reports.publish_at', $newsOrderByOrder)
        //         ->groupBy('reports.module_id')
        //         ->get()
        //         ->toArray();

        //     // ----------------------------
        //     // Group by chapter_id
        //     // ----------------------------
        //     $data = $data->whereNull('chapters.deleted_at');

        //     if ($group_by_location == 1) {
        //         $data = $data->orderBy('reports.location_id');
        //     }
        //     if ($group_by_language == 1) {
        //         $data = $data->orderBy('reports.language_id');
        //     }

        //     $data = $data->orderBy('reports.publish_at', $newsOrderByOrder)
        //         ->groupBy('reports.chapter_id')
        //         ->get()
        //         ->toArray();

        //     // Collect chapter IDs
        //     $chapterIds = array_column($data, 'chapter_id');

        //     // Pagination setup
        //     $per_page_reports = config('app.per_page_reports');
        //     $page = $request->page ?? 1;
        //     $count = $per_page_reports * ($page - 1);
        //     DB::connection('setfacts')->select(DB::raw('SET @count:=' . $count . ';'));

        //     // ----------------------------
        //     // Build the main query
        //     // ----------------------------
        //     $news = Report::with([
        //         'keypoint',
        //         'chapter',
        //         'module',
        //         'tag',
        //         'videolink',
        //         'imagelink',
        //         'screenshot',      // Load all screenshots via relationship
        //         'feateredimage',
        //         'document',
        //         'followup',
        //         'reportmedias'
        //     ])
        //         ->select(
        //             'reports.*',
        //             'report_sources.source',
        //             'languages.name as language_name',
        //             'locations.name as location_name',
        //             'location_states.name as location_state_name',
        //             DB::raw('(@count:=@count+1) AS serial_number')
        //         )
        //         ->join('report_sources', 'report_sources.id', '=', 'reports.report_source_id')
        //         ->join('languages', 'reports.language_id', '=', 'languages.id')
        //         ->leftJoin('locations', 'reports.location_id', '=', 'locations.id')
        //         ->leftJoin('location_states', 'reports.location_state_id', '=', 'location_states.id')
        //         ->whereIn('chapter_id', $chapterIds)
        //         ->where('reports.is_deleted', 0)
        //         ->where('reports.active', 1);

        //     // ----------------------------
        //     // Filters
        //     // ----------------------------
        //     if (!empty($show_report_type)) {
        //         $news->where('reports.report_type', $show_report_type);
        //     }

        //     if ($fir_documents == 1) {
        //         $firDocumentReportIdArr = $this->getFirDocumentReportIds();
        //         if (!empty($firDocumentReportIdArr)) {
        //             $news->whereIn('reports.id', $firDocumentReportIdArr);
        //         }
        //     }

        //     if (!empty($from) && !empty($to)) {
        //         $news->whereBetween($dateRangeAttribute, [$from, $to]);
        //     }

        //     if (!empty($language_id)) {
        //         $news->whereIn('reports.language_id', $language_id);
        //     }

        //     if (!empty($request->first_time)) {
        //         $news->where('reports.first_time', $request->first_time);
        //     }

        //     if (!empty($request->followup)) {
        //         $news->where('reports.has_followup', $request->followup);
        //     }

        //     if (!empty($request->calendar_date)) {
        //         $news->where('reports.calendar_date', $request->calendar_date);
        //     }

        //     if (!empty($location_id)) {
        //         $news->whereIn('reports.location_id', $location_id);
        //     }

        //     if (!empty($location_state_id)) {
        //         $news->whereIn('reports.location_state_id', $location_state_id);
        //     }

        //     if (!empty($allLocationIds)) {
        //         $news->whereIn('reports.location_id', explode(',', $allLocationIds));
        //     }

        //     if (!empty($showReportForUser)) {
        //         $news->where('reports.user_id', $showReportForUser);
        //     }

        //     if (!empty($module_ids_arr)) {
        //         $news->whereIn('reports.module_id', $module_ids_arr);
        //     }

        //     // ----------------------------
        //     // Tags filter (optional)
        //     // ----------------------------
        //     if (!empty($tagArr)) {
        //         $news->where(function ($q) use ($tagArr) {
        //             foreach ($tagArr as $tag) {
        //                 $q->orWhere('reports.heading', 'like', '%' . $tag . '%')
        //                     ->orWhere('reports.link', 'like', '%' . $tag . '%')
        //                     ->orWhereHas('tag', function ($t) use ($tag) {
        //                         $t->where('team_tags.tag', 'like', '%' . $tag . '%');
        //                     })
        //                     ->orWhereHas('keypoint', function ($k) use ($tag) {
        //                         $k->where('report_keypoints.keypoint', 'like', '%' . $tag . '%');
        //                     });
        //             }
        //         });
        //     }

        //     // ----------------------------
        //     // Ordering
        //     // ----------------------------
        //     if ($group_by_location == 1) {
        //         $news->orderBy('reports.location_id');
        //     }
        //     if ($group_by_language == 1) {
        //         $news->orderBy('reports.language_id');
        //     }

        //     $news->orderBy('reports.publish_at', $newsOrderByOrder);

        //     // ----------------------------
        //     // Get results or paginate
        //     // ----------------------------
        //     if ($mode == "view") {
        //         $news = $news->paginate($per_page_reports)->withQueryString();
        //     } else {
        //         $news = $news->get()->toArray();
        //     }
        // }

        if ($repcount > 0) {

            // ----------------------------
            // Group by module_id
            // ----------------------------
            $dataModule = $dataModule->whereNull('modules.deleted_at')
                ->orderBy('reports.publish_at', $newsOrderByOrder)
                ->groupBy('reports.module_id')
                ->get()
                ->toArray();

            // ----------------------------
            // Group by chapter_id
            // ----------------------------
            $data = $data->whereNull('chapters.deleted_at');

            if ($report_data_type == "document_report") {
                if ($group_by_location == 1) $data = $data->orderBy('reports.location_id');
                if ($group_by_language == 1) $data = $data->orderBy('reports.language_id');
            } else {
                if ($group_by_location == 1) $data = $data->orderBy('reports.location_id');
                if ($group_by_language == 1) $data = $data->orderBy('reports.language_id');
                $data = $data->orderBy('reports.publish_at', $newsOrderByOrder);
            }

            $data = $data->groupBy('reports.chapter_id')
                ->get()
                ->toArray();

            // Collect chapter IDs
            $chapterIds = array_column($data, 'chapter_id');

            // ----------------------------
            // Pagination setup
            // ----------------------------
            $per_page_reports = config('app.per_page_reports');
            $page = !empty($request->page) ? $request->page : 1;
            $count = $per_page_reports * ($page - 1);

            DB::connection('setfacts')->select(DB::raw('SET @count:=' . $count . ';'));

            // ----------------------------
            // Build the main query
            // ----------------------------
            $news = Report::with([
                'keypoint',
                'chapter',
                'module',
                'tag',
                'videolink',
                'imagelink',
                'screenshot',
                'feateredimage',
                'document',
                'followup',
                'reportmedias'
            ])
                ->select(
                    'reports.*',
                    'report_sources.source',
                    'languages.name as language_name',
                    'locations.name as location_name',
                    'location_states.name as location_state_name',
                    'first_screenshot.screenshot as news_screenshot',
                    'chapters.name as chapter_names',
                    'modules.name as modules_name',
                    DB::raw('(@count:=@count+1) AS serial_number')
                )
                ->join('report_sources', 'report_sources.id', '=', 'reports.report_source_id')
                ->join('languages', 'reports.language_id', '=', 'languages.id')
                ->leftJoin('locations', 'reports.location_id', '=', 'locations.id')
                ->leftJoin('location_states', 'reports.location_state_id', '=', 'location_states.id')
                ->leftJoin('chapters', 'reports.chapter_id', '=', 'chapters.id')
                ->leftJoin('modules', 'reports.module_id', '=', 'modules.id')
                ->whereIn('chapter_id', $chapterIds)
                ->where('reports.is_deleted', 0)
                ->where('reports.active', 1);

            // ----------------------------
            // Filters
            // ----------------------------
            if (!empty($show_report_type)) {
                $news->where('reports.report_type', $show_report_type);
            }

            if ($fir_documents == 1) {
                $firDocumentReportIdArr = $this->getFirDocumentReportIds();
                if (!empty($firDocumentReportIdArr)) {
                    $news->whereIn('reports.id', $firDocumentReportIdArr);
                }
            }

            if (!empty($from) && !empty($to)) {
                $news->whereBetween($dateRangeAttribute, [$from, $to]);
            }

            if (!empty($language_id) && is_array($language_id)) {
                $news->whereIn('reports.language_id', $language_id);
            }

            if (!empty($request->first_time)) {
                $news->where('reports.first_time', $request->first_time);
            }

            if (!empty($request->followup)) {
                $news->where('reports.has_followup', $request->followup);
            }

            if (!empty($request->calendar_date)) {
                $news->where('reports.calendar_date', $request->calendar_date);
            }

            if (!empty($location_id) && is_array($location_id)) {
                $news->whereIn('reports.location_id', $location_id);
            }

            if (!empty($location_state_id) && is_array($location_state_id)) {
                $news->whereIn('reports.location_state_id', $location_state_id);
            }

            if (!empty($allLocationIds)) {
                $news->whereIn('reports.location_id', explode(',', $allLocationIds));
            }

            if (!empty($showReportForUser)) {
                $news->where('reports.user_id', $showReportForUser);
            }

            if (!empty($module_ids_arr) && is_array($module_ids_arr)) {
                $news->whereIn('reports.module_id', $module_ids_arr);
            }


            // $news->leftJoin('report_screenshots', 'report_screenshots.report_id', '=', 'reports.id');
            $news->leftJoin(DB::raw('(
    SELECT rs1.report_id, rs1.screenshot
    FROM report_screenshots rs1
    INNER JOIN (
        SELECT report_id, MIN(id) AS min_id
        FROM report_screenshots
        GROUP BY report_id
    ) rs2 ON rs1.id = rs2.min_id
) AS first_screenshot'), 'first_screenshot.report_id', '=', 'reports.id');



            // ----------------------------
            // Tags filter (optional)
            // ----------------------------
            if (!empty($tagArr)) {
                $news->leftJoin('report_team_tags', 'report_team_tags.report_id', '=', 'reports.id')
                    ->leftJoin('team_tags', 'team_tags.id', '=', 'report_team_tags.tag_id')
                    ->leftJoin('report_keypoints', 'report_keypoints.report_id', '=', 'reports.id')
                    ->where(function ($q) use ($tagArr) {
                        foreach ($tagArr as $tag) {
                            $q->orWhere('reports.heading', 'like', '%' . $tag . '%')
                                ->orWhere('reports.link', 'like', '%' . $tag . '%')
                                ->orWhere('team_tags.tag', 'like', '%' . $tag . '%')
                                ->orWhere('report_keypoints.keypoint', 'like', '%' . $tag . '%');
                        }
                    })
                    ->groupBy('reports.id');
            }

            if ($report_data_type == "document_report" && !empty($document_report_tags)) {
                $news->join('report_team_tags', 'report_team_tags.report_id', '=', 'reports.id')
                    ->join('team_tags', 'team_tags.id', '=', 'report_team_tags.tag_id')
                    ->where('team_tags.id', $document_report_tags)
                    ->groupBy('reports.id');
            }

            // ----------------------------
            // Ordering
            // ----------------------------
            if ($group_by_location == 1) $news->orderBy('reports.location_id');
            if ($group_by_language == 1) $news->orderBy('reports.language_id');

            $news->orderBy('reports.publish_at', $newsOrderByOrder);

            if (!empty($reportType) && $reportType == "pdf_desc") {
                $news->leftJoin('report_screenshots', 'report_screenshots.report_id', '=', 'reports.id');
            }

            // ----------------------------
            // Get results or paginate
            // ----------------------------

            // $sql = $news->toSql();
            // $bindings = $news->getBindings();
            // Log::info(Helper::interpolateQuery($sql, $bindings));

            // $sql = $news->toSql();
            // $bindings = $news->getBindings();
            // dd(Helper::interpolateQuery($sql, $bindings));

            // dd($finalSql);
            if ($mode == "view") {
                $news = $news->paginate($per_page_reports)->withQueryString();
            } else {
                $news = $news->get()->toArray();
            }
        }



        // $keyNews = Paginate::paginate($keyNews,1);
        // dd($keyNews);
        // echo "in";
        // exit();



        $mytime = Carbon\Carbon::now();

        $team_name_converted = "_all_teams";
        // if (!empty($team_ids_arr)) {
        //     $team_name_converted = str_replace(" ", "_", implode(",", $team_ids_arr));
        //     $team_name_converted = "_" . strtolower($team_name_converted);
        // }else{
        //     $team_name_converted = "_all_teams";
        // }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {

            $downloadFileName = date('jSF') . $team_name_converted;
        }

        $viewLayout = "layouts.admin";
        // if ($roleId == 0) {
        //     // $viewLayout = "layouts.app";
        //     $viewLayout = "layouts.webview";
        // }

        $publicReportColor = "blue";
        if (config('app.color_scheme') == 'pink') {
            $publicReportColor = "red";
        }

        $chapterReportData = $this->fetchChapterReportCount();

        $fromPage = "";
        if ($request->fromPage) {
            $fromPage = $request->fromPage;
        }


        $reportParams = [
            'to' => $to,
            'from' => $from,
            'report_data_type' => $report_data_type,
            'dataModule' => $dataModule,
            'data' => $data,
            'repcount' => $repcount,
            'keyNews' => $news,
            'mytime' => $mytime,
            'reportType' => $reportType,
            'moduleName' => $moduleName,
            'teamName' => $teamName,
            'chapterName' => $chapterName,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading,
            'roleId' => $roleId,
            'moduleNameArr' => $moduleNameArr,
            'viewLayout' => $viewLayout,
            'publicReportColor' => $publicReportColor,
            'chapterReportData' => $chapterReportData,
            'find_more' => $find_more,
            'module_ids_arr' => $module_ids_arr,
            'team_name' => $request->team_name,
            'page' => $page,
            'tagArr' => $tagArr,
            'zihad' => $loveZihad,
        ];

        // dd($reportParams);

        if ($mode == "view") {

            $view_content_web =  view('report.webview',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else {

            if ($reportType == "word") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_content_word = View::make('report.wordview', $reportParams)->render();

                $view_content_word = str_replace("&amp;", "and", $view_content_word);

                //added on Dec 13 for issue of #039 error '
                $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

                // echo "<pre>";print_r($view_content_word);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "googledoc") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_contents_googledoc = View::make('report.googledoc', $reportParams)->render();

                $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);

                //added on Dec 13 for issue of #039 error '
                $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

                // echo "<pre>";print_r($view_contents_googledoc);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "excel") {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

                // Set headers
                $headers = ['S.No', 'Publish Date', 'State', 'District', 'Categories', 'Sub Categories', 'Heading', 'Keypoints', 'Link'];
                $columnIndex = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($columnIndex . '1', $header);
                    $columnIndex++;
                }

                // Write data
                $rowIndex = 2;
                foreach ($news as $report) {
                    $keypoints = '';
                    foreach ($report['keypoint'] as $key => $keypoint) {
                        $keypoints .= ($key + 1) . '. ' . $keypoint['keypoint'] . "\n";
                    }

                    $location = $report['location_name'] ?? '';
                    if (!empty($report['location_state_name']) && $report['location_state_name'] !== 'NA') {
                        $location .= ' (' . $report['location_state_name'] . ')';
                    }

                    $sheet->setCellValue("A$rowIndex", $report['serial_number'] ?? $rowIndex - 1);
                    $sheet->setCellValue("B$rowIndex", \Carbon\Carbon::parse($report['publish_at'])->format('d/m/Y'));
                    $sheet->setCellValue("C$rowIndex", $report['location_name'] ?? '');
                    $sheet->setCellValue("D$rowIndex", $report['location_state_name'] ?? '');
                    $sheet->setCellValue("E$rowIndex", $report['modules_name'] ?? ''); // Replace with actual categories if available
                    $sheet->setCellValue("F$rowIndex", $report['chapter_name'] ?? '');
                    $sheet->setCellValue("G$rowIndex", $report['heading'] ?? '');
                    // Replace with actual sub-categories if available
                    $sheet->setCellValue("H$rowIndex", $keypoints);
                    // $sheet->setCellValue("I$rowIndex", $location);


                    // $sheet->setCellValue("I$rowIndex", $report['link'] ?? '');
                    $link = $report['link'] ?? '';

                    $sheet->setCellValueExplicit("I$rowIndex", $link, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    if (!empty($link)) {
                        $sheet->getCell("I$rowIndex")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("I$rowIndex")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                        $sheet->getStyle("I$rowIndex")->getFont()->setUnderline(true);
                    }



                    $rowIndex++;
                }

                // Dynamic styling
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); // e.g., 'I'
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Auto-size columns
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                }

                // Bold headers
                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);

                // Wrap text for all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()->setWrapText(true)->setVertical('top');


                // Apply thin border to all cells
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray($borderStyle);

                // Optional: Center align serial number
                $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal('center');

                // Save Excel
                $fileName = $downloadFileName . '.xlsx';
                $filePath = storage_path(config('app.download_report_base_folder') . "/" . $fileName);

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                try {
                    $writer->save($filePath);
                } catch (\Exception $e) {
                    // Handle error
                }

                return $fileName;
            } else if ($reportType == 'pdf_desc') {
                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'default_font' => 'notosansdevanagari',
                    'fontDir' => array_merge($fontDirs, [
                        resource_path('fonts'), // 👈 now pointing to your new folder
                    ]),
                    'fontdata' => $fontData + [
                        'notosansdevanagari' => [
                            'R' => 'NotoSansDevanagari-Regular.ttf',
                            'B' => 'NotoSansDevanagari-Bold.ttf',
                        ],
                    ],
                ]);

                $html = '
<style>
table { border-collapse: collapse; font-size: 12px; width: 100%; }
th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
td img { border: 1px solid #ccc; border-radius: 4px; margin: 2px; max-width: 120px; max-height: 120px; }
ul { margin: 0; padding-left: 18px; }
tr { page-break-inside: avoid; }
</style>

<table>
    <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="25%">Headline</th>
            <th width="15%">Location</th>
            <th width="25%">Screenshots</th>
            <th width="30%">Descriptions</th>
        </tr>
    </thead>
    <tbody>
';

                foreach ($news as $reports) {
                    $serial = $reports['serial_number'];
                    $heading = $reports['heading'];
                    $source  = $reports['source'];
                    $date    = Carbon\Carbon::parse($reports['publish_at'])->format('d/m/Y');
                    $location = $reports['location_name'] ?? 'N/A';
                    $keypoints = $reports['calendar_date_description'] ?? [];

                    // Screenshots
                    $frontScreenshot = $reports['news_screenshot'] ?? null;

                    $screenshotHtml = '';

                    if ($frontScreenshot) {
                        $src = Helper::getImageUrl($frontScreenshot); // Use your helper to get full URL
                        // dd($src);
                        if ($src) {
                            $screenshotHtml .= '<img src="' . $src . '" style="max-width:48%; max-height:180px; margin-right:2%;" />';
                        }
                    }

                    if (!$screenshotHtml) $screenshotHtml = 'N/A';


                    if (is_string($keypoints)) {
                        // If it's a string, split it into lines or by | if that's your separator
                        $keypoints = preg_split('/\r\n|\r|\n|\|/', $keypoints);
                    }

                    // If it’s still not an array, make it an empty array
                    if (!is_array($keypoints)) {
                        $keypoints = [];
                    }

                    // Now you can safely loop
                    $pointsHtml = '';
                    if (!empty($keypoints)) {
                        $pointsHtml .= '<ul>';
                        foreach ($keypoints as $point) {
                            $point = trim($point);
                            if ($point) {
                                $pointsHtml .= '<li>' . $point . '</li>';
                            }
                        }
                        $pointsHtml .= '</ul>';
                    } else {
                        $pointsHtml = 'N/A';
                    }

                    $html .= '
        <tr>
            <td align="center">' . $serial . '</td>
            <td><b>' . $heading . '</b><br>(' . $source . ', ' . $date . ')</td>
            <td>' . $location . '</td>
            <td>' . $screenshotHtml . '</td>
            <td>' . $pointsHtml . '</td>
        </tr>
    ';
                }

                $html .= '</tbody></table>';

                // Write PDF
                $mpdf->WriteHTML($html);

                $fileName = $downloadFileName . '_keypoints_final.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
                return $fileName;
            } else 
         if ($reportType == "lovezihad") {

                // dd($news);
                try {

                    $news = collect($news)->sortBy('chapter_id')->values('ASC');
                    // ==========================
                    // SERVER & PDF CONFIGURATION
                    // ==========================
                    ini_set('memory_limit', '2048M');
                    ini_set('max_execution_time', 0);
                    ini_set('pcre.backtrack_limit', '10000000');
                    ini_set('pcre.recursion_limit', '10000000');

                    $tempDir = storage_path('app/mpdf_temp');
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0777, true);
                    }

                    // ==========================
                    // FONT CONFIGURATION
                    // ==========================
                    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];

                    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];

                    $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'tempDir' => $tempDir,
                        'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
                        'fontdata' => $fontData + [
                            'nirmala' => [
                                'R' => 'nirmala-ui.ttf',
                                'B' => 'nirmala-ui-bold.ttf',
                            ],
                            'dejavusans' => [
                                'R' => 'DejaVuSans.ttf',
                                'B' => 'DejaVuSans-Bold.ttf',
                            ],
                        ],
                        'default_font' => 'nirmala',
                        'margin_top' => 20,
                        'margin_bottom' => 15,
                        'margin_left' => 20,
                        'margin_right' => 20,
                    ]);

                    $mpdf->autoScriptToLang = true;
                    $mpdf->autoLangToFont = true;

                    // ==========================
                    // HEADER & FOOTER
                    // ==========================
                    $headerHtml = View::make('report.mpdf.header', $reportParams)->render();
                    $indexHtml  = View::make('report.mpdf.basereport_heading_index', $reportParams)->render();

                    $mpdf->WriteHTML($headerHtml);
                    $mpdf->WriteHTML($indexHtml);
                    $mpdf->AddPage();

                    // ==========================
                    // STYLES
                    // ==========================
                    $css = <<<CSS
        body { font-family: nirmala; font-size: 17pt; color: #000; line-height: 1.7; }
        h2 { text-transform: uppercase; font-weight: bold; margin-bottom: 10px; font-size: 16pt; }
        h3 { text-decoration: underline; font-size: 13pt; margin: 15px 0 10px 0; }
        p, li { font-size: 12.5pt; margin-bottom: 5px; text-align: justify; }
        ol { margin: 5px 0 10px 0; padding-left: 18px; }
        a { color: #0000EE; text-decoration: none; }
        .meta { font-size: 11pt; margin-bottom: 10px; color: #444; }
        .image { text-align: center; margin: 8px 0; page-break-inside: avoid; }
        .image img {
            width: auto;
            max-width: 250px;
            max-height: 250px;
            object-fit: contain;
            border-radius: 8px;
        }
        .keypoints ol { margin-left: 20px; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 20px 0; }
        .news-item {
            page-break-inside: avoid;
            page-break-after: always;
        }
        CSS;

                    $mpdf->WriteHTML("<style>{$css}</style>", \Mpdf\HTMLParserMode::HEADER_CSS);
                    $mpdf->WriteHTML("<h2>TOPIC - LOVE JIHAD</h2><h3>Latest News (One per Page)</h3>");

                    // ==========================
                    // MAIN CONTENT (ONE PER PAGE)



                    // ==========================
                    $serial = 1;
                    foreach ($news as $report) {
                        $moduleName = !empty($report['modules_name']) ? e($report['modules_name']) : '';
                        $chapterName = !empty($report['chapter_names']) ? e($report['chapter_names']) : '';

                        $chunkHtml = "<div class='news-item'>";
                        $chunkHtml .= "<p><b>" . $serial . ". " . e($report['heading']) . "</b></p>";
                        $chunkHtml .= "<p class='meta'>" . e($report['publish_at']) . " &nbsp;&nbsp; "
                            . e($report['language_name']) . " &nbsp;&nbsp;"
                            . e($report['location_name']) . " (" . e($report['location_state_name']) . ") &nbsp;&nbsp;"
                            . "<a href='" . e($report['link']) . "' target='_blank'>" . e($report['source']) . "</a></p>";


                        $chunkHtml .= "<p style='font-weight:bold; font-size:13pt; color:#000; margin:6px 0 10px 0;'>
        {$moduleName} → {$chapterName}
    </p>";

                        if (!empty($report['news_screenshot'])) {
                            $imageUrl = Helper::getImageUrl($report['news_screenshot']);
                            $chunkHtml .= "<div class='image'><img style='width:auto;height:300px;' src='{$imageUrl}' alt='screenshot'></div>";
                        }

                        if (!empty($report['keypoint'])) {
                            $chunkHtml .= "<div class='keypoints'><ol>";
                            foreach ($report['keypoint'] as $point) {
                                $pointText = $point['keypoint'] ?? '';
                                if (!empty($pointText)) {
                                    $chunkHtml .= "<li>" . e($pointText) . "</li>";
                                }
                            }
                            $chunkHtml .= "</ol></div>";
                        }

                        $chunkHtml .= "<hr></div>";

                        $mpdf->WriteHTML($chunkHtml, \Mpdf\HTMLParserMode::HTML_BODY);
                        $serial++;
                    }

                    // ==========================
                    // SAVE PDF FILE
                    // ==========================
                    $fileName = 'LJReport.pdf';
                    $savePath = storage_path(config('app.download_report_base_folder') . '/' . $fileName);

                    $mpdf->Output($savePath, \Mpdf\Output\Destination::FILE);

                    return $fileName;
                } catch (\Throwable $e) {
                    \Log::error('PDF Generation Error', ['error' => $e->getMessage()]);
                    return response("PDF generation failed: " . $e->getMessage(), 500);
                }
            } else {

                ///////////////////////////////
                /// Below is to create PDF
                ///////////////////////////////

                // new approach Feb 16
                $mpdf = new \Mpdf\Mpdf();

                //write header
                $view_content_mpdfview_header = View::make('report.mpdf.header', $reportParams)->render();
                $mpdf->WriteHTML($view_content_mpdfview_header);

                //write heading and index
                $view_content_mpdfview_basereport_heading_index = View::make('report.mpdf.basereport_heading_index', $reportParams)->render();
                $mpdf->WriteHTML($view_content_mpdfview_basereport_heading_index);

                //write content


                if ($report_data_type == 'document_report') {
                    foreach ($news as $key => $reports) {


                        // if ($roleId === 1) {
                        $headingContent = '<p style="font-weight:bold;margin-top: 10px;">' . $reports['serial_number'] . '. ' . $reports['heading'];

                        // old tags
                        // if (count($reports['tag']) > 0) {
                        //     $headingContent .= '<span style="color:#88b165;">
                        // (';
                        //     foreach ($reports['tag'] as $key => $tag) {
                        //         $headingContent .= $tag['tag'] . ', ';
                        //     }
                        //     $headingContent .= ')</span>';
                        // }

                        // // New Team tags
                        // if (count($reports['team_tags']) > 0) {
                        //     $headingContent .= '<span style="color:#88b165;">
                        // (';
                        //     foreach ($reports['team_tags'] as $key => $team_tag) {
                        //         if ($team_tag['tag'] != 'NA') {
                        //             $headingContent .= $team_tag['parent_tag'] . ' -> ' . $team_tag['tag'] . ", ";
                        //         }
                        //     }
                        //     $headingContent .= ')</span>';
                        // }


                        $headingContent .= '</p>';
                        // } else {
                        //     $headingContent = '<tr>
                        //     <td><p style="font-weight:bold;margin-top: 10px;">' . $snoHeading . '. ' . $reports['heading'] . '</p>';
                        // }
                        $mpdf->WriteHTML($headingContent);


                        $keyPointHeadingContent = '<br/>';
                        $mpdf->WriteHTML($keyPointHeadingContent);
                        foreach ($reports['keypoint'] as $key => $keypoint) {
                            $snoKeypoint = $key + 1;
                            $keyPoint = trim($keypoint['keypoint']) . '&nbsp;';
                            $mpdf->WriteHTML($keyPoint);
                        }

                        if ($reports['location_name']) {
                            $locationContent = 'Location: ' . $reports['location_name'];
                            if ($reports['location_state_name'] && ($reports['location_state_name'] != 'NA')) {
                                $locationContent .= " ( " . $reports['location_state_name'] . " )";
                            }
                            $mpdf->WriteHTML($locationContent);
                        }
                    }

                    ////////////////////
                    // links
                    ////////////////////

                    $headingContent = '<tr>
                        <td><p style="font-weight:bold;margin-top: 10px;">References:</p></td></tr>';
                    $mpdf->WriteHTML($headingContent);
                    $snoHeading = 1;
                    foreach ($news as $key => $reports) {

                        $headingContent = '<tr><td>';
                        $mpdf->WriteHTML($headingContent);

                        $publishAt = Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('d/m/Y');
                        $sourceContent = $snoHeading . " " . ucfirst($reports['source']) . ", " . $publishAt;
                        $mpdf->WriteHTML($sourceContent);

                        $sourceContent = '<br/><a href="' . $reports['link'] . '" style="color:blue">' . $reports['link'] . '</a><br/>';
                        $mpdf->WriteHTML($sourceContent);

                        foreach ($reports['screenshots'] as $key => $screenshot) {
                            if ($screenshot['screenshot'] != "" && $screenshot['screenshot_type'] == "front_page") {
                                $screenshot = '<a href="' . Helper::getImageUrl($screenshot['screenshot']) . '" style="color:blue">Front Page Screenshot</a> | ';
                                $mpdf->WriteHTML($screenshot);
                            }
                        }
                        foreach ($reports['screenshots'] as $key => $screenshot) {
                            if ($screenshot['screenshot'] != "" && $screenshot['screenshot_type'] == "full_news") {
                                $screenshot = '<a href="' . Helper::getImageUrl($screenshot['screenshot']) . '" style="color:blue">Full Page Screenshot</a> | ';
                                $mpdf->WriteHTML($screenshot);
                            }
                        }

                        foreach ($reports['featuredimages'] as $key => $featured_image) {
                            if ($featured_image['featured_image'] != "") {
                                $snoFeaturedImage = $key + 1;
                                $featuredImage = '<a href="' . Helper::getImageUrl($featured_image['featured_image']) . '" style="color:blue">Featured Image</a> | ';
                                $mpdf->WriteHTML($featuredImage);
                            }
                        }

                        foreach ($reports['images'] as $key => $image) {
                            $snoImage = $key + 1;
                            $image = '<a href="' . $image['imagelink'] . '" style="color:blue">Image</a>';
                            $mpdf->WriteHTML($image);
                        }

                        foreach ($reports['videos'] as $key => $video) {
                            $snoVideo = $key + 1;
                            $video = '<a href="' . $video['videolink'] . '" style="color:blue">Video</a> |';
                            $mpdf->WriteHTML($video);
                        }


                        foreach ($reports['documents'] as $key => $document) {
                            $sno = $key + 1;
                            if ($document['document_type'] != '' && $document['document_type'] == 'general') {
                                if ($document['document_name'] != '') {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">' . $document['document_name'] . '</a> | ';
                                } else {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">Document Link</a> | ';
                                }
                            }
                            if ($document['document_type'] != '' && $document['document_type'] == 'fir_copy') {
                                if ($document['document_name'] != '') {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">' . $document['document_name'] . '</a> | ';
                                } else {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">FIR Link</a> | ';
                                }
                            }
                            $mpdf->WriteHTML($documentLink);
                        }

                        foreach ($reports['followups'] as $key => $followup) {
                            $followup = '&nbsp;<a href="' . $followup['followup_link'] . '" style="color:blue">' . $followup['followup_name'] . '</a> | ';
                            $mpdf->WriteHTML($followup);
                        }


                        $headingContentEnd = '</td></tr>';
                        $mpdf->WriteHTML($headingContentEnd);

                        $snoHeading++;
                    }
                } else {

                    foreach ($news as $key => $reports) {

                        // if ($roleId === 1) {
                        $headingContent = '<p style="font-weight:bold;margin-top: 10px;">' . $reports['serial_number'] . '. ' . $reports['heading'];
                        $headingContent .= '</p>';
                        $mpdf->WriteHTML($headingContent);


                        $sourceContent = '<p>';
                        $mpdf->WriteHTML($sourceContent);

                        $publishAt = Carbon\Carbon::createFromTimestamp(strtotime($reports['publish_at']))->format('d/m/Y');
                        $sourceContent = '(' . $reports['source'] . ', ' . $publishAt . ') <a href="' . $reports['link'] . '" style="color:blue">News Link</a> | ';
                        $mpdf->WriteHTML($sourceContent);

                        foreach ($reports['screenshot'] as $key => $screenshot) {
                            if ($screenshot['screenshot'] != "" && $screenshot['screenshot_type'] == "front_page") {
                                $screenshot = '&nbsp;<a href="' . Helper::getImageUrl($screenshot['screenshot']) . '" style="color:blue">Front Page Screenshot Link</a> | ';
                                $mpdf->WriteHTML($screenshot);
                            }
                        }
                        foreach ($reports['screenshot'] as $key => $screenshot) {
                            if ($screenshot['screenshot'] != "" && $screenshot['screenshot_type'] == "full_news") {
                                $screenshot = '&nbsp;<a href="' . Helper::getImageUrl($screenshot['screenshot']) . '" style="color:blue">Full Page Screenshot Link</a> | ';
                                $mpdf->WriteHTML($screenshot);
                            }
                        }

                        foreach ($reports['feateredimage'] as $key => $featured_image) {
                            if ($featured_image['featured_image'] != "") {
                                $snoFeaturedImage = $key + 1;
                                $featuredImage = '&nbsp;<a href="' . Helper::getImageUrl($featured_image['featured_image']) . '" style="color:blue">Featured Image Link ' . $snoFeaturedImage . '</a> | ';
                                $mpdf->WriteHTML($featuredImage);
                            }
                        }

                        foreach ($reports['imagelink'] as $key => $image) {
                            $snoImage = $key + 1;
                            $image = '&nbsp;<a href="' . $image['imagelink'] . '" style="color:blue">Image Link ' . $snoImage . '</a> | ';
                            $mpdf->WriteHTML($image);
                        }

                        foreach ($reports['videolink'] as $key => $video) {
                            $snoVideo = $key + 1;
                            $video = '&nbsp;<a href="' . $video['videolink'] . '" style="color:blue">Video Link ' . $snoVideo . '</a> | ';
                            $mpdf->WriteHTML($video);
                        }

                        foreach ($reports['document'] as $key => $document) {
                            $sno = $key + 1;
                            if ($document['document_type'] != '' && $document['document_type'] == 'general') {
                                if ($document['document_name'] != '') {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document'])  . '" style="color:blue">' . $document['document_name'] . '</a> | ';
                                } else {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">Document Link</a> | ';
                                }
                            }
                            if ($document['document_type'] != '' && $document['document_type'] == 'fir_copy') {
                                if ($document['document_name'] != '') {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">' . $document['document_name'] . '</a> | ';
                                } else {
                                    $documentLink = '<a href="' . Helper::getImageUrl($document['document']) . '" style="color:blue">FIR Link</a> | ';
                                }
                            }
                            $mpdf->WriteHTML($documentLink);
                        }

                        foreach ($reports['followup'] as $key => $followup) {
                            $followup = '&nbsp;<a href="' . $followup['followup_link'] . '" style="color:blue">' . $followup['followup_name'] . '</a> | ';
                            $mpdf->WriteHTML($followup);
                        }

                        $sourceContent = '</p>';
                        $mpdf->WriteHTML($sourceContent);

                        if ($reports['location_name']) {
                            $locationContent = 'Location: ' . $reports['location_name'];
                            if ($reports['location_state_name'] && ($reports['location_state_name'] != 'NA')) {
                                $locationContent .= " ( " . $reports['location_state_name'] . " )";
                            }
                            $mpdf->WriteHTML($locationContent);
                        }

                        $keyPointHeadingContent = '<br/>Key Points:';
                        $mpdf->WriteHTML($keyPointHeadingContent);
                        foreach ($reports['keypoint'] as $key => $keypoint) {
                            $snoKeypoint = $key + 1;
                            $keyPoint = '<p>' . $snoKeypoint . '. ' . trim($keypoint['keypoint']) . '</p><br>';
                            $mpdf->WriteHTML($keyPoint);
                        }
                    }
                }




                //Write footer
                $view_content_mpdfview_footer = View::make('report.mpdf.footer', $reportParams)->render();
                $mpdf->WriteHTML($view_content_mpdfview_footer);

                $fileName = $downloadFileName . '.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
                return $fileName;
            }
        }
    }

    // daily_report & custom_report
    // wordview_allteam, googledoc_allteam, mpdfview_allteam
    public function downloadReportAllTeam($request, $from, $to, $To_Year, $From_Year, $allLocationIds, $roleId, $moduleNameArr, $mode)
    {
        // webview_allteam.blade

        // echo "inside downloadReportAllTeam";
        // echo "<pre>";
        // print_r($request->all());
        // dd($request->all());
        // dd(request()->fullUrl());
        // dd($roleId);

        // dd($from);

        // dd($request->all());

        // $lovezihad = null;

        // if ($request['team_name'][0] == "11" && $request['module'][0] == "104") {
        //     $loveZihad = 1;
        // }
        $loveZihad = 0;
        if (!empty($request['team_name'][0]) && !empty($request['module'][0])) {
            if ($request['team_name'][0] == '11' && $request['module'][0] == '104') {
                $loveZihad = 1;
            }
        }


        // dd($monthDayEnd);

        if (!empty($To_Year && $From_Year)) {
            $fromExpl = explode('-', $From_Year);
            $monthDayStart = $fromExpl[1] . '-' . $fromExpl[0];

            $toExpl = explode('-', $To_Year);
            $monthDayEnd = $toExpl[1] . '-' . $toExpl[0];

            $toYear = $To_Year;
            $fromYear = $From_Year;

            $onlyfromYear = $fromExpl[2];
            $onlytoYear = $toExpl[2];
        }




        // dd($from,$to);

        // dd($year);





        $showReportForUser = "";
        if ($request->from_user) {
            $showReportForUser = $request->from_user;
        }

        // if ($roleId == config('app.basic_user_role_id')) {
        //     $user = auth()->user();
        //     $showReportForUser = $user->id;
        // } else {
        //     if ($request->from_user) {
        //         $showReportForUser = $request->from_user;
        //     }
        // }


        $show_report_type_new = $request->show_report_type;

        // dd($show_report_type_new);

        $report_data_type = $request->report_data_type;

        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;

        $group_by_language = '';
        if ($request->group_by_language) {
            $group_by_language = $request->group_by_language;
        }
        $group_by_location = '';
        if ($request->group_by_location) {
            $group_by_location = $request->group_by_location;
        }

        $tagArr = array();
        if ($request->tags) {
            $tags = $request->tags;
            if (!empty($tags)) {
                $tagArr = explode("\r\n", $tags);
            }
        }
        // dd($tagArr);

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }
        // dd($module_ids_arr);

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }
        // dd($chapter_ids_arr);        

        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }

        $tName = $request->team_name;

        $location_state_id = array();
        if ($request->location_state_id) {
            $location_state_id = $request->location_state_id;
        }

        // $language_id = $request->language_id;
        $language_id = array();
        if ($request->language_id) {
            $language_id = $request->language_id;
        }

        $fir_documents = 0;
        if ($request->fir_documents) {
            $fir_documents = $request->fir_documents;
        }

        $show_index = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_index)) {
            $show_index = false;
        }
        $show_chapter = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_chapter)) {
            $show_chapter = false;
        }
        $show_link_text = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_link_display)) {
            $show_link_text = false;
        }
        $show_link = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_link)) {
            $show_link = false;
        }
        $show_headline = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_headline)) {
            $show_headline = false;
        }
        $show_source = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_source)) {
            $show_source = false;
        }
        $show_tags = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_tags)) {
            $show_tags = false;
        }
        $show_videolinks = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_videolinks)) {
            $show_videolinks = false;
        }
        $show_keypoints = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_keypoints)) {
            $show_keypoints = false;
        }
        $show_imagesources = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_imagesources)) {
            $show_imagesources = false;
        }
        $show_front_page_screenshots = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_front_page_screenshots)) {
            $show_front_page_screenshots = false;
        }
        $show_full_page_screenshots = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_full_news_screenshots)) {
            $show_full_page_screenshots = false;
        }
        $show_featuredimages = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_featuredimages)) {
            $show_featuredimages = false;
        }
        $show_date = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_date)) {
            $show_date = false;
        }
        $show_tags = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_tags)) {
            $show_tags = false;
        }
        $show_fir = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_fir)) {
            $show_fir = false;
        }
        $show_documents = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_documents)) {
            $show_documents = false;
        }
        $show_language = true;
        if (($report_data_type == "custom_report") && empty($request->custom_report_languages)) {
            $show_language = false;
        }


        $find_more = '';
        if (!empty($request->find_more)) {
            $find_more = $request->find_more;
        }

        // echo $reportTitle;
        // exit();

        //News of a date = published_date
        $dateRangeAttribute = "reports.publish_at";

        $newsOrderByOrder = "desc";
        if (!empty($request->news_order_by_order)) {
            $newsOrderByOrder = $request->news_order_by_order;
        }
        // echo $newsOrderByOrder;

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $teamName = "";
        if (!empty($tName)) {
            $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
        }
        $moduleName = "";
        $chapterName = "";

        if ($report_data_type == "custom_report") {
            $reportTitle = is_object($teamName) ? ucwords(strtolower($teamName->name)) . " | Custom Report" : "Custom Report";
        } else {
            $reportTitle = is_object($teamName) ? ucwords(strtolower($teamName->name)) . " | Daily Report" : "Daily Report";
        }

        $teamsData = array();
        $teams = Team::all()->toArray();
        foreach ($teams as $team) {
            $teamsData[$team['id']] = $team['name'];
        }

        $dataAllTeamReport = Report::with(['team'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select('reports.team_id', DB::connection('setfacts')->Raw('COUNT(teams.id) AS team_count'))
            ->join('teams', 'reports.team_id', '=', 'teams.id')
            ->where('teams.is_public', 1)
            ->groupBy('reports.team_id'); // Group by team_id

        $data = Report::with(['chapter'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select(
                'reports.team_id',
                'reports.chapter_id',
                DB::connection('setfacts')->Raw('COUNT(DISTINCT(`reports`.`id`)) AS chapter_count')
            )
            ->join('chapters', function ($join) {
                $join->on('reports.chapter_id', '=', 'chapters.id');
                $join->wherenull('chapters.deleted_at');
            })
            ->join('modules', function ($join) {
                $join->on('reports.module_id', '=', 'modules.id');
                $join->wherenull('modules.deleted_at');
            })
            ->groupBy('reports.team_id', 'reports.chapter_id');




        $firDocumentReportIdArr = array();
        if ($fir_documents == 1) {
            $firDocumentReportIdArr = $this->getFirDocumentReportIds();
            if (count($firDocumentReportIdArr) > 0) {
                $data = $data->wherein('reports.id', $firDocumentReportIdArr);
                $dataAllTeamReport = $dataAllTeamReport->wherein('reports.id', $firDocumentReportIdArr);
            }
        }


        if (!empty($from && $to)) {
            // dd("From & To");
            $data = $data->whereBetween($dateRangeAttribute, [$from, $to]);
            $dataAllTeamReport = $dataAllTeamReport->whereBetween($dateRangeAttribute, [$from, $to]);
        }


        // if (!empty($fromYear && $toYear) && !empty($monthDayStart && $monthDayEnd)) {
        //     // dd("From & To & To_Year");
        //     $data = $data->whereYear($dateRangeAttribute, $onlyfromYear)->whereRaw("DATE_FORMAT($dateRangeAttribute, '%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);

        //     $dataAllTeamReport = $dataAllTeamReport->whereYear($dateRangeAttribute, $fromYear)->whereRaw("DATE_FORMAT($dateRangeAttribute, '%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);
        // }


        if (!empty($fromYear) && !empty($toYear) && !empty($monthDayStart) && !empty($monthDayEnd)) {
            // Apply year range and month-day range filters
            $data = $data->whereBetween(DB::raw("YEAR($dateRangeAttribute)"), [$onlyfromYear, $onlytoYear])
                ->whereRaw("DATE_FORMAT($dateRangeAttribute, '%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);

            $dataAllTeamReport = $dataAllTeamReport->whereBetween(DB::raw("YEAR($dateRangeAttribute)"), [$onlyfromYear, $onlytoYear])
                ->whereRaw("DATE_FORMAT($dateRangeAttribute, '%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);
        }

        if (!empty($tName)) {
            $data = $data->wherein('reports.team_id', $tName);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.team_id', $tName);
        }

        if (!empty($request->first_time)) {
            $first_time = $request->first_time;
            $data = $data->where('first_time', $first_time);
            $dataAllTeamReport = $dataAllTeamReport->where('first_time', $first_time);
        }

        if (!empty($request->followup)) {
            $followup = $request->followup;
            $data = $data->where('has_followup', $followup);
            $dataAllTeamReport = $dataAllTeamReport->where('has_followup', $followup);
        }

        if (!empty($request->calendar_date)) {
            $calendar_date = $request->calendar_date;
            $data = $data->where('calendar_date', $calendar_date);
            $dataAllTeamReport = $dataAllTeamReport->where('calendar_date', $calendar_date);
        }

        if (count($language_id) > 0) {
            $data = $data->wherein('reports.language_id', $language_id);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.language_id', $language_id);
        }

        if (count($location_id) > 0) {
            $data = $data->wherein('location_id', $location_id);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.location_id', $location_id);
        }

        if (count($location_state_id) > 0) {
            $data = $data->wherein('location_state_id', $location_state_id);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.location_state_id', $location_state_id);
        }

        if (!empty($allLocationIds)) {
            $locationIdsArr = explode(',', $allLocationIds);
            $data = $data->whereIn('location_id', $locationIdsArr);
            $dataAllTeamReport = $dataAllTeamReport->whereIn('reports.location_id', $locationIdsArr);
        }

        if (count($module_ids_arr) > 0) {
            $data = $data->wherein('reports.module_id', $module_ids_arr);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.module_id', $module_ids_arr);
        }
        if (count($chapter_ids_arr) > 0) {
            $data = $data->wherein('reports.chapter_id', $chapter_ids_arr);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        if (!empty($showReportForUser)) {
            $data = $data->where('user_id', $showReportForUser);
            $dataAllTeamReport = $dataAllTeamReport->where('user_id', $showReportForUser);
        }


        if (!empty($show_report_type_new)) {
            // dd("N");
            $data = $data->where('reports.report_type', $show_report_type_new);
            $dataAllTeamReport = $dataAllTeamReport->where('reports.report_type', $show_report_type_new);
        }







        //////////////////////////////////
        // group by chapter_id
        //////////////////////////////////
        $data = $data->wherenull('chapters.deleted_at');

        if ($report_data_type == "document_report") {

            if ($group_by_location == 1) {
                $data = $data->orderBy('reports.location_id');
            }
            if ($group_by_language == 1) {
                $data = $data->orderBy('reports.language_id');
            }

            // $data = $data->orderBy('orderByData', 'DESC');
        } else {

            if ($group_by_location == 1) {
                $data = $data->orderBy('reports.location_id');
            }
            if ($group_by_language == 1) {
                $data = $data->orderBy('reports.language_id');
            }


            $data = $data->orderBy('reports.id', $newsOrderByOrder);
        }

        $data = $data->groupBy('reports.chapter_id');




        // $data = $data->toSql();
        // dd($data);

        // $sql = $data->toSql();
        // $bindings = $data->getBindings();
        // $finalSql = Helper::interpolateQuery($sql, $bindings);

        // dd($finalSql);

        $data = $data->get()->toArray();
        // dd($data);


        // loop for chapterIds          
        $chapterIds = array();
        foreach ($data as $datas) {
            $chapterIds[] = $datas['chapter_id'];
        }

        // dd($chapterIds);   



        $repcountData = clone $dataAllTeamReport;


        //  $sql = $repcountData->toSql();
        //     $bindings = $repcountData->getBindings();
        //     $finalSql = Helper::interpolateQuery($sql, $bindings);

        //     dd($finalSql);

        $repcountData = $repcountData->groupBy('reports.id');
        $repcountData = $repcountData->get()->count();
        $repcount = $repcountData;
        // dump($repcountData);        

        if ($mode == "getcount") {
            return $repcount;
        }

        $keyNews = [];
        $news = [];

        if ($repcount > 0) {

            //////////////////////////////////
            // group by module_id
            //////////////////////////////////
            // $dataAllTeamReport = $dataAllTeamReport->wherenull('modules.deleted_at');
            $dataAllTeamReport = $dataAllTeamReport->orderBy('reports.publish_at', $newsOrderByOrder);
            $dataAllTeamReport = $dataAllTeamReport->whereIn('chapter_id', $chapterIds);
            $dataAllTeamReport = $dataAllTeamReport->groupBy('reports.team_id');
            // $dataAllTeamReport = $dataAllTeamReport->groupBy('reports.module_id');
            // $dataModule = $dataAllTeamReport->toSql();
            // dd($dataModule);

            // $sql = $dataAllTeamReport->toSql();
            // $bindings = $dataAllTeamReport->getBindings();
            // $finalSql = Helper::interpolateQuery($sql, $bindings);

            // dd($finalSql);

            $dataAllTeamReport = $dataAllTeamReport->get()->toArray();
            // dd($dataAllTeamReport);


            // loop for teamIds          
            $teamIds = array();
            foreach ($dataAllTeamReport as $datas) {
                $teamIds[] = $datas['team_id'];
            }
            // dd($teamIds);               

            $per_page_reports = config('app.per_page_reports');
            $page = 1;
            $count = 0;
            if (!empty($request->page)) {
                $page = $request->page;
                $count = $per_page_reports * ($page - 1);
            }

            DB::connection('setfacts')->select(DB::raw('SET @count:=' . $count . ';'));
            $news = Report::with(
                ['keypoint', 'chapter', 'module', 'tag', 'videolink', 'imagelink', 'screenshot', 'feateredimage', 'document', 'followup']
            )->select(
                'reports.*',
                'report_sources.source',
                'languages.id as language_id',
                'languages.name as language_name',
                'locations.name as location_name',
                'location_states.name as location_state_name',
                'chapters.name as chapter_name',
                'modules.name as modules_name',
                'teams.name as teams_name',
                'first_screenshot.screenshot as  news_screenshot',


                DB::raw('(@count:=@count+1) AS serial_number'),
            )
                ->join('report_sources', 'report_sources.id', 'reports.report_source_id');

            $news = $news->join('languages', 'reports.language_id', 'languages.id');
            $news = $news->leftjoin('locations', 'reports.location_id', 'locations.id');
            $news = $news->leftjoin('location_states', 'reports.location_state_id', 'location_states.id');
            $news = $news->leftjoin('modules', 'reports.module_id', 'modules.id');
            $news = $news->leftjoin('chapters', 'reports.chapter_id', 'chapters.id');
            $news = $news->leftjoin('teams', 'reports.team_id', 'teams.id');

            $news = $news->whereIn('reports.team_id', $teamIds);
            $news = $news->whereIn('reports.chapter_id', $chapterIds);

            // $news = $news->with('chapter')->where('chapter_id', $chapterItem->chapter_id)->where('is_deleted', 0);

            $news = $news->where('is_deleted', 0)->where('reports.active', 1);

            $firDocumentReportIdArr = array();
            if ($fir_documents == 1) {
                $firDocumentReportIdArr = $this->getFirDocumentReportIds();
                if (count($firDocumentReportIdArr) > 0) {
                    $news = $news->wherein('reports.id', $firDocumentReportIdArr);
                }
            }

            if (!empty($from && $to)) {
                $news = $news->whereBetween($dateRangeAttribute, [$from, $to]);
            }

            // if (!empty($monthDayStart && $monthDayEnd && $toYear && $fromYear)) {
            //     $news = $news->whereYear($dateRangeAttribute, $year)->whereRaw("DATE_FORMAT($dateRangeAttribute,'%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);
            // }

            if (!empty($monthDayStart) && !empty($monthDayEnd) && !empty($fromYear) && !empty($toYear)) {
                $news = $news->whereBetween(DB::raw("YEAR($dateRangeAttribute)"), [$onlyfromYear, $onlytoYear])
                    ->whereRaw("DATE_FORMAT($dateRangeAttribute, '%m-%d') BETWEEN ? AND ?", [$monthDayStart, $monthDayEnd]);
            }




            if (count($language_id) > 0) {
                $news = $news->wherein('language_id', $language_id);
            }


            if (!empty($request->first_time)) {
                $first_time = $request->first_time;
                $news = $news->where('first_time', $first_time);
            }

            if (!empty($request->followup)) {
                $followup = $request->followup;
                $news = $news->where('has_followup', $followup);
            }

            if (!empty($request->show_report_type)) {
                $report_type_new = $request->show_report_type;
                $news = $news->where('reports.report_type', $report_type_new);
            }

            $calendate_date_new = '';
            if (!empty($request->calendar_date)) {
                $calendar_date = $request->calendar_date;
                $news = $news->where('calendar_date', $calendar_date);
                $calendate_date_new = $calendar_date;
            }

            if (count($location_id) > 0) {
                $news = $news->wherein('reports.location_id', $location_id);
            }

            if (count($location_state_id) > 0) {
                $news = $news->wherein('reports.location_state_id', $location_state_id);
            }

            if (!empty($allLocationIds)) {
                $locationIdsArr = explode(',', $allLocationIds);

                $news = $news->whereIn('reports.location_id', $locationIdsArr);
            }

            if (!empty($showReportForUser)) {
                $news = $news->where('user_id', $showReportForUser);
            }



            // dd($request);



            if (count($tagArr) > 0) {
                $news = $news->leftjoin('report_team_tags', 'report_team_tags.report_id', 'reports.id');
                $news = $news->leftjoin('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
                $news = $news->leftjoin('report_keypoints', 'report_keypoints.report_id', 'reports.id');

                $news->where(function ($internalQuery) use ($tagArr) {
                    foreach ($tagArr as $tag) {
                        $internalQuery->orWhere('reports.heading', 'like', '%' . $tag . '%')
                            ->orWhere('reports.link', 'like', '%' . $tag . '%')
                            ->orWhere('team_tags.tag', 'like', '%' . $tag . '%')
                            ->orWhere('report_keypoints.keypoint', 'like', '%' . $tag . '%');
                    }
                });
                $news = $news->groupBy('reports.id');
            }

            if ($report_data_type == "document_report" && !empty($document_report_tags)) {
                $news = $news->join('report_team_tags', 'report_team_tags.report_id', 'reports.id');
                $news = $news->join('team_tags', 'team_tags.id', 'report_team_tags.tag_id');
                $news = $news->where('team_tags.id', $document_report_tags);
                $news = $news->groupBy('reports.id');
            }

            if ($report_data_type == "document_report") {

                //Group by Location and Language
                if ($group_by_location == 1) {
                    $news = $news->orderBy('reports.location_id');
                }
                if ($group_by_language == 1) {
                    $news = $news->orderBy('reports.language_id');
                }

                // $news = $news->orderBy('orderByData', 'DESC');
            } else {
                //Group by Location and Language
                if ($group_by_location == 1) {
                    $news = $news->orderBy('reports.location_id');
                }
                if ($group_by_language == 1) {
                    $news = $news->orderBy('reports.language_id');
                }

                $news = $news->orderBy('reports.team_id');
                $news = $news->orderBy('reports.publish_at', $newsOrderByOrder);
            }


            $news->leftJoin(DB::raw('(
    SELECT rs1.report_id, rs1.screenshot
    FROM report_screenshots rs1
    INNER JOIN (
        SELECT report_id, MIN(id) AS min_id
        FROM report_screenshots
        GROUP BY report_id
    ) rs2 ON rs1.id = rs2.min_id
) AS first_screenshot'), 'first_screenshot.report_id', '=', 'reports.id');



            // $sql = $news->toSql();
            // $bindings = $news->getBindings();
            // $finalSql = Helper::interpolateQuery($sql, $bindings);

            // dd($finalSql);

            if ($mode == "view") {
                $news = $news->paginate(config('app.per_page_reports'))->withQueryString();
            } else {
                $news = $news->get()->toArray();
            }
            // dd($news);
            // dump($news[0]);

        }

        // $keyNews = Paginate::paginate($keyNews,1);
        // dd($keyNews);
        // echo "in";
        // exit();

        $mytime = Carbon\Carbon::now();

        $all_teamArr = array();
        //  dd($dataAllTeamReport);
        foreach ($dataAllTeamReport as $key => $teamData) {
            $all_teamArr[] = str_replace(" ", "", ucwords(strtolower($teamData['team']['name'])));
        }
        $team_name_converted = implode("_", $all_teamArr);
        // dd($team_name_converted);

        if ($team_name_converted == "") {
            $team_name_converted = "all_teams";
        }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {
            $downloadFileName = date('jM') . "_" . $team_name_converted;
        }

        if ($teamName) {
            $downloadFileName = date('jM') . "_" . str_replace(" ", "", ucwords(strtolower($teamName->name)));
        }

        $publicReportColor = "blue";
        if (config('app.color_scheme') == 'pink') {
            $publicReportColor = "red";
        }

        $viewLayout = "layouts.admin";
        // if ($roleId == 0) {
        //     $viewLayout = "layouts.webview";
        // }

        // dd($viewLayout);

        $chapterReportData = $this->fetchChapterReportCount();

        $fromPage = "";
        $all_issues = "";
        $all_sm_cals = "";
        $prevDate = "";
        $nextDate = "";
        $selectedDate = "";
        $dailyPopupDate = "";
        $all_count_popup = "";
        if (!empty($request->fromPage)) {
            $fromPage = $request->fromPage;
            // dd($from);

            $today = Carbon\Carbon::createFromFormat('Y-m-d', $from);
            $dmForDate = $today->format('d-m-Y');
            $dataDM = $this->fetchDailyMonitoring($dmForDate);
            // dd($dataDM);

            $all_issues = $dataDM['all_issues'];
            $all_sm_cals = $dataDM['all_sm_cals'];
            $prevDate = $dataDM['prevDate'];
            $nextDate = $dataDM['nextDate'];
            $selectedDate = $dataDM['selectedDate'];

            $dailyPopupDate = $dataDM['dailyPopupDate'];
            $all_count_popup = $dataDM['all_count_popup'];

            $fromFormated = \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y');
            $reportTitle = $fromFormated . " | " . ucwords(strtolower($teamName->name)) . " | Daily Report";
        }
        // dd($news);

        // dd($calendar_date);



        $reportParams = [
            'to' => $to,
            'from' => $from,
            'dataAllTeamReport' => $dataAllTeamReport,
            'data' => $data,
            'keyNews' => $news,
            'mytime' => $mytime,
            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading,

            'show_index' => $show_index,
            'show_chapter' => $show_chapter,
            'show_link_text' => $show_link_text,
            'show_link' => $show_link,
            'show_headline' => $show_headline,
            'show_source' => $show_source,
            'show_tags' => $show_tags,
            'show_videolinks' => $show_videolinks,
            'show_keypoints' => $show_keypoints,
            'show_imagesources' => $show_imagesources,
            'show_front_page_screenshots' => $show_front_page_screenshots,
            'show_full_page_screenshots' => $show_full_page_screenshots,
            'show_featuredimages' => $show_featuredimages,
            'show_date' => $show_date,
            'show_fir' => $show_fir,
            'show_documents' => $show_documents,
            'show_language' => $show_language,
            'report_data_type' => $report_data_type,
            'roleId' => $roleId,
            'moduleNameArr' => $moduleNameArr,
            'viewLayout' => $viewLayout,
            'publicReportColor' => $publicReportColor,
            'chapterReportData' => $chapterReportData,
            'find_more' => $find_more,
            // 'otherChapterResults' => $otherChapterResults,
            'module_ids_arr' => $module_ids_arr,
            'team_name' => $request->team_name,

            'fromPage' => $fromPage,

            'all_issues' => $all_issues,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'selectedDate' => $selectedDate,
            'all_sm_cals' => $all_sm_cals,
            'all_count_popup' => $all_count_popup,
            'dailyPopupDate' => $dailyPopupDate,
            'calendar_date' => $calendate_date_new,
            'zihad' => $loveZihad,
            'repcount' => isset($news) ? count($news) : 0,

        ];

        // dd($reportParams);        

        if ($mode == "view") {

            $view_content_web =  view('report.webview_allteam',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else {
            if ($reportType == "word") {

                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_content_word = View::make('report.wordview_allteam', $reportParams)->render();

                $view_content_word = str_replace("&amp;", "and", $view_content_word);

                //added on Dec 13 for issue of #039 error '
                $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

                // echo "<pre>";print_r($view_content_word);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }
                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "googledoc") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_contents_googledoc = View::make('report.googledoc_allteam', $reportParams)->render();

                $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);

                //added on Dec 13 for issue of #039 error '
                $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

                // echo "<pre>";print_r($view_contents_googledoc);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }
                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "excel") {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

                // Set headers
                $headers = ['S.No', 'Publish Date', 'State', 'District', 'Issues', 'Categories', 'Sub Categories', 'Heading', 'Keypoints', 'Link'];
                $columnIndex = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($columnIndex . '1', $header);
                    $columnIndex++;
                }

                // Write data
                $rowIndex = 2;
                foreach ($news as $report) {
                    $keypoints = '';
                    foreach ($report['keypoint'] as $key => $keypoint) {
                        $keypoints .= ($key + 1) . '. ' . $keypoint['keypoint'] . "\n";
                    }

                    $location = $report['location_name'] ?? '';
                    if (!empty($report['location_state_name']) && $report['location_state_name'] !== 'NA') {
                        $location .= ' (' . $report['location_state_name'] . ')';
                    }

                    $sheet->setCellValue("A$rowIndex", $report['serial_number'] ?? $rowIndex - 1);
                    $sheet->setCellValue("B$rowIndex", \Carbon\Carbon::parse($report['publish_at'])->format('d/m/Y'));
                    $sheet->setCellValue("C$rowIndex", $report['location_name'] ?? '');
                    $sheet->setCellValue("D$rowIndex", $report['location_state_name'] ?? '');
                    $sheet->setCellValue("E$rowIndex", $report['teams_name'] ?? '');
                    $sheet->setCellValue("F$rowIndex", $report['modules_name'] ?? '');
                    $sheet->setCellValue("G$rowIndex", $report['chapter_name'] ?? '');
                    $sheet->setCellValue("H$rowIndex", $report['heading'] ?? '');
                    $sheet->setCellValue("I$rowIndex", $keypoints);

                    $link = $report['link'] ?? '';
                    $sheet->setCellValueExplicit("J$rowIndex", $link, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    if (!empty($link)) {
                        $sheet->getCell("J$rowIndex")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("J$rowIndex")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                        $sheet->getStyle("J$rowIndex")->getFont()->setUnderline(true);
                    }

                    $rowIndex++;
                }

                // Dynamic styling
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Bold headers
                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);

                // Fix header row height
                $sheet->getRowDimension(1)->setRowHeight(20);

                // Fix all other rows height (smaller & uniform)
                for ($r = 2; $r <= $highestRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                }

                // Set manual column widths
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(false); // disable autosize
                }
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(40); // Heading column
                $sheet->getColumnDimension('I')->setWidth(50); // Keypoints column
                $sheet->getColumnDimension('J')->setWidth(30); // Link

                // Wrap text + top align
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Apply thin border to all cells
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray($borderStyle);

                // Center align serial number
                $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal('center');

                // Save Excel
                $fileName = $downloadFileName . '.xlsx';
                $filePath = storage_path(config('app.download_report_base_folder') . "/" . $fileName);

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                try {
                    $writer->save($filePath);
                } catch (\Exception $e) {
                    // Handle error
                }

                return $fileName;
            } else if ($reportType == 'pdf_desc') {
                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'default_font' => 'notosansdevanagari',
                    'fontDir' => array_merge($fontDirs, [
                        resource_path('fonts'), // 👈 now pointing to your new folder
                    ]),
                    'fontdata' => $fontData + [
                        'notosansdevanagari' => [
                            'R' => 'NotoSansDevanagari-Regular.ttf',
                            'B' => 'NotoSansDevanagari-Bold.ttf',
                        ],
                    ],
                ]);

                $html = '
<style>
table { border-collapse: collapse; font-size: 12px; width: 100%; }
th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
td img { border: 1px solid #ccc; border-radius: 4px; margin: 2px; max-width: 120px; max-height: 120px; }
ul { margin: 0; padding-left: 18px; }
tr { page-break-inside: avoid; }
</style>

<table>
    <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="25%">Headline</th>
            <th width="15%">Location</th>
            <th width="25%">Screenshots</th>
            <th width="30%">Descriptions</th>
        </tr>
    </thead>
    <tbody>
';

                foreach ($news as $reports) {
                    $serial = $reports['serial_number'];
                    $heading = $reports['heading'];
                    $source  = $reports['source'];
                    $date    = Carbon\Carbon::parse($reports['publish_at'])->format('d/m/Y');
                    $location = $reports['location_name'] ?? 'N/A';
                    $keypoints = $reports['calendar_date_description'] ?? [];

                    // Screenshots
                    $frontScreenshot = $reports['news_screenshot'] ?? null;

                    $screenshotHtml = '';

                    if ($frontScreenshot) {
                        $src = Helper::getImageUrl($frontScreenshot); // Use your helper to get full URL
                        // dd($src);
                        if ($src) {
                            $screenshotHtml .= '<img src="' . $src . '" style="max-width:48%; max-height:180px; margin-right:2%;" />';
                        }
                    }

                    if (!$screenshotHtml) $screenshotHtml = 'N/A';


                    if (is_string($keypoints)) {
                        // If it's a string, split it into lines or by | if that's your separator
                        $keypoints = preg_split('/\r\n|\r|\n|\|/', $keypoints);
                    }

                    // If it’s still not an array, make it an empty array
                    if (!is_array($keypoints)) {
                        $keypoints = [];
                    }

                    // Now you can safely loop
                    $pointsHtml = '';
                    if (!empty($keypoints)) {
                        $pointsHtml .= '<ul>';
                        foreach ($keypoints as $point) {
                            $point = trim($point);
                            if ($point) {
                                $pointsHtml .= '<li>' . $point . '</li>';
                            }
                        }
                        $pointsHtml .= '</ul>';
                    } else {
                        $pointsHtml = 'N/A';
                    }

                    $html .= '
        <tr>
            <td align="center">' . $serial . '</td>
            <td><b>' . $heading . '</b><br>(' . $source . ', ' . $date . ')</td>
            <td>' . $location . '</td>
            <td>' . $screenshotHtml . '</td>
            <td>' . $pointsHtml . '</td>
        </tr>
    ';
                }

                $html .= '</tbody></table>';

                // Write PDF
                $mpdf->WriteHTML($html);

                $fileName = $downloadFileName . '_keypoints_final.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
                return $fileName;
            } else 
           if ($reportType == "lovezihad") {
                try {
                    // ======================================
                    // 1. MEMORY & EXECUTION SETTINGS
                    // ======================================
                    ini_set('memory_limit', '2048M');             // use up to 2 GB RAM safely
                    ini_set('max_execution_time', 0);             // no time limit for this process
                    ini_set('pcre.backtrack_limit', '10000000');  // raise regex parsing limits
                    ini_set('pcre.recursion_limit', '10000000');

                    // create temp directory for mPDF cache (avoid RAM overuse)
                    $tempDir = storage_path('app/mpdf_temp');
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0777, true);
                    }

                    // ======================================
                    // 2. FONT CONFIGURATION
                    // ======================================
                    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];

                    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];

                    $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'tempDir' => $tempDir,
                        'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
                        'fontdata' => $fontData + [
                            'nirmala' => [
                                'R' => 'nirmala-ui.ttf',
                                'B' => 'nirmala-ui-bold.ttf',
                            ],
                            'dejavusans' => [
                                'R' => 'DejaVuSans.ttf',
                                'B' => 'DejaVuSans-Bold.ttf',
                            ],
                        ],
                        'default_font' => 'nirmala',
                        'margin_top' => 15,
                        'margin_bottom' => 15,
                        'margin_left' => 20,
                        'margin_right' => 20,
                    ]);

                    // Enable complex script and language support
                    $mpdf->autoScriptToLang = true;
                    $mpdf->autoLangToFont = true;

                    // ======================================
                    // 3. HEADER + INDEX
                    // ======================================
                    $indexHtml = View::make('report.mpdf.basereport_heading_index', $reportParams)->render();
                    $mpdf->WriteHTML($indexHtml);
                    $mpdf->AddPage();

                    // ======================================
                    // 4. STYLES
                    // ======================================
                    $style = <<<CSS
        body { font-family: nirmala; font-size: 17pt; color: #000; line-height: 1.7; }
        h2 { text-transform: uppercase; font-weight: bold; margin-bottom: 10px; font-size: 16pt; }
        h3 { text-decoration: underline; font-size: 13pt; margin: 15px 0 10px 0; }
        p, li { font-size: 12.5pt; margin-bottom: 5px; text-align: justify; }
        ol { margin: 5px 0 10px 0; padding-left: 18px; }
        a { color: #0000EE; text-decoration: none; }
        .meta { font-size: 11pt; margin-bottom: 10px; color: #444; }
        .image { text-align: center; margin: 10px 0; }
        .image img { max-width: 300px; border-radius: 8px; }
        .keypoints ol { margin-left: 20px; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 20px 0; }
        CSS;
                    $mpdf->WriteHTML("<style>$style</style>", \Mpdf\HTMLParserMode::HEADER_CSS);

                    // ======================================
                    // 5. MAIN CONTENT (CHUNKED)
                    // ======================================
                    $mpdf->WriteHTML("<h2>TOPIC - LOVE JIHAD</h2><h3>Table of Contents (Latest news on Top)</h3>");

                    // reverse to make serial number in increasing order if needed
                    $newsList = array_reverse($news);

                    $count = 0;
                    foreach ($newsList as $index => $report) {
                        $sno = $index + 1; // Serial number in increasing order
                        $chunkHtml = "<div class='news-item'>";
                        $chunkHtml .= "<p><b>{$sno}. " . e($report['heading']) . "</b></p>";
                        $chunkHtml .= "<p class='meta'>" . e($report['publish_at']) . " &nbsp;&nbsp; "
                            . e($report['language_name']) . " &nbsp;&nbsp;"
                            . e($report['location_name']) . " (" . e($report['location_state_name']) . ") &nbsp;&nbsp; "
                            . "<a href='" . e($report['link']) . "' target='_blank'>" . e($report['source']) . "</a></p>";

                        if (!empty($report['news_screenshot'])) {
                            $imageUrl = Helper::getImageUrl($report['news_screenshot']);
                            $chunkHtml .= "<div class='image'><img src='{$imageUrl}' alt='screenshot'></div>";
                        }

                        if (!empty($report['keypoint'])) {
                            $chunkHtml .= "<div class='keypoints'><ol>";
                            foreach ($report['keypoint'] as $point) {
                                $pointText = $point['keypoint'] ?? '';
                                if (!empty($pointText)) {
                                    $chunkHtml .= "<li>" . e($pointText) . "</li>";
                                }
                            }
                            $chunkHtml .= "</ol></div>";
                        }

                        $chunkHtml .= "<hr></div>";

                        // write each chunk separately to prevent PCRE overflow
                        $mpdf->WriteHTML($chunkHtml, \Mpdf\HTMLParserMode::HTML_BODY);

                        $count++;
                        if ($count % 200 == 0) {
                            $mpdf->AddPage(); // optional page split for huge reports
                        }
                    }

                    // ======================================
                    // 6. SAVE FILE
                    // ======================================
                    $fileName = 'LJReport.pdf';
                    $savePath = storage_path(config('app.download_report_base_folder') . '/' . $fileName);
                    $mpdf->Output($savePath, \Mpdf\Output\Destination::FILE);

                    return $fileName;
                } catch (\Throwable $e) {
                    \Log::error('PDF Generation Error', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
            } else {

                // prevent pcre crash on large reports
                @ini_set('pcre.backtrack_limit', '5000000');
                @ini_set('pcre.recursion_limit', '500000');

                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',

                    'margin_left'   => 20,
                    'margin_right'  => 20,
                    'margin_top'    => 20,
                    'margin_bottom' => 20,

                    // ✅ your actual TTF folder
                    'fontDir' => array_merge($fontDirs, [
                        base_path('public/fonts/Hind'),
                    ]),

                    // ✅ register Hind font
                    'fontdata' => $fontData + [
                        'hind' => [
                            'R' => 'Hind-Regular.ttf',
                            'B' => 'Hind-Bold.ttf',
                        ],
                    ],

                    // ✅ force default font to Hind (Hindi supported)
                    'default_font' => 'hind',

                    // ✅ helps mixed English + Hindi
                    'autoScriptToLang' => true,
                    'autoLangToFont'   => true,
                    'useSubstitutions' => true,
                ]);

                $mpdf->SetAutoPageBreak(true, 20);

                // 1) header + index
                $mpdf->WriteHTML(View::make('report.mpdf_allteam_header', $reportParams)->render());

                // 2) each news item as chunk (avoids pcre.backtrack_limit crash)
                foreach ($reportParams['keyNews'] as $reports) {
                    $mpdf->WriteHTML(
                        View::make('report.mpdf_allteam_item', array_merge($reportParams, [
                            'reports' => $reports
                        ]))->render()
                    );
                }

                // 3) footer
                $mpdf->WriteHTML(View::make('report.mpdf_allteam_footer', $reportParams)->render());

                $fileName = $downloadFileName . '.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');

                return $fileName;
            }
        }
    }

    /**
     * This takes symlinks into account.
     *
     * @param ZipArchive $zip
     * @param string     $path
     */
    private function addContent(\ZipArchive $zip, string $path)
    {
        /** @var SplFileInfo[] $files */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($path) + 1);

                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
    }

    public function fetchDownloadOptions(Request $request)
    {
        $mode = '';
        if ($request->mode) {
            $mode = $request->mode;
            // dd($request->mode);
        }

        $roleId = 0;
        $showReportForUser = "";

        if ($mode == "from_user") {
            $showReportForUser = $request->from_user;
        }

        // -----------------------------
        // MODE 1: USE DATE RANGE (AJAX COUNTS FOR FILTER PANEL)
        // -----------------------------
        if ($mode == "use_date") {

            $from = "";
            if ($request->from_date) {
                $fromdate = DateTime::createFromFormat('d-m-Y', $request->from_date);
                $from = $fromdate->format('Y-m-d');
            }

            $to = "";
            if ($request->to_date) {
                $todate = DateTime::createFromFormat('d-m-Y', $request->to_date);
                $to = $todate->format('Y-m-d');
            }

            if (empty($from)) $from = $to;
            if (empty($to)) $to = $from;

            // NEW: REPORT TYPE (News / Social Media Reports / etc.)
            $reportType = $request->show_report_type ?? null;

            // --------------------------------------------------------------------
            // Common Base Query Condition (Soft Delete + Active + Published Date)
            // --------------------------------------------------------------------
            $baseWhere = function (&$query) use ($from, $to, $reportType) {
                $query->whereNull('reports.deleted_at')
                    ->where('reports.is_deleted', 0)
                    ->where('reports.active', 1)
                    ->whereBetween('reports.publish_at', [$from, $to])
                    ->join('modules', function ($join) {
                        $join->on('reports.module_id', '=', 'modules.id');
                        $join->whereNull('modules.deleted_at');
                    })
                    ->join('chapters', function ($join) {
                        $join->on('reports.chapter_id', '=', 'chapters.id');
                        $join->whereNull('chapters.deleted_at');
                    });

                // 🔥 REPORT TYPE FILTER ADDED HERE
                if (!empty($reportType)) {
                    $query->where('reports.report_type', $reportType);
                }
            };

            // --------------------------------------------------------------------
            // BASE QUERIES
            // --------------------------------------------------------------------
            $date_reports            = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));
            $first_time_reports      = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));
            $followup_reports        = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));
            $calendar_date_reports   = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));
            $fir_documents_reports   = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));
            $total_reports           = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'));

            $users = Report::select(
                'users.id',
                'users.name',
                'users.email',
                DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
            )->join('users', 'users.id', '=', 'reports.user_id')
                ->whereNull('users.deleted_at');

            // Apply base conditions
            $baseWhere($date_reports);
            $baseWhere($first_time_reports);
            $baseWhere($followup_reports);
            $baseWhere($calendar_date_reports);
            $baseWhere($fir_documents_reports);
            $baseWhere($total_reports);
            $baseWhere($users);

            // Additional specific conditions
            $first_time_reports->where('reports.first_time', 1);
            $followup_reports->where('reports.has_followup', 1);
            $calendar_date_reports->where('reports.calendar_date', 1);
            $fir_documents_reports->join('report_documents', 'report_documents.report_id', '=', 'reports.id');

            // --------------------------------------------------------------------
            // APPLY DYNAMIC FILTERS (Team, Module, Chapter, Location, User)
            // --------------------------------------------------------------------

            // USER
            if (!empty($showReportForUser)) {
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->where('reports.user_id', $showReportForUser);
                }
            }

            // TEAM
            if ($request->team_id) {
                $teamIdArr = explode(",", $request->team_id);

                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.team_id', $teamIdArr);
                }
            }

            // MODULE
            if ($request->module_ids) {
                $moduleIdArr = explode(",", $request->module_ids);
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.module_id', $moduleIdArr);
                }
            }

            // CHAPTER
            if ($request->chapter_ids) {
                $chapterIdArr = explode(",", $request->chapter_ids);
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.chapter_id', $chapterIdArr);
                }
            }

            // LOCATION
            if ($request->location_ids) {
                $locationIdArr = explode(",", $request->location_ids);
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.location_id', $locationIdArr);
                }
            }

            // LOCATION STATE
            if ($request->location_state_ids) {
                $locationStateIdArr = explode(",", $request->location_state_ids);
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.location_state_id', $locationStateIdArr);
                }
            }

            // LANGUAGE
            if ($request->language_ids) {
                $languageIdArr = explode(",", $request->language_ids);
                foreach (
                    [
                        $date_reports,
                        $first_time_reports,
                        $followup_reports,
                        $calendar_date_reports,
                        $fir_documents_reports,
                        $total_reports,
                        $users
                    ] as $q
                ) {
                    $q->whereIn('reports.language_id', $languageIdArr);
                }
            }

            // --------------------------------------------------------------------
            // EXECUTE
            // --------------------------------------------------------------------
            $data['date_reports']          = $date_reports->get();
            $data['first_time_reports']    = $first_time_reports->get();
            $data['followup_reports']      = $followup_reports->get();
            $data['calendar_date_reports'] = $calendar_date_reports->get();
            $data['fir_documents_reports'] = $fir_documents_reports->get();

            $data['users'] = $users->groupBy('users.id')
                ->orderBy('users.id')
                ->get();

            $data['total_reports'] = $total_reports->get();

            return response()->json($data);
        }


        // -----------------------------
        // MODE 2+: EVERYTHING ELSE (modules, chapters, locations, etc.)
        // -----------------------------

        $from = "";
        if ($request->from_date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $request->from_date);
            $from = $fromdate->format('Y-m-d');
        }

        $to = "";
        if ($request->to_date) {
            $todate = DateTime::createFromFormat('d-m-Y', $request->to_date);
            $to = $todate->format('Y-m-d');
        }

        if (empty($from)) {
            $from = $to;
        }

        if (empty($to)) {
            $to = $from;
        }

        // ------------- RAW SQL PART (modules + chapters) ------------- //
        if ($request->team_id) {
            if (!empty($showReportForUser)) {
                $condition = " reports.user_id = " . (int)$showReportForUser . " AND ";
            } else {
                $condition = " 1=1 AND ";
            }

            // ✅ CHANGED: support multiple team IDs safely
            $teamIdArr = explode(',', $request->team_id);
            $teamIdArr = array_filter($teamIdArr, function ($id) {
                return trim($id) !== '';
            });
            $teamIdArr = array_map('intval', $teamIdArr);
            $teamIdStr = implode(',', $teamIdArr);
            if ($teamIdStr === '') {
                $teamIdStr = '0';
            }

            // modules under this/these team(s) + report counts
            $resultsForTeamId = DB::connection('setfacts')->select("
            SELECT 
                modules.id, 
                modules.name, 
                modules.slug AS module_slug, 
                teams.slug AS team_slug, 
                COUNT(DISTINCT reports.id) AS total_reports, 
                MAX(reports.publish_at) AS latest_published_at 
            FROM modules
            LEFT JOIN reports 
                ON reports.module_id = modules.id 
               AND ISNULL(reports.deleted_at) 
               AND reports.active = 1 
               AND $condition 1=1
            LEFT JOIN chapters 
                ON reports.chapter_id = chapters.id 
               AND ISNULL(chapters.deleted_at) 
               AND chapters.active = 1
            INNER JOIN teams 
                ON teams.id = modules.team_id
            WHERE 
                modules.active = 1 
                AND ISNULL(modules.deleted_at) 
                AND modules.team_id IN ($teamIdStr)
            GROUP BY modules.id
        ");

            // chapters under those modules + report counts
            $moduleIdsForTeam = [];
            foreach ($resultsForTeamId as $row) {
                $moduleIdsForTeam[] = (int)$row->id;
            }
            $moduleIdsForTeamStr = empty($moduleIdsForTeam) ? '0' : implode(',', $moduleIdsForTeam);

            $resultsForModuleId = DB::connection('setfacts')->select("
            SELECT 
                chapters.id, 
                chapters.name, 
                modules.slug AS module_slug, 
                chapters.slug AS chapter_slug, 
                teams.slug AS team_slug, 
                COUNT(DISTINCT reports.id) AS total_reports 
            FROM chapters
            LEFT JOIN reports 
                ON reports.chapter_id = chapters.id 
               AND ISNULL(reports.deleted_at) 
               AND reports.active = 1 
               AND $condition 1=1
            INNER JOIN modules 
                ON reports.module_id = modules.id 
               AND ISNULL(modules.deleted_at) 
               AND modules.active = 1
            INNER JOIN teams 
                ON teams.id = modules.team_id
            WHERE 
                chapters.active = 1 
                AND ISNULL(chapters.deleted_at) 
                AND chapters.module_id IN ($moduleIdsForTeamStr)
            GROUP BY chapters.id
        ");
        }

        if ($request->module_ids) {
            if (!empty($showReportForUser)) {
                $condition = " reports.user_id = " . (int)$showReportForUser . " AND ";
            } else {
                $condition = " 1=1 AND ";
            }

            $resultsForModuleId = DB::connection('setfacts')->select("
            SELECT 
                chapters.id, 
                chapters.name, 
                modules.slug AS module_slug, 
                chapters.slug AS chapter_slug, 
                teams.slug AS team_slug, 
                COUNT(DISTINCT reports.id) AS total_reports, 
                MAX(reports.publish_at) AS latest_published_at 
            FROM chapters
            LEFT JOIN reports 
                ON reports.chapter_id = chapters.id 
               AND ISNULL(reports.deleted_at) 
               AND reports.active = 1 
               AND $condition 1=1
            INNER JOIN modules 
                ON reports.module_id = modules.id 
               AND ISNULL(modules.deleted_at) 
               AND modules.active = 1
            INNER JOIN teams 
                ON teams.id = modules.team_id
            WHERE 
                chapters.active = 1 
                AND ISNULL(chapters.deleted_at) 
                AND chapters.module_id IN (" . $request->module_ids . ")
            GROUP BY chapters.id
            ORDER BY teams.id ASC, modules.id ASC, chapters.id ASC
        ");
        }

        // -------- aggregate total_reports + facets (outside use_date) -------- //

        $total_reports = Report::select(
            DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
        )
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        $locations = Report::select(
            'locations.id',
            'locations.name',
            DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
        )
            ->join('locations', 'locations.id', 'reports.location_id')
            ->whereNull('locations.deleted_at')
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        $locationStates = Report::select(
            'location_states.id',
            'location_states.name',
            DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
        )
            ->join('location_states', 'location_states.id', 'reports.location_state_id')
            ->whereNull('location_states.deleted_at')
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        $languages = Report::select(
            'languages.id',
            'languages.name',
            DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
        )
            ->join('languages', 'languages.id', 'reports.language_id')
            ->whereNull('languages.deleted_at')
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        $first_time_reports = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'))
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.first_time', 1)
            ->where('reports.active', 1);

        $followup_reports = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'))
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.has_followup', 1)
            ->where('reports.active', 1);

        $calendar_date_reports = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'))
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.calendar_date', 1)
            ->where('reports.active', 1);

        $fir_documents_reports = Report::select(DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports'))
            ->join('report_documents', 'report_documents.report_id', 'reports.id')
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        $users = Report::select(
            'users.id',
            'users.name',
            'users.email',
            DB::connection('setfacts')->raw('COUNT(DISTINCT reports.id) as total_reports')
        )
            ->join('users', 'users.id', 'reports.user_id')
            ->whereNull('users.deleted_at')
            ->whereNull('reports.deleted_at')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1);

        if (!empty($showReportForUser)) {
            $total_reports = $total_reports->where('reports.user_id', $showReportForUser);
            $locations = $locations->where('reports.user_id', $showReportForUser);
            $locationStates = $locationStates->where('reports.user_id', $showReportForUser);
            $languages = $languages->where('reports.user_id', $showReportForUser);
            $first_time_reports = $first_time_reports->where('reports.user_id', $showReportForUser);
            $followup_reports = $followup_reports->where('reports.user_id', $showReportForUser);
            $calendar_date_reports = $calendar_date_reports->where('reports.user_id', $showReportForUser);
            $fir_documents_reports = $fir_documents_reports->where('reports.user_id', $showReportForUser);
        }

        if ($request->team_id) {
            $teamIdArr = explode(",", $request->team_id);

            $total_reports = $total_reports->whereIn('reports.team_id', $teamIdArr);
            $locations = $locations->whereIn('reports.team_id', $teamIdArr);
            $locationStates = $locationStates->whereIn('reports.team_id', $teamIdArr);
            $languages = $languages->whereIn('reports.team_id', $teamIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.team_id', $teamIdArr);
            $followup_reports = $followup_reports->whereIn('reports.team_id', $teamIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.team_id', $teamIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.team_id', $teamIdArr);
            $users = $users->whereIn('reports.team_id', $teamIdArr);
        }

        if ($request->module_ids) {
            $moduleIdArr = explode(",", $request->module_ids);

            $total_reports = $total_reports->whereIn('reports.module_id', $moduleIdArr);
            $locations = $locations->whereIn('reports.module_id', $moduleIdArr);
            $locationStates = $locationStates->whereIn('reports.module_id', $moduleIdArr);
            $languages = $languages->whereIn('reports.module_id', $moduleIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.module_id', $moduleIdArr);
            $followup_reports = $followup_reports->whereIn('reports.module_id', $moduleIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.module_id', $moduleIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.module_id', $moduleIdArr);
            $users = $users->whereIn('reports.module_id', $moduleIdArr);
        }

        if ($request->chapter_ids) {
            $chapterIdArr = explode(",", $request->chapter_ids);

            $total_reports = $total_reports->whereIn('reports.chapter_id', $chapterIdArr);
            $locations = $locations->whereIn('reports.chapter_id', $chapterIdArr);
            $locationStates = $locationStates->whereIn('reports.chapter_id', $chapterIdArr);
            $languages = $languages->whereIn('reports.chapter_id', $chapterIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.chapter_id', $chapterIdArr);
            $followup_reports = $followup_reports->whereIn('reports.chapter_id', $chapterIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.chapter_id', $chapterIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.chapter_id', $chapterIdArr);
            $users = $users->whereIn('reports.chapter_id', $chapterIdArr);
        }

        if ($request->location_ids) {
            $locationIdArr = explode(",", $request->location_ids);

            $total_reports = $total_reports->whereIn('reports.location_id', $locationIdArr);
            $locationStates = $locationStates->whereIn('reports.location_id', $locationIdArr);
            $languages = $languages->whereIn('reports.location_id', $locationIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.location_id', $locationIdArr);
            $followup_reports = $followup_reports->whereIn('reports.location_id', $locationIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.location_id', $locationIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.location_id', $locationIdArr);
            $users = $users->whereIn('reports.location_id', $locationIdArr);
        }

        if ($request->location_state_ids) {
            $locationStateIdArr = explode(",", $request->location_state_ids);

            $total_reports = $total_reports->whereIn('reports.location_state_id', $locationStateIdArr);
            $languages = $languages->whereIn('reports.location_state_id', $locationStateIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.location_state_id', $locationStateIdArr);
            $followup_reports = $followup_reports->whereIn('reports.location_state_id', $locationStateIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.location_state_id', $locationStateIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.location_state_id', $locationStateIdArr);
            $users = $users->whereIn('reports.location_state_id', $locationStateIdArr);
        }

        if ($request->language_ids) {
            $languageIdArr = explode(",", $request->language_ids);

            $total_reports = $total_reports->whereIn('reports.language_id', $languageIdArr);
            $languages = $languages->whereIn('reports.language_id', $languageIdArr);
            $first_time_reports = $first_time_reports->whereIn('reports.language_id', $languageIdArr);
            $followup_reports = $followup_reports->whereIn('reports.language_id', $languageIdArr);
            $calendar_date_reports = $calendar_date_reports->whereIn('reports.language_id', $languageIdArr);
            $fir_documents_reports = $fir_documents_reports->whereIn('reports.language_id', $languageIdArr);
            $users = $users->whereIn('reports.language_id', $languageIdArr);
        }

        if (!empty($from) && !empty($to)) {
            $total_reports = $total_reports->whereBetween('reports.publish_at', [$from, $to]);
            $languages = $languages->whereBetween('reports.publish_at', [$from, $to]);
            $locations = $locations->whereBetween('reports.publish_at', [$from, $to]);
            $locationStates = $locationStates->whereBetween('reports.publish_at', [$from, $to]);
            $first_time_reports = $first_time_reports->whereBetween('reports.publish_at', [$from, $to]);
            $followup_reports = $followup_reports->whereBetween('reports.publish_at', [$from, $to]);
            $calendar_date_reports = $calendar_date_reports->whereBetween('reports.publish_at', [$from, $to]);
            $fir_documents_reports = $fir_documents_reports->whereBetween('reports.publish_at', [$from, $to]);
            $users = $users->whereBetween('reports.publish_at', [$from, $to]);
        }

        if ($mode != "location") {
            $locations = $locations->groupBy('locations.id')
                ->orderBy('locations.name')
                ->orderBy('locations.parent')
                ->get();
        }

        if ($mode != "location_state") {
            $locationStates = $locationStates->groupBy('location_states.id')
                ->orderBy('location_states.name')
                ->get();
        }

        if ($mode != "language") {
            $languages = $languages->groupBy('languages.id')
                ->orderBy('languages.id')
                ->get();
        }

        if ($mode != "from_user") {
            $users = $users->groupBy('users.id')
                ->orderBy('users.id')
                ->get();
        }

        $first_time_reports = $first_time_reports->get();
        $followup_reports = $followup_reports->get();
        $calendar_date_reports = $calendar_date_reports->get();
        $fir_documents_reports = $fir_documents_reports->get();

        // For team -> modules + chapters (Category)
        if (($mode == 'modules') && ($request->team_id)) {
            $data['modules'] = $resultsForTeamId ?? [];
            $data['chapters'] = $resultsForModuleId ?? [];
        }

        // For module -> chapters (Sub-category)
        if (($mode == 'chapters') && ($request->module_ids)) {
            $data['chapters'] = $resultsForModuleId ?? [];
        }

        if ($mode != "location") {
            $data['locations'] = $locations;
        }

        if ($mode != "location_state") {
            $data['location_state'] = $locationStates;
        }

        if ($mode != "language") {
            $data['languages'] = $languages;
        }

        if ($mode != "from_user") {
            $data['users'] = $users;
        }

        $data['first_time_reports'] = $first_time_reports;
        $data['followup_reports'] = $followup_reports;
        $data['calendar_date_reports'] = $calendar_date_reports;
        $data['fir_documents_reports'] = $fir_documents_reports;

        $total_reports = $total_reports->get();
        $data['total_reports'] = $total_reports;

        // Document tags logic unchanged
        if ($mode == "modules") {
            $team_id = $request->team_id;

            $parentId = "";
            $parentIdDatas = TeamTag::select('id')
                ->whereIn('team_id', explode(',', $team_id))
                ->where('parent', 0)
                ->where('tag', 'Document')
                ->get();

            foreach ($parentIdDatas as $parentIdData) {
                $parentId = $parentIdData->id;
            }

            if (!empty($parentId)) {
                $documentTags = TeamTag::select('id', 'tag')
                    ->whereIn('team_id', explode(',', $team_id))
                    ->where('parent', $parentId)
                    ->where('tag', '!=', 'NA')
                    ->get();

                $data['documentTags'] = $documentTags;
            }
        }

        return response()->json($data);
    }



    public function fetchTeamTags(Request $request)
    {
        $data = array();
        $team_id = '';
        if ($request->team_id) {
            $team_id = $request->team_id;
        }

        if (!empty($team_id)) {

            $parentId = "";
            $parentIdDatas = TeamTag::select('id')
                ->where('team_id', $team_id)
                ->where('parent', 0)
                ->where('tag', 'Document')
                ->get();

            foreach ($parentIdDatas as $parentIdData) {
                $parentId = $parentIdData->id;
            }

            if (!empty($parentId)) {
                $documentTags = TeamTag::select('id', 'tag')
                    ->where('team_id', $team_id)
                    ->where('parent', $parentId)
                    ->where('tag', '!=', 'NA')
                    ->get();

                $data['documentTags'] = $documentTags;
            }
        }

        return response()->json($data);
    }

    public function getFirDocumentReportIds()
    {
        $firDocumentReportIdArr = array();
        $firDocumentReportIds = ReportDocument::select(DB::connection('setfacts')->Raw('distinct(report_id) as report_id'))->get();
        foreach ($firDocumentReportIds as $firDocumentReportId) {
            $firDocumentReportIdArr[] = $firDocumentReportId->report_id;
        }

        return $firDocumentReportIdArr;
    }

    ///////////////////////////
    // From Report Controller
    //////////////////////////
    public function pdf(Request $request)
    {
        // dump($request->all());
        // center_website
        if (!empty($request->center_website)) {
            // $this->loadCenterWebsiteFromRequest($request->center_website);
            $websiteIdsShowOnly = implode(",", $request->center_website);
            Session::put('website_id_databse_search', $websiteIdsShowOnly);
        }

        $websiteIds = $this->getWebsiteIdsFromSessionForDatabaseSearch();

        // data loaded from View Composer
        $mode = "download";

        $faqs = Faq::where('active', 1)->orderby('order', 'DESC')->get();

        $documents = Document::where('active', 1)->orderby('id', 'DESC')->get();

        $defaultTeam = DB::connection('setfacts')->select(DB::raw("SELECT teams.id, teams.name, teams.slug, COUNT(reports.id) AS total_reports FROM `teams` 
        INNER JOIN `reports` ON `reports`.`team_id` = `teams`.`id` where teams.active=1 and teams.website_id in (" . $websiteIds . ") and teams.is_public=1 GROUP BY `teams`.`id`"));

        // $defaultTeamId = $defaultTeam[0]->id;        
        // dump("defaultTeamId ".$defaultTeamId);

        // $categoriesData = DB::connection('setfacts')->select(
        //     DB::connection('setfacts')->Raw(
        //         "SELECT modules.id, modules.name, COUNT(reports.id) AS total_reports FROM `modules` 
        //     left JOIN `reports` ON `reports`.`module_id` = `modules`.`id` WHERE modules.active=1  and modules.website_id in (".$websiteIds.") and `modules`.`team_id` = :team_id GROUP BY `modules`.`id`"
        //     ),
        //     array('team_id' =>  $defaultTeamId)
        // );  

        // $defaultModuleId = $categoriesData[0]->id;

        // dump($defaultModuleId);

        // $subCategoriesData = DB::connection('setfacts')->select(DB::connection('setfacts')->Raw(
        //     "SELECT chapters.id, chapters.name, COUNT(reports.id) AS total_reports FROM `chapters` 
        //     left JOIN `reports` ON `reports`.`chapter_id` = `chapters`.`id` 
        //     WHERE chapters.active = 1 and `chapters`.`module_id` = :module_id GROUP BY `chapters`.`id`"), 
        // array('module_id' => $defaultModuleId,));

        $defaultTeamId = 0;
        $defaultModuleId = 0;
        $categoriesData = array();
        $subCategoriesData = array();

        return view('report.pdf', compact('mode', 'faqs', 'documents', 'defaultTeamId', 'defaultModuleId', 'categoriesData', 'subCategoriesData'));
    }

    public function fetchModule(Request $request)
    {
        if (!empty($request->team_id)) {

            $results = DB::connection('setfacts')->select(
                DB::connection('setfacts')->Raw(
                    "SELECT modules.id, modules.name, COUNT(reports.id) AS total_reports FROM `modules` 
                left JOIN `reports` ON `reports`.`module_id` = `modules`.`id` WHERE modules.active=1  and `modules`.`team_id` = :team_id GROUP BY `modules`.`id`"
                ),
                array('team_id' =>  $request->team_id)
            );
        } else {
            $results = DB::connection('setfacts')->select(
                DB::connection('setfacts')->Raw(
                    "SELECT modules.id, modules.name, COUNT(reports.id) AS total_reports FROM `modules` 
                left JOIN `reports` ON `reports`.`module_id` = `modules`.`id` where modules.active=1 GROUP BY `modules`.`id`"
                )
            );
        }


        $data['modules'] = $results;
        return response()->json($data);
    }

    public function fetchChapter(Request $request)
    {
        $results = DB::connection('setfacts')->select(DB::connection('setfacts')->Raw("SELECT chapters.id, chapters.name, COUNT(reports.id) AS total_reports FROM `chapters` left JOIN `reports` ON `reports`.`chapter_id` = `chapters`.`id` WHERE chapters.active = 1 and `chapters`.`module_id` = :module_id GROUP BY `chapters`.`id`"), array(
            'module_id' => $request->module_id,
        ));

        $data['chapters'] = $results;

        return response()->json($data);
    }

    public function fetchState(Request $request)
    {
        $data['states'] = LocationState::where("location_id", $request->location_id)->wherenull('deleted_at')->get(["name", "id"]);
        return response()->json($data);
    }

    public function fetchTag(Request $request)
    {
        $searchTerm = $request->term['term'];
        $data['tags'] = ReportTag::select('id', 'tag')->where('tag', 'like', $searchTerm . '%')->orderBy('tag')->groupby('tag')->get()->toArray();

        return response()->json($data);
    }

    public function fetchSource(Request $request)
    {
        $searchTerm = $request->term['term'];
        $data['sources'] = ReportSource::select('id', 'source')->where('source', 'like', $searchTerm . '%')
            ->orderBy('source')->groupby('source')->get()->toArray();
        return response()->json($data);
    }

    function str_random($length = 16)
    {
        return Str::random($length);
    }

    public function createSavedReport(Request $request, $slug)
    {
        // dd(session()->get('website_id'));

        $user_id = 0;
        // if (auth()) {
        //     $user = auth()->user();
        //     if($user){
        //         $user_id = $user->id;
        //     }
        // }
        if (!empty($request->url_params)) {

            $params = $request->url_params;
            // dump($params);
            //find if params exist, return matching row name
            $checkExist = SavedReport::where('params', $params)->get()->count();
            if ($checkExist > 0) {
                $savedReportData = SavedReport::where('params', $params)->get();
                foreach ($savedReportData as $data) {
                    $name = $data->name;
                    return response()->json(array("status" => "success", "namedReport" => $name));
                }
            } else {

                $name = strtoupper($this->str_random(10));
                $savedReportObj = new SavedReport();
                $savedReportObj->name = $name;
                $savedReportObj->params = $params;
                $savedReportObj->user_id = $user_id;
                $savedReportObj->website_id = session()->get('website_id');
                $savedReportObj->save();

                return response()->json(array("status" => "success", "namedReport" => $name));
            }
        }
        return response()->json(array("status" => "error"));
    }

    public function fetchModulePublic(Request $request)
    {
        $results = DB::connection('setfacts')->select(
            DB::connection('setfacts')->Raw(
                "SELECT modules.id, modules.name, COUNT(reports.id) AS total_reports FROM `modules` 
            left JOIN `reports` ON `reports`.`module_id` = `modules`.`id` WHERE modules.active=1 and `modules`.`team_id` = :team_id GROUP BY `modules`.`id`"
            ),
            array('team_id' =>  $request->team_id)
        );

        $data['modules'] = $results;
        return response()->json($data);
    }

    public function fetchChapterPublic(Request $request)
    {

        $results = DB::connection('setfacts')->select(DB::connection('setfacts')->Raw("SELECT chapters.id, chapters.name, COUNT(reports.id) AS total_reports FROM `chapters` left JOIN `reports` ON `reports`.`chapter_id` = `chapters`.`id` WHERE chapters.active = 1 and  `chapters`.`module_id` = :module_id GROUP BY `chapters`.`id`"), array(
            'module_id' => $request->module_id,
        ));


        $data['chapters'] = $results;

        return response()->json($data);
    }



    // table_report
    // wordview, googledoc, inline pdf
    public function downloadTableReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode)
    {
        // echo "<br>Inside downloadTableReport() From ".$from." TO ".$to;
        // exit();

        $showReportForUser = "";
        // if ($roleId == config('app.basic_user_role_id')) {
        //     $user = auth()->user();
        //     $showReportForUser = $user->id;
        // } else {
        //     if ($request->from_user) {
        //         $showReportForUser = $request->from_user;
        //     }
        // }

        $tName = $request->team_name;

        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;
        $document_report_tags = $request->document_report_tags;

        $report_data_type = $request->report_data_type;

        $table_report_state_order = 'state_asc';
        if ($request->table_report_state_order) {
            $table_report_state_order = $request->table_report_state_order;
        }
        $table_report_state_min_cases = 5;
        if ($request->table_report_state_min_cases) {
            $table_report_state_min_cases = $request->table_report_state_min_cases;
        }

        $reportTitle = "Team 3 Table Report";

        if (!empty($from && $to)) {
            $from = $from . " 00:00:00";
            $to = $to . " 23:59:59";
        }
        $dateRangeAttribute = "reports.created_at";

        // echo "<br>From ".$from." TO ".$to;
        // echo "<BR>dateRangeAttribute ". $dateRangeAttribute;
        // echo "<BR>reportTitle ". $reportTitle;        
        // exit();


        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $teamName = Team::select('id', 'name')->where('id', $tName)->where('active', 1)->first();
        // dd($teamName);

        $categories = Module::select('modules.id', 'modules.name')->where('team_id', $tName)->wherenull('deleted_at')->get();
        $categoriesData = array();
        $categories_ids = array();
        foreach ($categories as $key => $category) {
            $categoriesData[$category['id']] = $category['name'];
            $categories_ids[] = $category['id'];
        }
        // dd($categoriesData);

        $subCategories = Chapter::select('chapters.id', 'chapters.name')->wherein('chapters.module_id', $categories_ids)->wherenull('deleted_at')->get();
        $subCategoriesData = array();
        foreach ($subCategories as $key => $subCategory) {
            $subCategoriesData[$subCategory['id']] = $subCategory['name'];
        }
        // dd($subCategoriesData);        

        // all states loop
        $locations = Location::select('locations.id', 'locations.name')->wherenull('locations.deleted_at');
        $locations = $locations->orderby('locations.id', 'ASC')->get();
        $locationsData = array();
        foreach ($locations as $key => $location) {
            $locationsData[$location['id']] = $location['name'];
        }
        // dd($locationsData);        


        // get new tags data for the Issue
        $teamData = Team::select('id', 'name')->get();
        $teamTagsData = array();
        foreach ($teamData as $key => $team_data) {
            $team_id = $team_data->id;
            $teamTagData = TeamTag::select('id', 'tag')->where('team_id', $team_id)->where('parent', 0)->get();
            $tagsData = array();
            foreach ($teamTagData as $key => $teamTag_Data) {
                $parentId = $teamTag_Data->id;
                $teamTagsForParent = TeamTag::select('id', 'tag')->where('team_id', $team_id)->where('parent', $parentId)->orderby('tag', 'asc')->get()->toArray();

                $contentArr['label'] = $teamTag_Data->tag;
                $contentArr['label_id'] = $parentId;
                $contentArr['tags'] = $teamTagsForParent;

                $tagsData[$parentId] = $contentArr;
            }
            $teamTagsData[$team_id] = $tagsData;
        }
        // dd($teamTagsData);        


        $report = Report::select(
            'reports.location_id',
            'reports.module_id',
            DB::raw('COUNT(reports.id) as totalReports')
        )
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1)
            ->where('reports.location_id', '>', 0);

        if (!empty($from && $to)) {
            $report = $report->whereBetween($dateRangeAttribute, [$from, $to]);
        }

        if (!empty($tName)) {
            $report = $report->where('reports.team_id', $tName);
        }


        $report =  $report->groupby('reports.location_id');
        $report =  $report->groupby('reports.module_id');

        // $report = $report->toSql();
        // dd($report);

        $report = $report->get();
        $repcount = count($report->toArray());
        // dd($repcount);

        if ($mode == "getcount") {
            return $repcount;
        }

        $mytime = Carbon\Carbon::now();

        $downloadFileName = date('jSF') . "Table_Report";

        $viewLayout = "layouts.admin";
        // if ($roleId == 0) {
        //     // $viewLayout = "layouts.app";
        //     $viewLayout = "layouts.webview";
        // }

        // Report Table 1
        $reportsData = array();
        foreach ($report as $key => $reportData) {
            $reportDataArr = array();
            $reportDataArr['category'] = $reportData['module_id'];
            $reportDataArr['totalReports'] = $reportData['totalReports'];
            $reportsData[$reportData['location_id']][] = $reportDataArr;
            //dd($dataArr);            
        }
        // dd($reportsData);


        ///////////////////////////////////
        // Report Table 2
        ///////////////////////////////////

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }
        // dd($module_ids_arr);

        $reportsTwoData = array();
        $chapters = array();
        $chaptersData = array();
        if (count($module_ids_arr) > 0) {

            $chapters = Chapter::select('chapters.id', 'chapters.name')->wherein('module_id', $module_ids_arr)->wherenull('deleted_at')->get();
            foreach ($chapters as $key => $chapter) {
                $chaptersData[$chapter['id']] = $chapter['name'];
            }
            // dd($categoriesData);            

            foreach ($module_ids_arr as $module_id) {

                $reportTwo = Report::select(
                    'reports.module_id',
                    'reports.location_id',
                    'reports.chapter_id',
                    DB::raw('COUNT(reports.id) as totalReports')
                )
                    ->where('reports.is_deleted', 0)
                    ->where('reports.active', 1)
                    ->where('reports.location_id', '>', 0);

                if (!empty($from && $to)) {
                    $reportTwo = $reportTwo->whereBetween($dateRangeAttribute, [$from, $to]);
                }

                if (!empty($tName)) {
                    $reportTwo = $reportTwo->where('reports.team_id', $tName);
                }

                $reportTwo = $reportTwo->where('reports.module_id',  $module_id);

                $reportTwo = $reportTwo->groupby('reports.module_id');
                $reportTwo = $reportTwo->groupby('reports.location_id');
                $reportTwo = $reportTwo->groupby('reports.chapter_id');

                // $reportTwo = $reportTwo->toSql();
                // dd($reportTwo);

                $reportTwo = $reportTwo->get();
                $reportDataLocArr = array();
                foreach ($reportTwo as $key => $reportData) {
                    $reportDataArr = array();
                    $reportDataArr['sub_category'] = $reportData['chapter_id'];
                    $reportDataArr['totalReports'] = $reportData['totalReports'];


                    $reportDataLocArr[$reportData['location_id']][] = $reportDataArr;
                    //dd($dataArr);            
                }

                $reportsTwoData[$module_id] = $reportDataLocArr;
            }
            // dump($reportsTwoData);      
        }


        ///////////////////////////////////
        // Report Table 3
        ///////////////////////////////////

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }
        // dd($module_ids_arr);

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }
        // dd($chapter_ids_arr);        

        $reportsThreeData = array();
        $chaptersDataThree = array();
        $chaptersThree = array();
        $tagsData = array();
        if (count($chapter_ids_arr) > 0) {

            $chaptersThree = Chapter::select('chapters.id', 'chapters.name')->wherein('chapters.id', $chapter_ids_arr)->wherenull('deleted_at');

            // $chaptersThree = $chaptersThree->toSql();                    
            // dd($chaptersThree);                    

            $chaptersThree = $chaptersThree->get();
            foreach ($chaptersThree as $key => $chapter) {
                $chaptersDataThree[$chapter['id']] = $chapter['name'];
            }
            // dd($chaptersDataThree);            

            foreach ($chapter_ids_arr as $chapter_id) {

                $reportThree = Report::select(
                    'reports.module_id',
                    'reports.chapter_id',
                    'reports.location_id',
                    DB::raw('COUNT(reports.id) as totalReports'),
                    DB::raw('GROUP_CONCAT(reports.id) as reportIds'),
                )
                    ->join('report_team_tags', 'report_team_tags.report_id', 'reports.id')
                    ->where('reports.is_deleted', 0)
                    ->where('reports.active', 1)
                    ->where('reports.location_id', '>', 0);

                if (!empty($from && $to)) {
                    $reportThree = $reportThree->whereBetween($dateRangeAttribute, [$from, $to]);
                }

                if (!empty($tName)) {
                    $reportThree = $reportThree->where('reports.team_id', $tName);
                }

                $reportThree = $reportThree->where('reports.chapter_id',  $chapter_id);

                $reportThree = $reportThree->groupby('reports.chapter_id');
                $reportThree = $reportThree->groupby('reports.location_id');

                // $reportThree = $reportThree->toSql();
                // dd($reportThree);

                $reportThree = $reportThree->get();
                $reportDataLocArr = array();
                foreach ($reportThree as $key => $reportData) {
                    $reportDataArr = array();
                    $reportDataArr['category'] = $reportData['module_id'];
                    $reportDataArr['sub_category'] = $reportData['chapter_id'];
                    $reportDataArr['totalReports'] = $reportData['totalReports'];
                    $reportDataArr['reportIds'] = $reportData['reportIds'];


                    // selected report team tags
                    $reportIdArr = explode(',', $reportData['reportIds']);
                    $selectedReportTeamTag = ReportTeamTag::select('tag_id')
                        ->wherein('report_id', $reportIdArr)
                        ->orderby('id', 'ASC')
                        ->get();
                    // dd($selectedReportTeamTag);
                    $selectedReportTeamTag = array_column($selectedReportTeamTag->toArray(), 'tag_id');
                    // dd($selectedReportTeamTag);
                    $reportDataArr['selectedReportTeamTag'] = $selectedReportTeamTag;


                    $reportDataLocArr[$reportData['location_id']][] = $reportDataArr;
                    // dd($reportDataLocArr);            
                }

                $reportsThreeData[$chapter_id] = $reportDataLocArr;
            }
            // dump($reportsThreeData);      
        }

        // dump($teamTagsData);

        $reportParams = [
            'to' => $to,
            'from' => $from,
            'report_data_type' => $report_data_type,

            'report' => $report,
            'reportsData' => $reportsData,
            'repcount' => $repcount,

            // modules
            'categories' => $categories,
            'categoriesData' => $categoriesData,
            'locations' => $locations,
            'locationsData' => $locationsData,

            // chapters
            'module_ids_arr' => $module_ids_arr,
            'reportsTwoData' => $reportsTwoData,
            'subcategories' => $chapters,
            'subcategoriesData' => $chaptersData,

            // chapter-tags
            'chapter_ids_arr' => $chapter_ids_arr,
            'reportsThreeData' => $reportsThreeData,
            'subcategoriesThree' => $chaptersThree,
            'subcategoriesDataThree' => $chaptersDataThree,
            'teamTagsData' => $teamTagsData,


            'reportType' => $reportType,
            'teamName' => $teamName,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading,
            'roleId' => $roleId,
            'moduleNameArr' => $moduleNameArr,
            'viewLayout' => $viewLayout,
        ];

        // dd($reportParams);

        if ($mode == "view") {

            $view_content_web =  view('report.webview_table_report',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else {
        }
    }

    // work_report
    // wordview_work_report, googledoc_work_report, mpdfview_work_report
    public function downloadWorkReport($request, $from, $to, $allLocationIds, $mode, $roleId)
    {
        // dd($request->all());

        $showReportForUser = "";
        // if ($roleId == config('app.basic_user_role_id')) {
        //     $user = auth()->user();
        //     $showReportForUser = $user->id;
        // } else {
        //     if ($request->from_user) {
        //         $showReportForUser = $request->from_user;
        //     }
        // }

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }
        // dd($module_ids_arr);

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }
        // dd($chapter_ids_arr);

        $tName = $request->team_name;

        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;

        $report_data_type = $request->report_data_type;
        // $language_id = $request->language_id;

        $language_id = array();
        if ($request->language_id) {
            $language_id = $request->language_id;
        }


        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }

        $fir_documents = 0;
        if ($request->fir_documents) {
            $fir_documents = $request->fir_documents;
        }

        $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
        // $moduleName = Module::select('name')->where('id', $module)->where('active', 1)->first();
        // $chapterName = Chapter::select('name')->where('id', $chapter_id)->where('active', 1)->first();
        $moduleName = "";
        $chapterName = "";

        $allTeams = Team::select('id', 'name')->where('active', 1)->get();

        $report_data_type = "work_report";
        if (!empty($request->report_data_type)) {
            $report_data_type = $request->report_data_type;
        }

        $reportTitle = "Team 3 Work Report";

        if (!empty($from && $to)) {
            $from = $from . " 00:00:00";
            $to = $to . " 23:59:59";
        }

        $dateRangeAttribute = "reports.created_at";

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $workReport = Team::select(
            'teams.id AS team_id',
            'teams.name as team_name',
            'users.id AS user_id',
            'users.name as user_name',
            DB::Raw('COUNT(reports.id) AS total_reports_by_team_user'),
            DB::Raw('SUM(IF(DATE(reports.`created_at`) = reports.`publish_at`, 1, 0)) AS daily_report_count'),
            DB::Raw('SUM(IF(DATE(reports.`created_at`) != reports.`publish_at`, 1, 0)) AS base_report_count')
        )
            ->join('reports', 'reports.team_id', 'teams.id')
            ->join('users', 'users.id', 'reports.user_id')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1)
            ->where('reports.sm_calendar_master_id', 0)
            ->where('teams.active', 1);


        // echo "<BR>".$from;
        // echo "<BR>".$to;


        $firDocumentReportIdArr = array();
        if ($fir_documents == 1) {
            $firDocumentReportIdArr = $this->getFirDocumentReportIds();
            if (count($firDocumentReportIdArr) > 0) {
                $workReport = $workReport->wherein('reports.id', $firDocumentReportIdArr);
            }
        }

        if (!empty($from && $to)) {
            $workReport = $workReport->whereBetween($dateRangeAttribute, [$from, $to]);
        }

        if (!empty($tName)) {
            $workReport = $workReport->where('reports.team_id', $tName);
        }

        // if (!empty($language_id)) {
        //     $workReport = $workReport->where('reports.language_id', $language_id);
        // }

        if (count($language_id) > 0) {
            $workReport = $workReport->wherein('reports.language_id', $language_id);
        }

        if (count($location_id) > 0) {
            $workReport = $workReport->wherein('reports.location_id', $location_id);
        }

        if (!empty($allLocationIds)) {
            $locationIdsArr = explode(',', $allLocationIds);
            $workReport = $workReport->whereIn('reports.location_id', $locationIdsArr);
        }

        // if (!empty($module)) {
        //     $workReport = $workReport->where('reports.module_id', $module);
        // }

        // if (!empty($chapter_id)) {
        //     $workReport = $workReport->where('reports.chapter_id', $chapter_id);
        // }

        if (count($module_ids_arr) > 0) {
            $workReport = $workReport->wherein('reports.module_id', $module_ids_arr);
        }
        if (count($chapter_ids_arr) > 0) {
            $workReport = $workReport->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        if (!empty($showReportForUser)) {
            $workReport = $workReport->where('user_id', $showReportForUser);
        }

        $workReport = $workReport->groupby('teams.id', 'users.id');
        // $workReport = $workReport->toSql();
        // dd($workReport);
        //         $sql = $workReport->toSql();
        // $bindings = $workReport->getBindings();

        // $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);

        // dd($fullSql);

        $workReport = $workReport->get();
        // dd($workReport);

        if ($mode == "getcount") {
            $workReport = $workReport->toArray();
            $repcount = count($workReport);
            return $repcount;
        }

        $mytime = Carbon\Carbon::now();

        $team_name_converted = "_work_report";
        if (!empty($tName)) {
            $team_name_converted = str_replace(" ", "_", $teamName->name);
            $team_name_converted = "_" . strtolower($team_name_converted);
        }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {

            $downloadFileName = date('jSF') . $team_name_converted;
        }

        $reportParams = [
            'allTeams' => $allTeams,
            'to' => $to,
            'from' => $from,
            'report_data_type' => $report_data_type,
            'workReport' => $workReport,
            'mytime' => $mytime,
            'reportType' => $reportType,
            'moduleName' => $moduleName,
            'teamName' => $teamName,
            'chapterName' => $chapterName,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading
        ];

        // dd($reportParams);
        // dd($mode);


        if ($mode == "view") {

            $view_content_web =  view('report.webview_work_report',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else {

            if ($reportType == "word") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_content_word = View::make('report.wordview_work_report', $reportParams)->render();

                $view_content_word = str_replace("&amp;", "and", $view_content_word);

                //added on Dec 13 for issue of #039 error '
                $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

                // echo "<pre>";print_r($view_content_word);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "googledoc") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_contents_googledoc = View::make('report.googledoc_work_report', $reportParams)->render();

                $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);

                //added on Dec 13 for issue of #039 error '
                $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

                // echo "<pre>";print_r($view_contents_googledoc);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                return $fileName;
            } else {

                // dd($reportParams);
                ///////////////////////////////
                /// Below is to create PDF
                ///////////////////////////////

                //new code on Oct 30
                $view_content_mpdfview = View::make('report.mpdfview_work_report', $reportParams)->render();

                $mpdf = new \Mpdf\Mpdf();
                $mpdf->WriteHTML($view_content_mpdfview);

                // echo "<pre>";print_r($view_content_mpdfview);exit;

                $fileName = $downloadFileName . '.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
                return $fileName;
            }
        }
    }

    // hashtag_report
    // wordview, googledoc, mpdfview
    public function downloadHashtagReport($request, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode)
    {

        // echo "inside downloadHashtagReport";
        // echo "<pre>";
        // print_r($request->all());
        // dd($request->all());
        // dd($roleId);

        $showReportForUser = "";
        // if ($roleId == config('app.basic_user_role_id')) {
        //     $user = auth()->user();
        //     $showReportForUser = $user->id;
        // } else {
        //     if ($request->from_user) {
        //         $showReportForUser = $request->from_user;
        //     }
        // }

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }
        // dd($module_ids_arr);

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }
        // dd($chapter_ids_arr);

        $tName = $request->team_name;
        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;

        $report_data_type = $request->report_data_type;
        $hashtags = $request->hashtags;

        $group_by_language = '';
        if ($request->group_by_language) {
            $group_by_language = $request->group_by_language;
        }
        $group_by_location = '';
        if ($request->group_by_location) {
            $group_by_location = $request->group_by_location;
        }

        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }

        $location_state_id = array();
        if ($request->location_state_id) {
            $location_state_id = $request->location_state_id;
        }

        $language_id = array();
        if ($request->language_id) {
            $language_id = $request->language_id;
        }

        $fir_documents = 0;
        if ($request->fir_documents) {
            $fir_documents = $request->fir_documents;
        }

        $report_data_type = "upload_date_report";
        if (!empty($request->report_data_type)) {
            $report_data_type = $request->report_data_type;
        }

        $reportTitle = "Team 3 Hashtag Report";

        if (!empty($from && $to)) {
            $from = $from . " 00:00:00";
            $to = $to . " 23:59:59";
        }
        $dateRangeAttribute = "reports.created_at";


        // echo "<br> from ".$from;
        // echo "<br> to ".$to;
        // echo "<br> team_id ".$tName;

        $hashtagArr = array();
        $hashtagSufix = "";
        if (!empty($hashtags)) {
            $hashtagArr = explode("\r\n", $hashtags);
            $hashtagSufix = implode(", ", $hashtagArr);
        }

        // dd($hashtagArr);

        $newsOrderByOrder = "desc";
        if (!empty($request->news_order_by_order)) {
            $newsOrderByOrder = $request->news_order_by_order;
        }
        // echo $newsOrderByOrder;

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
        $moduleName = "";
        $chapterName = "";

        $report = Report::where('is_deleted', 0)->where('active', 1);

        $data = Report::with(['keypoint', 'chapter', 'module'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select('reports.chapter_id', DB::raw('COUNT(chapters.id) AS chapter_count'));

        $data =  $data->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $data =  $data->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });

        $dataModule = Report::with(['keypoint', 'chapter', 'module'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select('reports.module_id', DB::raw('COUNT(modules.id) AS module_count'));

        $dataModule =  $dataModule->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $dataModule =  $dataModule->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });

        $dataHashtag = Report::with(['keypoint', 'chapter', 'module', 'videolink', 'screenshot', 'feateredimage'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1);

        $dataHashtag =  $dataHashtag->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $dataHashtag =  $dataHashtag->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });


        $firDocumentReportIdArr = array();
        if ($fir_documents == 1) {
            $firDocumentReportIdArr = $this->getFirDocumentReportIds();
            if (count($firDocumentReportIdArr) > 0) {
                $report = $report->wherein('reports.id', $firDocumentReportIdArr);
                $data = $data->wherein('reports.id', $firDocumentReportIdArr);
                $dataModule = $dataModule->wherein('reports.id', $firDocumentReportIdArr);
                $dataHashtag = $dataHashtag->wherein('reports.id', $firDocumentReportIdArr);
            }
        }

        if (!empty($from && $to)) {
            $report = $report->whereBetween($dateRangeAttribute, [$from, $to]);
            $data = $data->whereBetween($dateRangeAttribute, [$from, $to]);
            $dataModule = $dataModule->whereBetween($dateRangeAttribute, [$from, $to]);
            $dataHashtag = $dataHashtag->whereBetween($dateRangeAttribute, [$from, $to]);
        }

        if (!empty($tName)) {
            $report = $report->where('team_id', $tName);
            $data = $data->where('reports.team_id', $tName);
            $dataModule = $dataModule->where('reports.team_id', $tName);
            $dataHashtag = $dataHashtag->where('reports.team_id', $tName);
        }

        if (!empty($request->first_time)) {
            $first_time = $request->first_time;
            $report = $report->where('first_time', $first_time);
            $data = $data->where('first_time', $first_time);
            $dataModule = $dataModule->where('first_time', $first_time);
            $dataHashtag = $dataHashtag->where('first_time', $first_time);
        }

        if (!empty($request->followup)) {
            $followup = $request->followup;
            $report = $report->where('has_followup', $followup);
            $data = $data->where('has_followup', $followup);
            $dataModule = $dataModule->where('has_followup', $followup);
            $dataHashtag = $dataHashtag->where('has_followup', $followup);
        }

        if (!empty($request->calendar_date)) {
            $calendar_date = $request->calendar_date;
            $report = $report->where('calendar_date', $calendar_date);
            $data = $data->where('calendar_date', $calendar_date);
            $dataModule = $dataModule->where('calendar_date', $calendar_date);
            $dataHashtag = $dataHashtag->where('calendar_date', $calendar_date);
        }

        if (count($language_id) > 0) {
            $report = $report->wherein('language_id', $language_id);
            $data = $data->wherein('reports.language_id', $language_id);
            $dataModule = $dataModule->wherein('reports.language_id', $language_id);
            $dataHashtag = $dataHashtag->wherein('reports.language_id', $language_id);
        }

        if (count($location_id) > 0) {
            $report = $report->wherein('location_id', $location_id);
            $data = $data->wherein('reports.location_id', $location_id);
            $dataModule = $dataModule->wherein('reports.location_id', $location_id);
            $dataHashtag = $dataHashtag->wherein('reports.location_id', $location_id);
        }

        if (count($location_state_id) > 0) {
            $report = $report->wherein('location_state_id', $location_state_id);
            $data = $data->wherein('reports.location_state_id', $location_state_id);
            $dataModule = $dataModule->wherein('reports.location_state_id', $location_state_id);
            $dataHashtag = $dataHashtag->wherein('reports.location_state_id', $location_state_id);
        }

        if (!empty($allLocationIds)) {
            $locationIdsArr = explode(',', $allLocationIds);

            $report = $report->whereIn('location_id', $locationIdsArr);
            $data = $data->whereIn('reports.location_id', $locationIdsArr);
            $dataModule = $dataModule->whereIn('reports.location_id', $locationIdsArr);
            $dataHashtag = $dataHashtag->whereIn('reports.location_id', $locationIdsArr);
        }

        if (count($module_ids_arr) > 0) {
            $report = $report->wherein('reports.module_id', $module_ids_arr);
            $data = $data->wherein('reports.module_id', $module_ids_arr);
            $dataModule = $dataModule->wherein('reports.module_id', $module_ids_arr);
            $dataHashtag = $dataHashtag->wherein('reports.module_id', $module_ids_arr);
        }
        if (count($chapter_ids_arr) > 0) {
            $report = $report->wherein('reports.chapter_id', $chapter_ids_arr);
            $data = $data->wherein('reports.chapter_id', $chapter_ids_arr);
            $dataModule = $dataModule->wherein('reports.chapter_id', $chapter_ids_arr);
            $dataHashtag = $dataHashtag->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        if (!empty($showReportForUser)) {
            $report = $report->where('user_id', $showReportForUser);
            $data = $data->where('user_id', $showReportForUser);
            $dataModule = $dataModule->where('user_id', $showReportForUser);
            $dataHashtag = $dataHashtag->where('user_id', $showReportForUser);
        }

        $report = $report->orderBy('id', $newsOrderByOrder);
        // $report = $report->toSql();
        // dd($report);

        $report = $report->get()->toArray();
        $repcount = count($report);
        // dd($repcount);

        if ($mode == "getcount") {
            return $repcount;
        }

        $dataHashtag = $dataHashtag->orderBy('reports.id', $newsOrderByOrder);
        // $dataHashtag = $dataHashtag->toSql();
        // dd($dataHashtag);

        $dataHashtag = $dataHashtag->get()->toArray();
        // dd($dataHashtag);

        $baseFolderName = time() . rand(1111, 9999);
        mkdir(storage_path(config('app.download_report_base_folder') . "/" . $baseFolderName));

        $screenshotFolderName = $baseFolderName . "/screenshots";
        mkdir(storage_path(config('app.download_report_base_folder') . "/" . $screenshotFolderName));

        $keyNews = [];
        if ($repcount > 0) {

            //////////////////////////////////
            // group by module_id
            //////////////////////////////////
            $dataModule = $dataModule->orderBy('reports.id', $newsOrderByOrder);
            $dataModule = $dataModule->groupBy('reports.module_id');

            // $dataModule = $dataModule->toSql();
            // dd($dataModule);

            $dataModule = $dataModule->get()->toArray();

            //////////////////////////////////
            // group by chapter_id
            //////////////////////////////////
            //Group by Location and Language
            if ($group_by_location == 1) {
                $data = $data->orderBy('reports.location_id');
            }
            if ($group_by_language == 1) {
                $data = $data->orderBy('reports.language_id');
            }
            $data = $data->orderBy('reports.id', $newsOrderByOrder);
            $data = $data->groupBy('reports.chapter_id');
            // $data = $data->toSql();
            // dd($data);

            $data = $data->get()->toArray();
            // dd($data);

            foreach ($data as $datas) {

                // dd($datas);
                // echo "<br>Processing chapter_id ".$datas['chapter_id'];

                $news = Report::select(
                    'reports.*',
                    'report_sources.source',
                    'languages.id as language_id',
                    'languages.name as language_name',
                    'locations.name as location_name',
                    'location_states.name as location_state_name'
                )
                    ->join('report_sources', 'report_sources.id', 'reports.report_source_id');

                $news = $news->join('languages', 'reports.language_id', 'languages.id');
                $news = $news->leftjoin('locations', 'reports.location_id', 'locations.id');
                $news = $news->leftjoin('location_states', 'reports.location_state_id', 'location_states.id');

                $news = $news->where('chapter_id', $datas['chapter_id'])->where('is_deleted', 0)->where('reports.active', 1);

                $firDocumentReportIdArr = array();
                if ($fir_documents == 1) {
                    $firDocumentReportIdArr = $this->getFirDocumentReportIds();
                    if (count($firDocumentReportIdArr) > 0) {
                        $news = $news->wherein('reports.id', $firDocumentReportIdArr);
                    }
                }

                if (!empty($from && $to)) {
                    $news = $news->whereBetween($dateRangeAttribute, [$from, $to]);
                }

                if (count($language_id) > 0) {
                    $news = $news->wherein('language_id', $language_id);
                }

                if (!empty($request->first_time)) {
                    $first_time = $request->first_time;
                    $news = $news->where('first_time', $first_time);
                }

                if (!empty($request->followup)) {
                    $followup = $request->followup;
                    $news = $news->where('has_followup', $followup);
                }

                if (!empty($request->calendar_date)) {
                    $calendar_date = $request->calendar_date;
                    $news = $news->where('calendar_date', $calendar_date);
                }

                if (count($location_id) > 0) {
                    $news = $news->wherein('reports.location_id', $location_id);
                }

                if (count($location_state_id) > 0) {
                    $news = $news->wherein('reports.location_state_id', $location_state_id);
                }

                if (!empty($allLocationIds)) {
                    $locationIdsArr = explode(',', $allLocationIds);
                    $news = $news->whereIn('reports.location_id', $locationIdsArr);
                }

                if (!empty($showReportForUser)) {
                    $news = $news->where('user_id', $showReportForUser);
                }

                //Group by Location and Language
                if ($group_by_location == 1) {
                    $news = $news->orderBy('reports.location_id');
                }
                if ($group_by_language == 1) {
                    $news = $news->orderBy('reports.language_id');
                }
                $news = $news->orderBy('reports.id', $newsOrderByOrder);

                // $news = $news->toSql();
                // dd($news);

                $news = $news->get()->toArray();
                // dd($news);

                $newsDetails = [];
                $i = 0;
                foreach ($news as $news_data) {
                    // dd($news_data);
                    $keypoint = ReportKeypoint::where('report_id', $news_data['id'])->orderby('report_keypoints.id', 'ASC')->get()->toArray();
                    $tag = ReportTag::where('report_id', $news_data['id'])->get()->toArray();
                    $videos = ReportVideolink::where('report_id', $news_data['id'])->get()->toArray();
                    $images = ReportImagelink::where('report_id', $news_data['id'])->get()->toArray();
                    $screenshots = ReportScreenshot::where('report_id', $news_data['id'])->orderby('screenshot_type')->get()->toArray();
                    $featuredimages = ReportFeaturedimage::where('report_id', $news_data['id'])->get()->toArray();
                    $documents = ReportDocument::where('report_id', $news_data['id'])->get()->toArray();
                    $followups = ReportFollowup::where('report_id', $news_data['id'])->get()->toArray();

                    $team_tags = ReportTeamTag::select(
                        DB::raw('tt.tag as parent_tag'),
                        DB::raw('team_tags.tag as tag')
                    )
                        ->join('team_tags', 'report_team_tags.tag_id', 'team_tags.id')
                        ->join(DB::raw('team_tags as tt'), 'report_team_tags.parent_id', 'tt.id')
                        ->where('report_team_tags.report_id', $news_data['id'])->get();

                    //copy $screenshots
                    foreach ($screenshots as $screenshot) {
                        if ($screenshot['screenshot']) {

                            // echo "<BR>screenshot ".$screenshot['screenshot'];

                            $sourceScreenshot = Helper::getImageUrl($screenshot['screenshot']);

                            $pos = strpos($sourceScreenshot, "uploads/screenshots/");
                            if ($pos !== false) {
                                $targetScreenshot = storage_path(config('app.download_report_base_folder') . "/" . $screenshotFolderName) . str_replace('uploads/screenshots/', '/', $screenshot['screenshot']);
                            } else {
                                $targetScreenshot = storage_path(config('app.download_report_base_folder') . "/" . $screenshotFolderName) . str_replace('uploads/', '/', $screenshot['screenshot']);
                            }

                            if (file_exists($sourceScreenshot)) {
                                File::copy($sourceScreenshot, $targetScreenshot);

                                // echo "<BR>".$sourceScreenshot. " to " .$targetScreenshot;
                                // exit();
                            }
                        }
                    }



                    $newsDetails[$i]['id'] = $news_data['id'];
                    $newsDetails[$i]['language_name'] = $news_data['language_name'];
                    $newsDetails[$i]['location_name'] = $news_data['location_name'];
                    $newsDetails[$i]['location_state_name'] = $news_data['location_state_name'];

                    $newsDetails[$i]['heading'] = $news_data['heading'];
                    $newsDetails[$i]['link'] = $news_data['link'];
                    $newsDetails[$i]['source'] = $news_data['source'];
                    $newsDetails[$i]['created_at'] = $news_data['created_at'];
                    $newsDetails[$i]['publish_at'] = $news_data['publish_at'];
                    $newsDetails[$i]['keypoint'] = $keypoint;
                    $newsDetails[$i]['tag'] = $tag;
                    $newsDetails[$i]['screenshot'] = $news_data['screenshot'];
                    $newsDetails[$i]['feature_image_1'] = $news_data['feature_image_1'];
                    $newsDetails[$i]['feature_image_2'] = $news_data['feature_image_2'];
                    $newsDetails[$i]['videos'] = $videos;
                    $newsDetails[$i]['images'] = $images;
                    $newsDetails[$i]['screenshots'] = $screenshots;
                    $newsDetails[$i]['documents'] = $documents;
                    $newsDetails[$i]['followups'] = $followups;
                    $newsDetails[$i]['featuredimages'] = $featuredimages;
                    $newsDetails[$i]['team_tags'] = $team_tags;



                    $location = Location::select('name')->find($news_data['location_id']);
                    $locationState = LocationState::select('name')->find($news_data['location_state_id']);

                    $newsDetails[$i]['location'] = $location;
                    $newsDetails[$i]['locationState'] = $locationState;


                    $i++;
                }
                $keyNews[$datas['chapter_id']] = $newsDetails;
            }
        }

        $mytime = Carbon\Carbon::now();

        $team_name_converted = "_all_teams";
        if (!empty($tName)) {
            $team_name_converted = str_replace(" ", "_", $teamName->name);
            $team_name_converted = "_" . strtolower($team_name_converted);
        }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {

            $downloadFileName = date('jSF') . $team_name_converted;
        }

        $publicReportColor = "blue";
        if (config('app.color_scheme') == 'pink') {
            $publicReportColor = "red";
        }

        $viewLayout = "layouts.admin";
        // if ($roleId == 0) {
        //     // $viewLayout = "layouts.app";
        //     $viewLayout = "layouts.webview";
        // }

        $reportParams = [
            'to' => $to,
            'from' => $from,
            'report_data_type' => $report_data_type,
            'dataModule' => $dataModule,
            'data' => $data,
            'report' => $report,
            'repcount' => $repcount,
            'keyNews' => $keyNews,
            'mytime' => $mytime,
            'reportType' => $reportType,
            'moduleName' => $moduleName,
            'teamName' => $teamName,
            'chapterName' => $chapterName,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading,
            'roleId' => $roleId,
            'moduleNameArr' => $moduleNameArr,
            'viewLayout' => $viewLayout,
            'publicReportColor' => $publicReportColor,
        ];

        if ($mode == "view") {

            $view_content_web =  view('report.webview',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else {

            if ($reportType == "word") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_content_word = View::make('report.wordview', $reportParams)->render();

                $view_content_word = str_replace("&amp;", "and", $view_content_word);

                //added on Dec 13 for issue of #039 error '
                $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

                // echo "<pre>";print_r($view_content_word);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

                $fileName = $baseFolderName . "/" . $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                // return $fileName;
            } elseif ($reportType == "googledoc") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_contents_googledoc = View::make('report.googledoc', $reportParams)->render();

                $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);

                //added on Dec 13 for issue of #039 error '
                $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

                // echo "<pre>";print_r($view_contents_googledoc);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

                $fileName = $baseFolderName . "/" . $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }

                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                // return $fileName;
            } else {

                ///////////////////////////////
                /// Below is to create PDF
                ///////////////////////////////

                //$view = mb_convert_encoding('report.pdfview', 'HTML-ENTITIES', 'UTF-8');

                // commented on Oct 30
                // $pdf = PDF::loadView('report.pdfview', compact('reportType','report','data','from','to','repcount','keyNews','mytime','moduleName','teamName','chapterName'));
                // $fileName = 'Report' . '-' . now()->toDateString() . '.pdf';
                // return $pdf->download($fileName);

                //new code on Oct 30
                $view_content_mpdfview = View::make('report.mpdfview', $reportParams)->render();

                //echo "<pre>";print_r($view_content_mpdfview);exit;

                $mpdf = new \Mpdf\Mpdf();
                $mpdf->WriteHTML($view_content_mpdfview);

                $fileName = $baseFolderName . "/" . $downloadFileName . '.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
            }

            ///////////////////////////
            // Zip File Creation
            ///////////////////////////
            $languagesData = Language::select('id', 'name')->wherenull('deleted_at')->orderBy('id')->get()->toArray();

            $hashtagReportTypeArr = array("Headline", "NewsSourceLinks", "Headline_Source", "Keypoints_Source", "VideoLinks");

            foreach ($languagesData as $languageData) {

                // echo "<hr><br>Processing language ".$languageName;

                $folderPath = $baseFolderName . '/' . $languageData['name'];
                // echo  $folderPath;
                // exit();
                mkdir(storage_path(config('app.download_report_base_folder') . "/" . $folderPath));

                foreach ($hashtagReportTypeArr as $hashtagReportType) {

                    // echo "<br>Processing hashtagReportType ".$hashtagReportType;

                    $phpWord = new \PhpOffice\PhpWord\PhpWord();
                    \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
                    $phpWord->setDefaultFontName('Hind');
                    $phpWord->setDefaultFontSize(11);


                    $section = $phpWord->addSection();
                    $counter = 1;
                    $hasVideoLinks = "no";
                    foreach ($dataHashtag as $key => $reportData) {
                        if ($reportData['language_id'] == $languageData['id']) {
                            if ($hashtagReportType == "Headline") {
                                $section->addText($counter . '. ' . $reportData["heading"], ['bold' => true]);
                                $section->addText($hashtagSufix);
                            } else if ($hashtagReportType == "NewsSourceLinks") {
                                $section->addLink($reportData["link"], $counter . '. ' . $reportData["link"], ['color' => '00219D', 'bold' => true]);
                                $section->addText($hashtagSufix);
                            } else if ($hashtagReportType == "Headline_Source") {
                                $section->addText($counter . '. ' . $reportData["heading"], ['bold' => true]);
                                $section->addLink($reportData["link"], $reportData["link"], ['color' => '00219D', 'bold' => true]);
                                $section->addText($hashtagSufix);
                            } else if ($hashtagReportType == "Keypoints_Source") {

                                foreach ($reportData['keypoint'] as $key => $keypoint) {
                                    $cKey = $key + 1;
                                    $section->addText($cKey . '. ' . trim($keypoint['keypoint']), ['bold' => true]);
                                    $section->addLink($reportData["link"], $reportData["link"], ['color' => '00219D', 'bold' => true]);
                                    $section->addText($hashtagSufix);
                                }
                            } else if ($hashtagReportType == "VideoLinks") {
                                foreach ($reportData['videolink'] as $key => $video) {
                                    $cKey = $key + 1;
                                    $section->addLink($video['videolink'], $cKey . '. ' . $video['videolink'], ['color' => '00219D', 'bold' => true]);
                                    $section->addText($hashtagSufix);
                                    $hasVideoLinks = "yes";
                                }
                            }
                            $counter++;
                        }
                    }
                    if ($hashtagReportType == "Headline") {
                        $fileName = $folderPath . '/Headline_Hashtag' . '.docx';
                    } else if ($hashtagReportType == "NewsSourceLinks") {
                        $fileName = $folderPath . '/NewsLink_Hashtag' . '.docx';
                    } else if ($hashtagReportType == "Headline_Source") {
                        $fileName = $folderPath . '/Headline_Source_Hashtag' . '.docx';
                    } else if ($hashtagReportType == "Keypoints_Source") {
                        $fileName = $folderPath . '/Keypoints_Source_Hashtag' . '.docx';
                    } else if ($hashtagReportType == "VideoLinks") {
                        $fileName = $folderPath . '/VideoLinks_Hashtag' . '.docx';
                    }

                    if ($hashtagReportType == "VideoLinks") {
                        if ($hasVideoLinks == "yes") {
                            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                            $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                        }
                    } else {
                        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                        $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                    }
                }
            }

            // exit();
            $zipFileName = 'HashTagReport' . $baseFolderName . '.zip';

            // Create ZipArchive Obj
            $zip = new ZipArchive;

            if ($zip->open(storage_path(config('app.download_report_base_folder') . "/" . $zipFileName), ZipArchive::CREATE) === TRUE) {
                $this->addContent($zip, realpath(storage_path(config('app.download_report_base_folder') . "/" . $baseFolderName)));
                $zip->close();
            }

            File::deleteDirectory(realpath(storage_path(config('app.download_report_base_folder') . "/" . $baseFolderName)));

            return $zipFileName;
        }
    }

    /////////////////////////////
    // View Report methods
    /////////////////////////////

    public function getIdsBySlug($slug, $issue, $category, $sub_category)
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>category = ".$category;
        // echo "<br>sub_category = ".$sub_category;
        // echo "<br>website_id = ".session()->get('website_id');
        // exit();

        $parameterData = array();

        if ($issue) {
            $teamData = Team::select('teams.id', 'teams.name')
                ->where('teams.slug', $issue)
                ->join('websites', 'websites.id', 'teams.website_id')
                ->where('websites.domain', $slug)
                ->get();
            // dd( $teamData);
            foreach ($teamData as $data) {
                $parameterData['team_id'] = $data->id;
                $parameterData['team_name'] = $data->name;
            }
        }

        if ($category) {
            $categoryData = Module::select('modules.id', 'modules.name')
                ->where('modules.slug', $category)
                ->join('websites', 'websites.id', 'modules.website_id')
                ->where('websites.domain', $slug)
                ->where('modules.team_id', $parameterData['team_id'])
                ->get();
            foreach ($categoryData as $data) {
                $parameterData['category_id'] = $data->id;
                $parameterData['category_name'] = $data->name;
            }
        }

        if ($sub_category) {
            $sub_categoryData = Chapter::select('chapters.id', 'chapters.name')
                ->where('chapters.slug', $sub_category)
                ->join('websites', 'websites.id', 'chapters.website_id')
                ->where('websites.domain', $slug)
                ->where('chapters.module_id', $parameterData['category_id'])
                ->get();
            foreach ($sub_categoryData as $data) {
                $parameterData['sub_category_id'] = $data->id;
                $parameterData['sub_category_name'] = $data->name;
            }
        }

        // dd($parameterData);
        // exit();
        return $parameterData;
    }

    public function viewReportByIssueAndDate(Request $request, $slug, $issue, $date = "")
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>date = ".$date;

        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000"); 

        $from = "";
        $to = "";
        if ($date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $date);
            $from = $fromdate->format('Y-m-d');
            $to = $fromdate->format('Y-m-d');
        }

        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        $allLocationIds = "";
        $roleId = 0;

        $mode = "view";
        if (!empty($request->mode)) {
            $mode = $request->mode;
        }

        // dd($issue);
        $fileName = $this->viewReportDetail($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
        if ($mode == 'download') {
            return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
        } else if ($mode == 'getcount') {
            echo $fileName;
        }
    }

    public function viewReportByIssue(Request $request, $slug, $issue, $date = "")
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>date = ".$date;

        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000"); 

        $from = "";
        $to = "";
        if ($date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $date);
            $from = $fromdate->format('Y-m-d');
            $to = $fromdate->format('Y-m-d');
        }

        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        $allLocationIds = "";
        $roleId = 0;
        $mode = "view";

        $fileName = $this->viewReportDetail($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
        if ($mode == 'download') {
            return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
        } else if ($mode == 'getcount') {
            echo $fileName;
        }
    }

    public function viewReportByIssueCat(Request $request, $slug, $issue, $category, $date = "")
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>category = ".$category;
        // echo "<br>date = ".$date;

        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000");     

        $from = "";
        $to = "";
        if ($date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $date);
            $from = $fromdate->format('Y-m-d');
            $to = $fromdate->format('Y-m-d');
        }

        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        $allLocationIds = "";
        $roleId = 0;
        $mode = "view";

        $fileName = $this->viewReportDetail($request, $slug, $issue, $category, $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
        if ($mode == 'download') {
            return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
        } else if ($mode == 'getcount') {
            echo $fileName;
        }
    }

    public function viewReportByIssueCatSubCat(Request $request, $slug, $issue, $category, $sub_category, $date = "")
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>category = ".$category;
        // echo "<br>sub_category = ".$sub_category;
        // echo "<br>date = ".$date;

        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000");     

        $from = "";
        $to = "";
        if ($date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $date);
            $from = $fromdate->format('Y-m-d');
            $to = $fromdate->format('Y-m-d');
        }

        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        $allLocationIds = "";
        $roleId = 0;
        $mode = "view";

        $fileName = $this->viewReportDetail($request, $slug, $issue, $category, $sub_category, $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
        if ($mode == 'download') {
            return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
        } else if ($mode == 'getcount') {
            echo $fileName;
        }
    }

    public function viewReportAllIssueAndDate(Request $request, $slug, $issue, $date = "")
    {
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>date = ".$date;
        // echo "<br>mode = ".$mode;
        // dd($slug);
        // dd($issue);
        // dd($request->all());
        // ini_set('memory_limit', '512M');
        // ini_set("pcre.backtrack_limit", "10000000000"); 

        if (empty(session()->get('website_id'))) {
            $this->loadCenter($slug);
        }

        $issueFromUrl = $issue;

        $from = "";
        $to = "";
        $prevDate = "";
        $nextDate = "";
        if ($date) {
            $fromdate = DateTime::createFromFormat('d-m-Y', $date);
            $from = $fromdate->format('Y-m-d');
            $to = $fromdate->format('Y-m-d');

            // $today = \Carbon\Carbon::createFromFormat('d-m-Y', $date);
            $prevDate = \Carbon\Carbon::createFromFormat('d-m-Y', $date)->subDays(1)->format('d-m-Y');
            $nextDate =  \Carbon\Carbon::createFromFormat('d-m-Y', $date)->addDays(1)->format('d-m-Y');

            // $selectedDate = $date;
            // $dailyPopupDate = $nextDate;
        }

        $moduleListByChapter = Module::select('chapters.id', 'modules.name')
            ->join('chapters', 'chapters.module_id', 'modules.id')
            ->where('chapters.active', 1)
            ->orderby('chapters.id')
            ->get();
        $moduleNameArr = array();
        foreach ($moduleListByChapter as $key => $val) {
            $moduleNameArr[$val->id] = $val->name;
        }

        $allLocationIds = "";
        $roleId = 0;

        $mode = "view-all";
        if (!empty($request->mode)) {
            $mode = $request->mode;
        }

        // dd($mode);
        if ($mode == "view-all") {

            $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
            // dd($websiteIds);
            $date_condition_sm_cal = " AND reports.publish_at = '" . $from . "' ";

            //fetch SM data
            $all_items_sm_cal = DB::connection('setfacts')->select("
            SELECT 'sm_cal' as type, allItems.publish_at, allItems.heading, allItems.description,
             allItems.link, allItems.image, allItems.calendar_year, allItems.website, allItems.id, allItems.team_id,
             allItems.language_name, allItems.source, allItems.team_name,
             allItems.module_id, allItems.module_name, allItems.chapter_id, allItems.chapter_name
            FROM
            (
                SELECT
                websites.domain AS website,
                reports.team_id,
                reports.id,
                reports.publish_at,
                reports.heading,
                reports.description,
                reports.link,            
                reports.image as image,
                DATE_FORMAT(
                  reports.publish_at,
                  '%Y'
                  ) as calendar_year,
                languages.name as language_name,
                report_sources.source,
                teams.name as team_name,
                modules.id as module_id,
                modules.name as module_name,
                chapters.id as chapter_id,
                chapters.name as chapter_name
                FROM
                sm_calendar_masters
                join websites on websites.id = sm_calendar_masters.website_id         
                , reports
                join languages on languages.id = reports.language_id
                join report_sources on report_sources.id = reports.report_source_id
                left join teams on teams.id = reports.team_id
                left join modules on modules.id = reports.module_id
                left join chapters on chapters.id = reports.chapter_id
                WHERE 
                sm_calendar_masters.id = reports.sm_calendar_master_id and
                sm_calendar_masters.active = 1 and
                sm_calendar_masters.website_id in (" . $websiteIds . ") and                        
                reports.id IS NOT NULL and 
                reports.active = 1 and             
                ISNULL(reports.deleted_at)
                " . $date_condition_sm_cal . "
            ) AS allItems  
            ORDER BY id        
          ");

            //   dd($all_items_sm_cal);

            $view_sm_content_web = "";

            // dd(count($all_items_sm_cal));

            $count_sm = count($all_items_sm_cal);
            if (count($all_items_sm_cal) > 0) {

                $SMParams = [
                    'selectedDate' => $from,
                    'all_items_sm_cal' => $all_items_sm_cal,
                    'prevDate' => $prevDate,
                    'nextDate' => $nextDate,
                    'slug' => $slug,
                    'issue' => $issue,
                    'count_sm' => $count_sm,
                ];

                $view_sm_content_web =  view('dailymonitoring.sm_index_webview',  $SMParams)->render();
                // echo $view_sm_content_web;
                // exit();               
            }


            //get all teams
            $teamIdsToExclude = 13;
            $allTeamSql = "
            SELECT
            teams.id,teams.name, teams.slug, count(reports.id) as totalReports
            FROM
                teams
            left join reports on reports.team_id = teams.id  and reports.publish_at between '" . $from . "' and '" . $to . "'        and ISNULL( reports.deleted_at)             and reports.active = 1   
            WHERE 
            teams.active = 1 
            and teams.is_public = 1 
            and ISNULL( teams.deleted_at)             
            and teams.website_id in (" . $websiteIds . ")
            and teams.id not in (" . $teamIdsToExclude . ")
            group by teams.id
            ORDER BY  teams.id asc       
            ";

            // echo $allTeamSql;
            // exit();

            $paramTeamId = array();
            $all_issues = DB::connection('setfacts')->select($allTeamSql);
            // dd($all_issues);
            foreach ($all_issues as $all_issue) {
                $paramTeamId[] = $all_issue->slug;
            }

            $content = '';
            $sectionCnt = 2;
            foreach ($paramTeamId as $teamId) {
                // dd("Hello");
                $issue = $teamId;
                //$content .= '<div class="section" id="section'.$sectionCnt.'"><h2 class="sticky-header">Section '.$sectionCnt.'</h2>';
                $content .= $this->viewReportDetail($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode, $issueFromUrl, $sectionCnt);
                //$content .= "</div>";
                $sectionCnt++;
            }

            // fetch todays history
            $todayHistoryContent = $this->getTodayHistoryContent($request, $sectionCnt);

            $topToBottomButton = '<button id="scrollToTop" title="Go to top">↑</button>';

            echo $view_sm_content_web . $content . $todayHistoryContent . $topToBottomButton;

            // echo $content;

        } else {
            // dd("else");
            $fileName = $this->viewReportDetail($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode);
            if ($mode == 'download') {
                return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
            } else if ($mode == 'getcount') {
                echo $fileName;
            } else {
                echo $fileName;
            }
        }
    }

    public function getTodayHistoryContent($request, $sectionCnt)
    {

        $calendar_date = $request->date;     //2022-11-03
        // dump($calendar_date);

        $condition_info_sm_cal = "";
        $condition_info_incidence = "";


        $condition_info_sm_cal_arr = array();
        $condition_info_incidence_arr = array();

        $condition_info_focus_arr = array();
        $condition_info_focus = "";
        $condition_focus_teams = "";


        $calendar_date_db = "";
        $calendar_date_display = "";
        if (!empty($calendar_date)) {
            $dateTime = DateTime::createFromFormat('d-m-Y', $calendar_date);
            // dd($dateTime);
            $calendar_date_db = $dateTime->format('m-d');
            $loadingForYear = $dateTime->format('Y');

            $calendar_date_display = $dateTime->format('F-d');
        }

        $date_condition_info_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m-%d' ) = '" . $calendar_date_db . "' ";
        $date_condition_info_incidence = " AND DATE_FORMAT(incidence_calendars.date,'%m-%d') = '" . $calendar_date_db . "' ";
        $date_condition_info_focus = " AND DATE_FORMAT(documents.date, '%m-%d' ) = '" . $calendar_date_db . "' ";

        $websiteIds = $this->getWebsiteIdsFromSessionForCalendar();

        $allItemsSQLIncidence = "      
          SELECT         
          allItems.type, 
          allItems.website,      
          allItems.calendar_year,
          allItems.date, 
    
          allItems.report_source,
          allItems.language_name,
          allItems.location_name,
          allItems.location_state_name,
          allItems.incidence_calendars_id,
          allItems.incidence_calendars_report_id, 
          allItems.incidence_calendars_heading, 
          allItems.incidence_calendars_description, 
          
          allItems.tdty_checked,
          allItems.tdty_checked_description,
    
          allItems.report_publish_at,
          allItems.report_link,
          allItems.report_team_id,
          allItems.report_module_id,
          allItems.report_chapter_id,
    
          allItems.sm_calendars_team_id,
          allItems.sm_calendars_id,
          allItems.sm_calendars_hashtag ,
          allItems.sm_calendars_description,
          allItems.sm_calendars_link,
          allItems.sm_calendars_image,
    
          allItems.team_name,
          allItems.module_name,
          allItems.chapter_name
          FROM
          (      
    
              SELECT
              'incidence_cal' as type,
              websites.domain AS website,
              date(incidence_calendars.date) as date,
              DATE_FORMAT(
                incidence_calendars.date,
                '%Y'
                ) as calendar_year     ,
    
    
              report_sources.source as report_source,            
              languages.name as language_name,
              locations.name as location_name,
              location_states.name as location_state_name,
              incidence_calendars.id as incidence_calendars_id,
              incidence_calendars.report_id as incidence_calendars_report_id,
              incidence_calendars.heading as incidence_calendars_heading,
              incidence_calendars.description as incidence_calendars_description,            
              
              reports.calendar_date AS tdty_checked,
              reports.calendar_date_description AS tdty_checked_description,
    
              reports.publish_at AS report_publish_at,
              reports.link AS report_link,
              reports.team_id AS report_team_id,
              reports.module_id AS report_module_id,
              reports.chapter_id AS report_chapter_id,
            
              '' as sm_calendars_team_id,
              '' as sm_calendars_id,
              '' as sm_calendars_hashtag ,
              '' as sm_calendars_description,
              '' as sm_calendars_link,
              '' as sm_calendars_image,
    
              teams.`name` AS team_name,
              modules.`name` AS module_name,
              chapters.`name` AS chapter_name                     
                            
              FROM
              incidence_calendars
    
              INNER JOIN reports
              ON incidence_calendars.report_id = reports.id
    
              INNER JOIN languages
              ON languages.id = reports.language_id            
    
              INNER JOIN teams
              ON reports.team_id = teams.id            
    
              INNER JOIN report_sources
              ON report_sources.id = reports.report_source_id   
              
              LEFT JOIN locations
              ON locations.id = reports.location_id
    
              LEFT JOIN location_states
              ON location_states.id = reports.location_state_id            
    
              join websites on websites.id = reports.website_id 
              
              LEFT JOIN modules ON modules.id = reports.module_id
              LEFT JOIN chapters ON chapters.id = reports.chapter_id                
    
              WHERE 1=1
              " . $date_condition_info_incidence . "
              AND reports.is_deleted = 0
              AND reports.active = 1
              AND reports.website_id in (" . $websiteIds . ")  
              " . $condition_info_incidence . "
              GROUP BY reports.id      
    
    
          ) AS allItems  
          ORDER BY allItems.date desc
        ";
        $all_items_incidence = DB::connection('setfacts')->select($allItemsSQLIncidence);

        $allItemsSQLSM = "      
          SELECT         
          allItems.type, 
          allItems.website,      
          allItems.calendar_year,
          allItems.date, 
    
          allItems.report_source,
          allItems.language_name,
          allItems.location_name,
          allItems.location_state_name,
          allItems.incidence_calendars_id,
          allItems.incidence_calendars_report_id, 
          allItems.incidence_calendars_heading, 
          allItems.incidence_calendars_description, 
          
          allItems.tdty_checked,
          allItems.tdty_checked_description,
    
          allItems.report_publish_at,
          allItems.report_link,
          allItems.report_team_id,
          allItems.report_module_id,
          allItems.report_chapter_id,
    
          allItems.sm_calendars_team_id,
          allItems.sm_calendars_id,
          allItems.sm_calendars_hashtag ,
          allItems.sm_calendars_description,
          allItems.sm_calendars_link,
          allItems.sm_calendars_image,
    
          allItems.team_name,
          allItems.module_name,
          allItems.chapter_name
    
          FROM
          (      
              SELECT
              'sm_cal' as type,
              websites.domain AS website,
              date(reports.publish_at) as date,
              DATE_FORMAT(
                reports.publish_at,
                '%Y'
                ) as calendar_year,
    
              '' as report_source,            
              '' as language_name,
              '' as location_name,
              '' as location_state_name,
              '' as incidence_calendars_id,
              '' as incidence_calendars_report_id,
              '' as incidence_calendars_heading,
              '' as incidence_calendars_description,    
    
              '' AS tdty_checked,
              '' AS tdty_checked_description,
    
              '' AS report_publish_at,
              '' AS report_link,
              '' AS report_team_id,
              '' AS report_module_id,
              '' AS report_chapter_id,
             
              reports.team_id as sm_calendars_team_id,
              reports.id as sm_calendars_id,
              reports.heading as sm_calendars_hashtag ,
              reports.description as sm_calendars_description,
              reports.link as sm_calendars_link,
              reports.image as sm_calendars_image,
              teams.`name` AS team_name,
              modules.`name` AS module_name,
              chapters.`name` AS chapter_name                  
    
              FROM
              sm_calendar_masters
              join websites on websites.id = sm_calendar_masters.website_id
              join reports on reports.sm_calendar_master_id = sm_calendar_masters.id
              LEFT JOIN teams ON teams.id = reports.team_id
              LEFT JOIN modules ON modules.id = reports.module_id
              LEFT JOIN chapters ON chapters.id = reports.chapter_id 
              WHERE 
              sm_calendar_masters.id = reports.sm_calendar_master_id and
              sm_calendar_masters.active = 1 and
              sm_calendar_masters.website_id in (" . $websiteIds . ") and                        
              reports.id IS NOT NULL and 
              reports.active = 1 and 
              reports.for_calendar = 1 and ISNULL(reports.deleted_at)
              " . $date_condition_info_sm_cal . "
              " . $condition_info_sm_cal . "  
    
          ) AS allItems  
          ORDER BY allItems.date desc
        ";
        $all_items_sm = DB::connection('setfacts')->select($allItemsSQLSM);


        $all_items_focus_sql = "
            SELECT allItems.id, allItems.website, allItems.type, allItems.title, allItems.description, allItems.tags, allItems.document, allItems.date, allItems.team_id, allItems.module_id, allItems.chapter_id
            , allItems.team_name, allItems.module_name, allItems.chapter_name
            FROM
            (             
                  (
                      SELECT
                      websites.domain AS website,
                      \"document\" AS type,
                      documents.id,
                      documents.date,
                      documents.title,
                      documents.description,
                      documents.document,
                      documents.tags,       
                      documents.team_id,
                      documents.module_id,
                      documents.chapter_id,
                      teams.`name` AS team_name,
                      modules.`name` AS module_name,
                      chapters.`name` AS chapter_name                                          
                    FROM
                      documents  
                      join websites on websites.id = documents.website_id      
                      LEFT JOIN teams ON teams.id = documents.team_id
                      LEFT JOIN modules ON modules.id = documents.module_id
                      LEFT JOIN chapters ON chapters.id = documents.chapter_id                          
                      WHERE  1=1 
                      " . $date_condition_info_focus . " and
                      documents.active = 1              
                      and documents.is_deleted = 0 and ISNULL(documents.deleted_at) 
                      and documents.website_id in (" . $websiteIds . ")  
                      " . $condition_focus_teams . "   
                      " . $condition_info_focus . "                                        
                  ) 
              ) AS allItems
              ORDER BY day(allItems.date) asc
          ";
        $all_items_documents = DB::connection('setfacts')->select($all_items_focus_sql);




        $chapterReportData = $this->fetchChapterReportCount();
        // $all_items = array_merge($all_items_incidence);  
        $all_items = array_merge($all_items_incidence, $all_items_sm, $all_items_documents);
        // dump($all_items);

        $view_history_content_web = "";
        if (count($all_items) > 0) {

            $historyParams = [
                'selectedDate' => $calendar_date_display,
                'all_items' => $all_items,
                'chapterReportData' => $chapterReportData,
                'sectionCnt' => $sectionCnt,
            ];

            $view_history_content_web =  view('report.history_report',  $historyParams)->render();
            // echo $view_history_content_web;
            // exit();               
        }

        return $view_history_content_web;
    }

    public function viewReportDetail($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode, $issueFromUrl = "", $sectionCnt = 0)
    {
        // webview_allteam.blade

        // echo "<br><br>inside downloadReportAllTeam";
        // echo "<br>slug = ".$slug;
        // echo "<br>slug = ".$slug;
        // echo "<br>issue = ".$issue;
        // echo "<br>category = ".$category;
        // echo "<br>sub_category = ".$sub_category;
        // echo "<br>date = ".$from;
        // echo "<br>to = ".$to;
        // echo "<br>allLocationIds = ".$allLocationIds;
        // echo "<br>roleId = ".$roleId;
        // echo "<br>mode = ".$mode;
        // dd($moduleNameArr);

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $reportParams = $this->getViewReportDetailData($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode, $issueFromUrl, $sectionCnt);
        // dd($reportParams['keyNews']);

        // dump($reportParams);

        // dd($mode);

        if ($mode == "view") {
            $view_content_web =  view('report.webview_viewreport',  $reportParams)->render();
            echo $view_content_web;
            exit();
        } else if ($mode == "view-all") {

            $total_news = 0;
            foreach ($reportParams['dataAllTeamReport'] as $key => $teamData) {
                $total_news = $total_news + $teamData['team_count'];
            }
            // dd($total_news);
            $view_content_web_Final = "";
            if ($total_news > 0) {
                $view_content_web =  view('report.webview_viewreport',  $reportParams)->render();

                $view_content_web_Final = '<div class="section" id="section' . $sectionCnt . '"><h2 class="sticky-header">' . $reportParams['reportTitle'] . '</h2>';
                $view_content_web_Final .= '<a id="' . $issue . '"></a>' . $view_content_web;
                $view_content_web_Final .= "</div>";
            }

            // echo $view_content_web;
            // exit();
            return $view_content_web_Final;
        } else {
            if ($reportType == "word") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_content_word = View::make('report.wordview_allteam', $reportParams)->render();

                $view_content_word = str_replace("&amp;", "and", $view_content_word);

                //added on Dec 13 for issue of #039 error '
                $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

                // echo "<pre>";print_r($view_content_word);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }
                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } elseif ($reportType == "googledoc") {
                ///////////////////////////////
                /// Below is to create WORD
                ///////////////////////////////

                $view_contents_googledoc = View::make('report.googledoc_allteam', $reportParams)->render();

                $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);

                //added on Dec 13 for issue of #039 error '
                $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

                // echo "<pre>";print_r($view_contents_googledoc);exit;

                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Its working without this also still can keep it.
                $phpWord->setDefaultFontName('Hind');
                $phpWord->setDefaultFontSize(11);

                \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

                $fileName = $downloadFileName . '.docx';

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                try {
                    $objWriter->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));
                } catch (Exception $e) {
                }
                // return response()->download(storage_path(config('app.download_report_base_folder')."/".$fileName));
                return $fileName;
            } else {

                ///////////////////////////////
                /// Below is to create PDF
                ///////////////////////////////
                // dd($teamNews);
                //new code on Oct 30
                $view_content_mpdfview = View::make('report.mpdfview_allteam', $reportParams)->render();

                //echo "<pre>";print_r($view_content_mpdfview);exit;

                // $mpdf = new \Mpdf\Mpdf();
                // $mpdf->WriteHTML($view_content_mpdfview);

                // $fileName = $downloadFileName . '.pdf';
                // return $mpdf->Output($fileName, 'D');

                $mpdf = new \Mpdf\Mpdf();
                $mpdf->WriteHTML($view_content_mpdfview);

                $fileName = $downloadFileName . '.pdf';
                $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');

                return $fileName;
            }
        }
    }


    private function getViewReportDetailData($request, $slug, $issue, $category = "", $sub_category = "", $from, $to, $allLocationIds, $roleId, $moduleNameArr, $mode, $issueFromUrl = "", $sectionCnt)
    {

        if (empty(session()->get('website_id'))) {
            $this->loadCenter($slug);
        }

        $report_data_type = "base_report";

        $paramsArr = array();
        $paramsArr = $this->getIdsBySlug($slug, $issue, $category, $sub_category);
        // dd( $paramsArr);

        // Issue            
        $paramTeamId = "";
        if (!empty($paramsArr['team_id'])) {
            $paramTeamId = $paramsArr['team_id'];
        }

        $paramCategoryId = "";
        if (!empty($paramsArr['category_id'])) {
            $paramCategoryId = $paramsArr['category_id'];
        }

        $paramSubCategoryId = "";
        if (!empty($paramsArr['sub_category_id'])) {
            $paramSubCategoryId = $paramsArr['sub_category_id'];
        }

        // Category
        $module_ids_arr = array();
        if ($paramCategoryId) {
            $module_ids_arr[] = $paramCategoryId;
        }
        // dd($module_ids_arr);

        // Subcategory
        $chapter_ids_arr = array();
        if ($paramSubCategoryId) {
            $chapter_ids_arr[] = $paramSubCategoryId;
        }
        // dd($chapter_ids_arr);        

        $show_index = true;
        $show_chapter = true;
        $show_link_text = true;
        $show_link = true;
        $show_headline = true;
        $show_source = true;
        $show_tags = true;
        $show_videolinks = true;
        $show_keypoints = true;
        $show_imagesources = true;
        $show_front_page_screenshots = true;
        $show_full_page_screenshots = true;
        $show_featuredimages = true;
        $show_date = true;
        $show_tags = true;
        $show_fir = true;
        $show_documents = true;
        $show_language = true;

        $find_more = '';

        //News of a date = published_date
        $dateRangeAttribute = "reports.publish_at";
        $newsOrderByOrder = "desc";

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        $teamName = "";
        if (!empty($paramTeamId)) {
            // $teamName = Team::select('name')->where('id', $paramTeamId)->where('active', 1)->first();
            $teamName = Team::select('name')->where('id', $paramTeamId)->where('active', 1)->first();
        }
        // dd($teamName);

        $moduleName = "";
        $chapterName = "";

        $fromFormated = \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y');

        $reportTitle = $fromFormated . " | Daily Report";
        if (!empty($teamName)) {
            $reportTitle = $fromFormated . " | " . ucwords(strtolower($teamName->name)) . " | Daily Report";
        }

        $teamsData = array();
        $teams = Team::all()->toArray();
        foreach ($teams as $team) {
            $teamsData[$team['id']] = $team['name'];
        }

        $dataAllTeamReport = Report::with(['team'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->where('reports.sm_calendar_master_id', 0)

            ->select('reports.team_id', DB::connection('setfacts')->Raw('COUNT(teams.id) AS team_count'))
            ->join('teams', 'reports.team_id', '=', 'teams.id');

        $dataAllTeamReport = $dataAllTeamReport->where('teams.is_public', 1);

        $data = Report::with(['chapter'])
            ->where('is_deleted', 0)
            ->where('reports.active', 1)
            ->select(
                'reports.chapter_id',
                DB::connection('setfacts')->Raw('COUNT(DISTINCT(`reports`.`id`)) AS chapter_count')
            );

        $data =  $data->join('chapters', function ($join) {
            $join->on('reports.chapter_id', '=', 'chapters.id');
            $join->wherenull('chapters.deleted_at');
        });

        $data =  $data->join('modules', function ($join) {
            $join->on('reports.module_id', '=', 'modules.id');
            $join->wherenull('modules.deleted_at');
        });

        if (!empty($from && $to)) {
            $data = $data->whereBetween($dateRangeAttribute, [$from, $to]);
            $dataAllTeamReport = $dataAllTeamReport->whereBetween($dateRangeAttribute, [$from, $to]);
        }

        if (!empty($paramTeamId)) {
            $data = $data->where('reports.team_id', $paramTeamId);
            $dataAllTeamReport = $dataAllTeamReport->where('reports.team_id', $paramTeamId);
        }

        if (count($module_ids_arr) > 0) {
            $data = $data->wherein('reports.module_id', $module_ids_arr);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.module_id', $module_ids_arr);
        }
        if (count($chapter_ids_arr) > 0) {
            $data = $data->wherein('reports.chapter_id', $chapter_ids_arr);
            $dataAllTeamReport = $dataAllTeamReport->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        //////////////////////////////////
        // group by chapter_id
        //////////////////////////////////
        $data = $data->wherenull('chapters.deleted_at');

        $data = $data->where('reports.sm_calendar_master_id', '=', 0);

        $data = $data->orderBy('reports.id', $newsOrderByOrder);

        $data = $data->groupBy('reports.chapter_id');
        // $data = $data->toSql();
        // dd($data);
        //         $sql = $dataAllTeamReport->toSql();
        // $bindings = $dataAllTeamReport->getBindings();

        // $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);

        // dd($fullSql);

        // $data = $data->get()->toArray();
        // dd($data);

        // loop for chapterIds          
        $chapterIds = array();
        foreach ($data as $datas) {
            $chapterIds[] = $datas['chapter_id'];
        }
        // dd($chapterIds);   


        $repcountData = clone $dataAllTeamReport;
        $repcountData = $repcountData->groupBy('reports.id');
        $repcountData = $repcountData->get()->count();
        $repcount = $repcountData;
        // dump($repcountData);     
        //         Log::info($dataAllTeamReport->toSql());
        // Log::info($dataAllTeamReport->getBindings());   

        if ($mode == "getcount") {
            return $repcount;
        }

        $keyNews = [];
        $news = [];
        $page = 1;

        if ($repcount > 0) {

            //////////////////////////////////
            // group by module_id
            //////////////////////////////////
            // $dataAllTeamReport = $dataAllTeamReport->wherenull('modules.deleted_at');
            $dataAllTeamReport = $dataAllTeamReport->orderBy('reports.id', $newsOrderByOrder);
            // $dataAllTeamReport = $dataAllTeamReport->groupBy('reports.module_id');
            // $dataModule = $dataModule->toSql();
            // dd($dataModule);

            $dataAllTeamReport = $dataAllTeamReport->get()->toArray();
            // dd($dataAllTeamReport);


            // loop for teamIds          
            $teamIds = array();
            foreach ($dataAllTeamReport as $datas) {
                $teamIds[] = $datas['team_id'];
            }
            // dd($teamIds);               

            $per_page_reports = config('app.per_page_reports');
            $page = 1;
            $count = 0;
            if (!empty($request->page)) {
                $page = $request->page;
                $count = $per_page_reports * ($page - 1);
            }

            DB::connection('setfacts')->select(DB::raw('SET @count:=' . $count . ';'));
            $news = Report::with(
                ['keypoint', 'chapter', 'module', 'tag', 'videolink', 'imagelink', 'screenshot', 'feateredimage', 'document', 'followup']
            )->select(
                'reports.*',
                'report_sources.source',
                'languages.id as language_id',
                'languages.name as language_name',
                'locations.name as location_name',
                'location_states.name as location_state_name',
                DB::raw('(@count:=@count+1) AS serial_number'),
            )
                ->leftjoin('report_sources', 'report_sources.id', 'reports.report_source_id');

            $news = $news->leftjoin('languages', 'reports.language_id', 'languages.id');
            $news = $news->leftjoin('locations', 'reports.location_id', 'locations.id');
            $news = $news->leftjoin('location_states', 'reports.location_state_id', 'location_states.id');

            $news = $news->whereIn('team_id', $teamIds);

            $news = $news->where('reports.sm_calendar_master_id', 0);

            if (count($chapterIds) > 0) {
                $news = $news->whereIn('chapter_id', $chapterIds);
            }

            // $news = $news->with('chapter')->where('chapter_id', $chapterItem->chapter_id)->where('is_deleted', 0);

            $news = $news->where('is_deleted', 0)->where('reports.active', 1);

            if (!empty($from && $to)) {
                $news = $news->whereBetween($dateRangeAttribute, [$from, $to]);
            }

            $news = $news->orderBy('reports.id', $newsOrderByOrder);

            Log::info(
                $news->toSql(),
                $news->getBindings()
            );

            // $news = $news->toSql();
            // dd($news);

            // if ($mode == "view" || $mode == "view-all") {
            if ($mode == "view") {
                $news = $news->paginate(config('app.per_page_reports'))->withQueryString();
            } else {
                $news = $news->get()->toArray();
            }
            // dd($news);
            // dump($news[0]);

        }

        // $keyNews = Paginate::paginate($keyNews,1);
        // dd($keyNews);
        // echo "in";
        // exit();

        $mytime = Carbon\Carbon::now();

        $all_teamArr = array();
        //  dd($dataAllTeamReport);
        foreach ($dataAllTeamReport as $key => $teamData) {
            $all_teamArr[] = str_replace(" ", "", ucwords(strtolower($teamData['team']['name'])));
        }
        $team_name_converted = implode("_", $all_teamArr);
        // dd($team_name_converted);

        if ($team_name_converted == "") {
            $team_name_converted = "all_teams";
        }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {
            $downloadFileName = date('jM') . "_" . $team_name_converted;
        }

        if ($teamName) {
            $downloadFileName = date('jM') . "_" . str_replace(" ", "", ucwords(strtolower($teamName->name)));
        }

        $publicReportColor = "blue";
        if (config('app.color_scheme') == 'pink') {
            $publicReportColor = "red";
        }

        // if($mode == "view-all"){
        //     $viewLayout = "layouts.admin_no_header_footer";        
        //     // $viewLayout = "layouts.admin";        
        // }else{
        $viewLayout = "layouts.admin";
        // }

        // dd($viewLayout);

        $chapterReportData = $this->fetchChapterReportCount();

        $fromPage = "";
        $all_issues = "";
        $all_sm_cals = "";
        $prevDate = "";
        $nextDate = "";
        $selectedDate = "";
        $dailyPopupDate = "";
        $all_count_popup = "";


        if (!empty($from)) {
            $fromPage = $request->fromPage;
            // dd($from);

            $today = Carbon\Carbon::createFromFormat('Y-m-d', $from);
            $dmForDate = $today->format('d-m-Y');
            $dataDM = $this->fetchDailyMonitoring($dmForDate);
            // dd($dataDM);

            $all_issues = $dataDM['all_issues'];
            $all_sm_cals = $dataDM['all_sm_cals'];
            $prevDate = $dataDM['prevDate'];
            $nextDate = $dataDM['nextDate'];
            $selectedDate = $dataDM['selectedDate'];

            $dailyPopupDate = $dataDM['dailyPopupDate'];
            $all_count_popup = $dataDM['all_count_popup'];

            $fromFormated = \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y');

            $reportTitle = $fromFormated . " | Daily Report";
            if (!empty($teamName)) {
                $reportTitle = $fromFormated . " | " . ucwords(strtolower($teamName->name)) . " | Daily Report";
            }
        }
        // dd($data);

        $download_file_title = "";
        $download_file_heading = "";

        $reportParams = [
            'to' => $to,
            'from' => $from,
            'dataAllTeamReport' => $dataAllTeamReport,
            'data' => $data,
            'keyNews' => $news,
            'mytime' => $mytime,
            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading,

            'show_index' => $show_index,
            'show_chapter' => $show_chapter,
            'show_link_text' => $show_link_text,
            'show_link' => $show_link,
            'show_headline' => $show_headline,
            'show_source' => $show_source,
            'show_tags' => $show_tags,
            'show_videolinks' => $show_videolinks,
            'show_keypoints' => $show_keypoints,
            'show_imagesources' => $show_imagesources,
            'show_front_page_screenshots' => $show_front_page_screenshots,
            'show_full_page_screenshots' => $show_full_page_screenshots,
            'show_featuredimages' => $show_featuredimages,
            'show_date' => $show_date,
            'show_fir' => $show_fir,
            'show_documents' => $show_documents,
            'show_language' => $show_language,
            'report_data_type' => $report_data_type,
            'roleId' => $roleId,
            'moduleNameArr' => $moduleNameArr,
            'viewLayout' => $viewLayout,
            'publicReportColor' => $publicReportColor,
            'chapterReportData' => $chapterReportData,
            'find_more' => $find_more,
            // 'otherChapterResults' => $otherChapterResults,
            'module_ids_arr' => $module_ids_arr,
            'team_name' => $request->team_name,

            'fromPage' => $fromPage,

            'all_issues' => $all_issues,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'selectedDate' => $selectedDate,
            'all_sm_cals' => $all_sm_cals,
            'all_count_popup' => $all_count_popup,
            'dailyPopupDate' => $dailyPopupDate,
            'mode' => $mode,
            'currentIssue' => $issue,
            'page' => $page,

            'issueFromUrl' => $issueFromUrl,
        ];

        return $reportParams;
    }

    public function downloadSocialMediaReport($request, $from, $to, $allLocationIds, $mode, $roleId)
    {
        // EXACT SAME LOGIC AS WORK REPORT
        $showReportForUser = "";

        $module_ids_arr = array();
        if ($request->module) {
            $module_ids_arr = $request->module;
        }

        $chapter_ids_arr = array();
        if ($request->chapter_id) {
            $chapter_ids_arr = $request->chapter_id;
        }

        $tName = $request->team_name;

        $download_file_name = $request->download_file_name;
        $download_file_title = $request->download_file_title;
        $download_file_heading = $request->download_file_heading;

        $report_data_type = $request->report_data_type;

        $language_id = array();
        if ($request->language_id) {
            $language_id = $request->language_id;
        }

        $location_id = array();
        if ($request->location_id) {
            $location_id = $request->location_id;
        }

        $fir_documents = 0;
        if ($request->fir_documents) {
            $fir_documents = $request->fir_documents;
        }

        $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
        $moduleName = "";
        $chapterName = "";

        $allTeams = Team::select('id', 'name')->where('active', 1)->get();

        // CHANGE 1: TITLE
        $report_data_type = "social_media";
        $reportTitle = "Team 3 Social Media Report";

        if (!empty($from && $to)) {
            $from = $from . " 00:00:00";
            $to = $to . " 23:59:59";
        }

        $dateRangeAttribute = "reports.created_at";

        $reportType = "pdf";
        if (!empty($request->report_type)) {
            $reportType = $request->report_type;
        }

        // *** FULL ORIGINAL QUERY — NO CHANGES ***
        $socialMediaReport = Team::select(
            'teams.id AS team_id',
            'teams.name as team_name',
            'users.id AS user_id',
            'users.name as user_name',
            DB::Raw('COUNT(reports.id) AS total_reports_by_team_user'),
            DB::Raw('SUM(IF(DATE(reports.`created_at`) = reports.`publish_at`, 1, 0)) AS daily_report_count'),
            DB::Raw('SUM(IF(DATE(reports.`created_at`) != reports.`publish_at`, 1, 0)) AS base_report_count')
        )
            ->join('reports', 'reports.team_id', 'teams.id')
            ->join('users', 'users.id', 'reports.user_id')
            ->where('reports.is_deleted', 0)
            ->where('reports.active', 1)
            ->where('teams.active', 1)
            ->where('reports.sm_calendar_master_id', 1);

        if ($fir_documents == 1) {
            $firDocumentReportIdArr = $this->getFirDocumentReportIds();
            if (count($firDocumentReportIdArr) > 0) {
                $socialMediaReport = $socialMediaReport->wherein('reports.id', $firDocumentReportIdArr);
            }
        }

        if (!empty($from && $to)) {
            $socialMediaReport = $socialMediaReport->whereBetween($dateRangeAttribute, [$from, $to]);
        }

        if (!empty($tName)) {
            $socialMediaReport = $socialMediaReport->where('reports.team_id', $tName);
        }

        if (count($language_id) > 0) {
            $socialMediaReport = $socialMediaReport->wherein('reports.language_id', $language_id);
        }

        if (count($location_id) > 0) {
            $socialMediaReport = $socialMediaReport->wherein('reports.location_id', $location_id);
        }

        if (!empty($allLocationIds)) {
            $locationIdsArr = explode(',', $allLocationIds);
            $socialMediaReport = $socialMediaReport->whereIn('reports.location_id', $locationIdsArr);
        }

        if (count($module_ids_arr) > 0) {
            $socialMediaReport = $socialMediaReport->wherein('reports.module_id', $module_ids_arr);
        }

        if (count($chapter_ids_arr) > 0) {
            $socialMediaReport = $socialMediaReport->wherein('reports.chapter_id', $chapter_ids_arr);
        }

        if (!empty($showReportForUser)) {
            $socialMediaReport = $socialMediaReport->where('user_id', $showReportForUser);
        }

        $socialMediaReport = $socialMediaReport->groupby('teams.id', 'users.id');


        //      $sql = vsprintf(
        //     str_replace('?', "'%s'", $socialMediaReport->toSql()),
        //     $socialMediaReport->getBindings()
        // );
        // dd($sql);

        $socialMediaReport = $socialMediaReport->get();

        if ($mode == "getcount") {
            return count($socialMediaReport);
        }

        $mytime = Carbon\Carbon::now();

        // CHANGE 2: file suffix
        $team_name_converted = "_social_media_report";
        if (!empty($tName)) {
            $team_name_converted = "_" . strtolower(str_replace(" ", "_", $teamName->name));
        }

        if (!empty($download_file_name)) {
            $downloadFileName = $download_file_name;
        } else {
            $downloadFileName = date('jSF') . $team_name_converted;
        }

        $reportParams = [
            'allTeams' => $allTeams,
            'to' => $to,
            'from' => $from,
            'report_data_type' => $report_data_type,
            'socialMediaReport' => $socialMediaReport, // KEEP SAME VARIABLE
            'mytime' => $mytime,
            'reportType' => $reportType,
            'moduleName' => $moduleName,
            'teamName' => $teamName,
            'chapterName' => $chapterName,
            'reportTitle' => $reportTitle,
            'reportDescription' => $download_file_title,
            'reportHeading' => $download_file_heading
        ];

        if ($mode == "view") {
            // CHANGE 3: Social media blade
            echo view('report.webview_social_media_report', $reportParams)->render();
            exit();
        }

        if ($reportType == "word") {
            $view_content_word = View::make('report.wordview_social_media_report', $reportParams)->render();
            $view_content_word = str_replace("&amp;", "and", $view_content_word);
            $view_content_word = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_content_word);

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->setDefaultFontName('Hind');
            $phpWord->setDefaultFontSize(11);

            \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_content_word, true);

            $fileName = $downloadFileName . '.docx';
            \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')
                ->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));

            return $fileName;
        }

        if ($reportType == "googledoc") {
            $view_contents_googledoc = View::make('report.googledoc_social_media_report', $reportParams)->render();
            $view_contents_googledoc = str_replace("&amp;", "and", $view_contents_googledoc);
            $view_contents_googledoc = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $view_contents_googledoc);

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->setDefaultFontName('Hind');
            $phpWord->setDefaultFontSize(11);

            \PhpOffice\PhpWord\Shared\Html::addHtml($phpWord->addSection(), $view_contents_googledoc, true);

            $fileName = $downloadFileName . '.docx';
            \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')
                ->save(storage_path(config('app.download_report_base_folder') . "/" . $fileName));

            return $fileName;
        }

        $view_content_mpdfview = View::make('report.mpdfview_social_media_report', $reportParams)->render();

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($view_content_mpdfview);

        $fileName = $downloadFileName . '.pdf';
        $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');

        return $fileName;
    }
}
