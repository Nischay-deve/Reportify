<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmCalendar;
use App\Models\IncidenceCalendar;
use DateTime;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\RoleUser;
use App\Models\TdtySavedReport;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\CommonMethodsTraits;
use Dompdf\Dompdf;
use Mpdf\Mpdf;
use Exception;
use View;
use App\Models\Chapter;
use App\Models\Module;
use App\Models\Team;

class DailyMonitoringController extends Controller
{
  use CommonMethodsTraits;

  public function index(Request $request, $slug, $date = "")
  {
    // if($date == "daily-monitoring-report"){
    //     $date = "";
    // }

    // dd($date);

    $data = $this->fetchDailyMonitoring($date);

    $all_issues = $data['all_issues'];
    $all_sm_cals = $data['all_sm_cals'];
    $prevDate = $data['prevDate'];
    $nextDate = $data['nextDate'];
    $selectedDate = $data['selectedDate'];

    $dailyPopupDate = $data['dailyPopupDate'];
    $all_count_popup = $data['all_count_popup'];

    $imageSize = $request->query('image_size', '100'); // Default to 100% if not specified


    return view('dailymonitoring.index', compact(
      'all_issues',
      'prevDate',
      'nextDate',
      'selectedDate',
      'all_sm_cals',
      'all_count_popup',
      'dailyPopupDate',
      'imageSize'
    ));
  }

  public function sm_index(Request $request, $slug, $date = "")
  {

    $imageSize = $request->query('image_size', '100'); // Default to 100% if not specified

    
    $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
    // dump($websiteIds);

    $data = $this->fetchDailyMonitoring($date);
    // dd($data);

    $all_issues = $data['all_issues'];
    $all_sm_cals = $data['all_sm_cals'];
    $prevDate = $data['prevDate'];
    $nextDate = $data['nextDate'];
    $selectedDate = $data['selectedDate'];
    $selectedDate = $data['selectedDate'];
    $calendar_date_db = "";
    if (!empty($selectedDate)) {
      $dateTime = DateTime::createFromFormat('d-m-Y', $selectedDate);
      $calendar_date_db = $dateTime->format('Y-m-d');
      $loadingForYear = $dateTime->format('Y');
    }
    // dd($calendar_date_db);

    // $date_condition_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m-%d' ) = '" . $calendar_date_db . "' ";

    $date_condition_sm_cal = " AND reports.publish_at = '" . $calendar_date_db . "' ";

    $all_items_sm_cal = DB::connection('setfacts')->select("
        SELECT 'sm_cal' as type, allItems.publish_at, allItems.heading, allItems.description,
         allItems.link, allItems.image, allItems.calendar_year, allItems.website, allItems.id, allItems.team_id
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
              ) as calendar_year
            FROM
            sm_calendar_masters
            join websites on websites.id = sm_calendar_masters.website_id         
            , reports
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

      

    return view('dailymonitoring.sm_index', compact(
      'all_issues',
      'prevDate',
      'nextDate',
      'selectedDate',
      'all_items_sm_cal',
      'imageSize'
    ));
  }


  public function sm_download_index(Request $request, $slug)
  {
    $date = $request->query('date');
    // dd($date);
    $reportType = $request->query('download_report_type');

    $imageSize = $request->query('image_size', '100'); // Default to 100% if not specified


    $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
    // dump($websiteIds);

    $data = $this->fetchDailyMonitoring($date);
    // dd($data);

    $all_issues = $data['all_issues'];
    $all_sm_cals = $data['all_sm_cals'];
    $prevDate = $data['prevDate'];
    $nextDate = $data['nextDate'];
    $selectedDate = $data['selectedDate'];
    $selectedDate = $data['selectedDate'];
    $calendar_date_db = "";
    if (!empty($selectedDate)) {
      $dateTime = DateTime::createFromFormat('d-m-Y', $selectedDate);
      $calendar_date_db = $dateTime->format('Y-m-d');
      $loadingForYear = $dateTime->format('Y');
    }
    // dd($calendar_date_db);

    // $date_condition_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m-%d' ) = '" . $calendar_date_db . "' ";

    $date_condition_sm_cal = " AND reports.publish_at = '" . $calendar_date_db . "' ";

    $all_items_sm_cal = DB::connection('setfacts')->select("
        SELECT 'sm_cal' as type, allItems.publish_at, allItems.heading, allItems.description,
         allItems.link, allItems.image, allItems.calendar_year, allItems.website, allItems.id, allItems.team_id
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
              ) as calendar_year
            FROM
            sm_calendar_masters
            join websites on websites.id = sm_calendar_masters.website_id         
            , reports
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

    $reportParams = [
      'all_issues' => $all_issues,
      'prevDate' => $prevDate,
      'nextDate' => $nextDate,
      'selectedDate' => $selectedDate,
      'all_items_sm_cal' => $all_items_sm_cal,  
      'imageSize' => $imageSize
    ];


    $downloadFileName = date('jSF') . "_social_media_report";

    if ($reportType == "word") {
      ///////////////////////////////
      /// Below is to create WORD
      ///////////////////////////////

      $view_content_word = View::make('dailymonitoring.sm_index_download_doc', $reportParams)->render();
      // echo($view_content_word);
      // exit();
      // dd($view_content_word);

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

    } elseif ($reportType == "googledoc") {
      ///////////////////////////////
      /// Below is to create WORD
      ///////////////////////////////

      $view_contents_googledoc = View::make('dailymonitoring.sm_index_download_doc', $reportParams)->render();

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

    } else {

      $mpdf = new \Mpdf\Mpdf();


      //write header
      $view_content = View::make('dailymonitoring.sm_index_download', $reportParams)->render();
      $mpdf->WriteHTML($view_content);

      $fileName = $downloadFileName . '.pdf';
      $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
    }

    return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));

  }

  
      ///////////////////////////
    // From Report Controller
    //////////////////////////
    public function pdf(Request $request)
    {
        // dump($request->all());
        // center_website
        if(!empty($request->center_website)){
            // $this->loadCenterWebsiteFromRequest($request->center_website);
            $websiteIdsShowOnly = implode(",",$request->center_website);
            Session::put('website_id_databse_search', $websiteIdsShowOnly);
        }
  
        $websiteIds = $this->getWebsiteIdsFromSessionForDatabaseSearch();
        
        // data loaded from View Composer
        $mode = "download";

        $defaultTeam = DB::connection('setfacts')->select(DB::raw("SELECT teams.id, teams.name, teams.slug, COUNT(reports.id) AS total_reports FROM `teams` 
        INNER JOIN `reports` ON `reports`.`team_id` = `teams`.`id` where teams.active=1 and teams.website_id in (".$websiteIds.") and teams.is_public=1 GROUP BY `teams`.`id`"));

        // dump($defaultTeam);

        $defaultTeamId=0;
        $defaultModuleId=0;
        $categoriesData=array();
        $subCategoriesData=array();

        return view('dailymonitoring.pdf', compact('mode','defaultTeamId','defaultModuleId','categoriesData','subCategoriesData'));
    }

  public function pdfview(Request $request, $slug)
  {
      if(empty(session()->get('website_id'))){
          $this->loadCenter($slug);
      }

      // dd($request->all());
      
      $tName = $request->team_name;

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

      $teamName = "";
      if (!empty($tName)) {
          $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
      }
      $moduleName = "";
      $chapterName = "";

      $allTeams = Team::select('id', 'name')->where('active', 1)->get();


      $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
      // dump($websiteIds);
  
      $condition_sm_cal = "";
      if (!empty($tName)) {
        $condition_sm_cal .= " AND reports.team_id = ".$tName;        
      }

      $moduleIds = "";
      if (count($module_ids_arr) > 0) {
        $moduleIds = implode(",", $module_ids_arr);
        $condition_sm_cal .= " AND  reports.module_id in (" . $moduleIds . ") ";        
      }

      $chapterIds = "";
      if (count($chapter_ids_arr) > 0) {
        $chapterIds = implode(",", $chapter_ids_arr);
        $condition_sm_cal .= " AND  reports.chapter_id in (" . $chapterIds . ") ";                
      }
      
      $all_items_sm_cal = DB::connection('setfacts')->select("
          SELECT 'sm_cal' as type, allItems.publish_at, allItems.heading, allItems.description,
           allItems.link, allItems.image, allItems.calendar_year, allItems.website, allItems.id, allItems.team_id
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
                ) as calendar_year
              FROM
              sm_calendar_masters
              join websites on websites.id = sm_calendar_masters.website_id         
              , reports
              WHERE 
              sm_calendar_masters.id = reports.sm_calendar_master_id and
              sm_calendar_masters.active = 1 and
              sm_calendar_masters.website_id in (" . $websiteIds . ") and                        
              reports.id IS NOT NULL and 
              reports.active = 1 and             
              ISNULL(reports.deleted_at)
              " . $condition_sm_cal . "
          ) AS allItems  
          ORDER BY id        
        ");
  
  
      return view('dailymonitoring.sm_database_index', compact(  
        'all_items_sm_cal',
        'teamName',
        'tName', 'moduleIds','chapterIds'

      ));

  }

  
  public function downloadDatabase(Request $request, $slug)
  {
    // dd($request->all());
    
    $reportType = $request->query('download_report_type');

    $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
    // dump($websiteIds);

    $tName = $request->tName;

    $moduleIds = "";
    if ($request->moduleIds) {
        $moduleIds = $request->moduleIds;
    }

    $chapterIds = "";
    if ($request->chapterIds) {
        $chapterIds = $request->chapterIds;
    }


    $teamName = "";
    if (!empty($tName)) {
        $teamName = Team::select('name')->where('id', $tName)->where('active', 1)->first();
    }

    $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
    // dump($websiteIds);

    $condition_sm_cal = "";
    if (!empty($tName)) {
      $condition_sm_cal .= " AND reports.team_id = ".$tName;        
    }

    if ($moduleIds != "") {
      $condition_sm_cal .= " AND  reports.module_id in (" . $moduleIds . ") ";        
    }

    if ($chapterIds != "") {
      $condition_sm_cal .= " AND  reports.chapter_id in (" . $chapterIds . ") ";                
    }

    // dd($condition_sm_cal);

    $all_items_sm_cal = DB::connection('setfacts')->select("
        SELECT 'sm_cal' as type, allItems.publish_at, allItems.heading, allItems.description,
         allItems.link, allItems.image, allItems.calendar_year, allItems.website, allItems.id, allItems.team_id
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
              ) as calendar_year
            FROM
            sm_calendar_masters
            join websites on websites.id = sm_calendar_masters.website_id         
            , reports
            WHERE 
            sm_calendar_masters.id = reports.sm_calendar_master_id and
            sm_calendar_masters.active = 1 and
            sm_calendar_masters.website_id in (" . $websiteIds . ") and                        
            reports.id IS NOT NULL and 
            reports.active = 1 and             
            ISNULL(reports.deleted_at)
            " . $condition_sm_cal . "
        ) AS allItems  
        ORDER BY id        
      ");

      // dd($all_items_sm_cal);

    $reportParams = [
      'all_items_sm_cal' => $all_items_sm_cal,
      'teamName' => $teamName,
    ];

    $downloadFileName = date('jSF') . "_social_media_report";

    if ($reportType == "word") {
      ///////////////////////////////
      /// Below is to create WORD
      ///////////////////////////////

      $view_content_word = View::make('dailymonitoring.sm_index_database_download_doc', $reportParams)->render();
      // echo($view_content_word);
      // exit();
      // dd($view_content_word);

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

    } elseif ($reportType == "googledoc") {
      ///////////////////////////////
      /// Below is to create WORD
      ///////////////////////////////

      $view_contents_googledoc = View::make('dailymonitoring.sm_index_database_download_doc', $reportParams)->render();

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

    } else {

      $mpdf = new \Mpdf\Mpdf();


      //write header
      $view_content = View::make('dailymonitoring.sm_index_database_download', $reportParams)->render();
      $mpdf->WriteHTML($view_content);

      $fileName = $downloadFileName . '.pdf';
      $mpdf->Output(storage_path(config('app.download_report_base_folder') . "/" . $fileName), 'F');
    }

    return response()->download(storage_path(config('app.download_report_base_folder') . "/" . $fileName));

  }

  public function fetchDownloadOptions(Request $request)
  {
      $mode = '';
      if ($request->mode) {
          $mode = $request->mode;
      }
      
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

          if (empty($from)) {
              $from = $to;
          }

          if (empty($to)) {
              $to = $from;
          }

          // $from = $from . " 00:00:00";
          // $to = $to . " 23:59:59";                        
          // echo $from."  ".$to;

          $date_reports = SmCalendar::select(DB::connection('setfacts')->Raw('COUNT(reports.id) as total_reports'))
              ->wherenull('reports.deleted_at')
              ->where('reports.active', 1)              
              ->whereBetween('reports.publish_at', [$from, $to]);

          $total_reports = SmCalendar::select(
              DB::connection('setfacts')->Raw('COUNT(reports.id) as total_reports')
          )->wherenull('reports.deleted_at')
              ->where('reports.active', 1)              
              ->whereBetween('reports.publish_at', [$from, $to]);

          if ($request->team_id) {
              $total_reports = $total_reports->where('reports.team_id', $request->team_id);
              $date_reports = $date_reports->where('reports.team_id', $request->team_id);
          }

          if ($request->module_ids) {
              $moduleIdArr = explode(",", $request->module_ids);
              $total_reports = $total_reports->wherein('reports.module_id', $moduleIdArr);
              $date_reports = $date_reports->wherein('reports.module_id', $moduleIdArr);
          }

          if ($request->chapter_ids) {
              $chapterIdArr = explode(",", $request->chapter_ids);
              $total_reports = $total_reports->wherein('reports.chapter_id', $chapterIdArr);
              $date_reports = $date_reports->wherein('reports.chapter_id', $chapterIdArr);             
          }

          // $date_reports = $date_reports->toSql();
          // dd($date_reports);

          $date_reports = $date_reports->get();
        
          // $total_reports = $total_reports->toSql();
          // dd($total_reports);            
          $total_reports = $total_reports->get();
          $data['total_reports'] = $total_reports;

          $data['date_reports'] = $date_reports;         
          return response()->json($data);
      } else {

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
      }

      if ($request->team_id) {
          $condition = " 1=1 ";
          $resultsForTeamId = DB::connection('setfacts')->select("SELECT modules.id, modules.name, modules.slug as module_slug, teams.slug as team_slug, COUNT(reports.id) AS total_reports FROM `modules` 
          INNER JOIN reports ON reports.`module_id` = `modules`.`id` and ISNULL( reports.deleted_at) and  reports.active = 1 and " . $condition . "
          INNER JOIN `chapters` ON reports.`chapter_id` = `chapters`.`id` and ISNULL( chapters.deleted_at) and chapters.active=1
          INNER JOIN `teams` ON `teams`.`id` = `modules`.`team_id`
          WHERE 
          modules.active = 1 and
          ISNULL( modules.deleted_at) and           
          `modules`.`team_id` = :team_id 
          GROUP BY `modules`.`id`", array(
              'team_id' => $request->team_id,
          ));

          $resultsForModuleId = DB::connection('setfacts')->select("SELECT chapters.id, chapters.name, modules.slug as module_slug, chapters.slug as chapter_slug, teams.slug as team_slug, COUNT(reports.id) AS total_reports FROM `chapters` 
          INNER JOIN reports ON reports.`chapter_id` = `chapters`.`id`  and ISNULL( reports.deleted_at) and  reports.active = 1 and " . $condition . "
          INNER JOIN `modules` ON reports.`module_id` = `modules`.`id` and ISNULL( modules.deleted_at) and modules.active=1
          INNER JOIN `teams` ON `teams`.`id` = `modules`.`team_id`
          WHERE 
          chapters.active = 1 and
          ISNULL( chapters.deleted_at) and         
          `chapters`.`module_id` in (" . $resultsForTeamId[0]->id . ") GROUP BY `chapters`.`id`");   
      }

      if ($request->module_ids) {
          $condition = " 1=1 ";

          $resultsForModuleId = DB::connection('setfacts')->select("SELECT chapters.id, chapters.name, modules.slug as module_slug, chapters.slug as chapter_slug, teams.slug as team_slug, COUNT(reports.id) AS total_reports FROM `chapters` 
          INNER JOIN reports ON reports.`chapter_id` = `chapters`.`id`  and ISNULL( reports.deleted_at) and  reports.active = 1 and " . $condition . "
          INNER JOIN `modules` ON reports.`module_id` = `modules`.`id` and ISNULL( modules.deleted_at) and modules.active=1
          INNER JOIN `teams` ON `teams`.`id` = `modules`.`team_id`

          WHERE 
          chapters.active = 1 and
          ISNULL( chapters.deleted_at) and         
          `chapters`.`module_id` in (" . $request->module_ids . ") GROUP BY `chapters`.`id`");
      }

      $total_reports = SmCalendar::select(
          DB::connection('setfacts')->Raw('COUNT(reports.id) as total_reports')
      )->wherenull('reports.deleted_at')->where('reports.active', 1);

      if ($request->team_id) {
          $total_reports = $total_reports->where('reports.team_id', $request->team_id);
      }

      if ($request->module_ids) {
          $moduleIdArr = explode(",", $request->module_ids);
          $total_reports = $total_reports->wherein('reports.module_id', $moduleIdArr);
      }
      
      if ($request->chapter_ids) {
          $chapterIdArr = explode(",", $request->chapter_ids);
          $total_reports = $total_reports->wherein('reports.chapter_id', $chapterIdArr);
      }

      if (!empty($from) && !empty($to)) {
          $total_reports = $total_reports->whereBetween('reports.publish_at', [$from, $to]);
      }

      //request TeamId
      if (($mode == 'modules') && ($request->team_id)) {
          $data['modules'] = $resultsForTeamId;
          $data['chapters'] = $resultsForModuleId;
      }        

      //request module_ids
      if (($mode == 'chapters') && ($request->module_ids)) {
          $data['chapters'] = $resultsForModuleId;
      }                

      // $total_reports = $total_reports->toSql();
      // dd($total_reports);

      $total_reports = $total_reports->get();
      
      // dd($total_reports);
      $data['total_reports'] = $total_reports;

      return response()->json($data);
  }
}
