<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
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
use App\Traits\CommonMethodsTraits;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use finfo;
use App\Traits\MediaManager;
use Illuminate\Support\Facades\Session;
use Carbon;

class CalendarController extends Controller
{
  use CommonMethodsTraits, MediaManager;

  public function loadDefaultCenter(){
    if(!empty(session()->get('website_slug'))){
      $slug = session()->get('website_slug');
    }else{
      $slug = "team3";    
    }    
    return redirect()->route('calendar.index', $slug);
  }

  public function loadCenter($slug){
    // dd("in");
    if(empty($slug)){
      $slug = "team3";
    }

    // dump($slug);
    $centerData = DB::connection('setfacts')->select("select * from websites where domain = '".$slug."'");
    // dd($centerData[0]);
    if($centerData){
      // echo "Website Found";

      $arr = array($centerData[0]->id);
      $this->loadCenterWebsiteFromRequest($arr);

      // Session::put('website_id', $centerData[0]->id);
    }else{
      // echo "Invalid Website";
      Session::put('website_id', 1);
      Session::put('website_slug', 'team3');
    }
    // dd($centerData);
  }

  // Helper function to group and sum items
  private function groupAndSumItems($items) {
    $grouped = [];
    foreach ($items as $item) {
        $key = $item->calendar_date . '_' . $item->cal_type . '_' . $item->calendar_year;
        
        if (!isset($grouped[$key])) {
            $grouped[$key] = (object) [
                'cal_type' => $item->cal_type,
                'totalEvents' => (int)$item->totalEvents,
                'calendar_date' => $item->calendar_date,
                'calendar_year' => $item->calendar_year,
                'website' => $item->website,
                'description' => $item->description
            ];
        } else {
            $grouped[$key]->totalEvents += (int)$item->totalEvents;
            $grouped[$key]->description .= '<hr>' . $item->description;
        }
    }
    return array_values($grouped);
  }


  public function index(Request $request, $slug="", $monitoring_date = "")
  {
    $imageSize = $request->query('image_size', '100'); // Default to 100% if not specified

    $this->loadCenter($slug);

    $today = Carbon\Carbon::today()->toDateString();
    $nextDay = Carbon\Carbon::today()->addDays(1)->toDateString();
    
    // dd($request->all());
    // center_website
    if(!empty($request->center_website)){
      // $this->loadCenterWebsiteFromRequest($request->center_website);
      $websiteIdsShowOnly = implode(",",$request->center_website);
      Session::put('website_id_calendar', $websiteIdsShowOnly);
    }

    if(Session::missing('website_id')){
      // Session::put('website_id', 0);
      Session::put('website_id', 1);
    }

    if(Session::missing('website_id_calendar')){
      // Session::put('website_id_calendar', 0);
      Session::put('website_id', 1);
    }
    
    if(Session::missing('website_id_databse_search')){
      // Session::put('website_id_databse_search', 0);
      Session::put('website_id', 1);
    }    

    if(Session::missing('website_slug')){
      Session::put('website_slug', 'team3');
    }

    // dd(session()->get('website_slug'));

    $websiteIds = $this->getWebsiteIdsFromSessionForCalendar();
    // dd($websiteIds);

    $allItemsSearch = "SELECT
        all_search_items.id,
        all_search_items.title,
        all_search_items.tableName
        FROM
        (
            (SELECT
            teams.id AS id,
            teams.`name` AS title,
            'teams' AS tableName
            FROM
            teams
            WHERE teams.`active` = 1
            AND teams.`is_public` = 1
            AND teams.`tdty_search` = 1
              AND teams.`deleted_at` IS NULL
            and teams.website_id in (".$websiteIds.") 
            )
            UNION
            (SELECT
            modules.id AS id,
            modules.`name` AS title,
            'modules' AS tableName
            FROM
            modules
            WHERE modules.`active` = 1
            AND modules.`tdty_search` = 1
            AND modules.`deleted_at` IS NULL
            and modules.website_id in (".$websiteIds.") 
            )
            UNION
            (SELECT
            sm_calendar_masters.id AS id,
            sm_calendar_masters.`title` AS title,
            'sm_calendar_masters' AS tableName
            FROM
            sm_calendar_masters
            WHERE sm_calendar_masters.`active` = 1
            AND sm_calendar_masters.`tdty_search` = 1
            AND sm_calendar_masters.website_id in (".$websiteIds.") 
            AND sm_calendar_masters.`deleted_at` IS NULL)
        ) AS all_search_items
        ORDER BY all_search_items.tableName DESC,
        all_search_items.id ASC";

    $all_items_search = DB::connection('setfacts')->select($allItemsSearch);
    // dd($all_items_search);



    $calendar_date = $request->date;
    $type = $request->type;
    $eventYear = $request->eventYear;
    $selected_month = $request->selected_month;
    $selected_week = $request->selected_week;

    $keywords = array();
    if(!empty($request->keywords)){
      if(is_array($request->keywords)){
        $keywords = $request->keywords;
      }else{
        $keywords = explode(",",$request->keywords);
      }
    }
    
    $condition_info_sm_cal_arr = array();
    $condition_info_incidence_arr = array();
    $condition_info_focus_arr = array();

    $condition_info_sm_cal = "";
    $condition_info_incidence = "";
    $condition_info_focus = "";
    $condition_focus_teams="";
    $condition_focus_modules="";  

    $keywordType=array();

    $isFocusSearch=false;
    $teamIdsForFocus = array();
    $moduleIdsForFocus = array();
    
    if(in_array("Document", $keywords)){
      $isFocusSearch = true;
      $keywordType[]="Document";
    }

    
    foreach($keywords as $keyword){

      if (!empty($keyword)) {
         
         $keywordTable = "";
         $keywordTableId = "";         
         foreach($all_items_search as $searchKeywordData){
            if(ucwords(strtolower($keyword)) == ucwords(strtolower($searchKeywordData->title))){
              $keywordTable = $searchKeywordData->tableName;
              $keywordTableId = $searchKeywordData->id;

              $keywordType[]= $keywordTable;
            }
         }

         if($keywordTable == "teams"){
            // $condition_info_incidence_arr[] = "(teams.id = '.$keywordTableId.' )";  

            $condition_info_incidence_arr[] = "(teams.id = '".$keywordTableId."' OR incidence_calendars.heading like '%" . $keyword . "%' OR incidence_calendars.description like '%" . $keyword . "%' OR teams.name like '%" . $keyword . "%' )";
            
            $condition_info_sm_cal_arr[] = "(reports.team_id = '".$keywordTableId."' OR reports.heading like '%" . $keyword . "%' OR reports.description like '%" . $keyword . "%' )";

            if($isFocusSearch){
              $teamIdsForFocus[]=$keywordTableId;              
            }
         }

         if($keywordTable == "modules"){
          // $condition_info_incidence_arr[] = "(teams.id = '.$keywordTableId.' )";  

          $condition_info_incidence_arr[] = "(reports.module_id = '".$keywordTableId."')";
          
          $condition_info_sm_cal_arr[] = "(reports.module_id = '".$keywordTableId."')";

          if($isFocusSearch){
            $moduleIdsForFocus[]=$keywordTableId;
          }
        }

        //  dd($condition_info_incidence_arr, $condition_info_sm_cal_arr);
          

         if($keywordTable == "sm_calendar_masters"){
            // $condition_info_sm_cal_arr[] = "(sm_calendar_masters.id = '.$keywordTableId.' )";  

            $condition_info_sm_cal_arr[] = "(sm_calendar_masters.id = '".$keywordTableId."' OR reports.heading like '%" . $keyword . "%' OR reports.description like '%" . $keyword . "%' )";

            // if($isFocusSearch){
            //   $teamIdsForFocus[]=$keywordTableId;
            // }
         }         

        // $condition_info_sm_cal_arr[] = "(reports.heading like '%" . $keyword . "%' OR reports.description like '%" . $keyword . "%' )";
        // $condition_info_incidence_arr[] = "(incidence_calendars.heading like '%" . $keyword . "%' OR incidence_calendars.description like '%" . $keyword . "%' OR teams.name like '%" . $keyword . "%' )";
        
        if($isFocusSearch){
          // Church  10
          // Islam   11
          // Rising Bharat   1
          // Women Empowerment 3
          // Temple 6
          // China   2
          
          // if($keyword == "Church"){
          //   $teamIdsForFocus[]=10;
          // }
          // if($keyword == "Islam"){
          //   $teamIdsForFocus[]=11;
          // }
          // if($keyword == "Rising Bharat"){
          //   $teamIdsForFocus[]=1;
          // }
          // if($keyword == "Women Empowerment"){
          //   $teamIdsForFocus[]=3;
          // }
          // if($keyword == "Temple"){
          //   $teamIdsForFocus[]=6;
          // }
          // if($keyword == "China"){
          //   $teamIdsForFocus[]=2;
          // }                                                  


        }else{
          if($keyword != "Document"){
            $condition_info_focus_arr[] = "(documents.title like '%" . $keyword . "%' OR documents.description like '%" . $keyword . "%' )";
          }          
        }
      }

    }

    if(count($condition_info_sm_cal_arr) > 0){
      $condition_info_sm_cal = implode(" OR ", $condition_info_sm_cal_arr);
      $condition_info_sm_cal = rtrim($condition_info_sm_cal, ' OR');
      $condition_info_sm_cal = " AND (".$condition_info_sm_cal." )";
    }
    if(count($condition_info_incidence_arr) > 0){
      $condition_info_incidence = implode(" OR ", $condition_info_incidence_arr);
      $condition_info_incidence = rtrim($condition_info_incidence, ' OR');
      $condition_info_incidence = " AND (".$condition_info_incidence." )";
    }
    if(count($condition_info_focus_arr) > 0){
      $condition_info_focus = implode(" OR ", $condition_info_focus_arr);
      $condition_info_focus = rtrim($condition_info_focus, ' OR');
      $condition_info_focus = " AND (".$condition_info_focus." )";
    }

    $condition_focus_teams="";
    if($isFocusSearch && count($teamIdsForFocus) > 0){
      $condition_focus_teams = " and documents.team_id in (".implode(",", $teamIdsForFocus).")";
    }

    $condition_focus_modules="";
    if($isFocusSearch && count($moduleIdsForFocus) > 0){
      $condition_focus_modules = " and documents.module_id in (".implode(",", $moduleIdsForFocus).")";
    } 

    // dd($condition_info_focus);

    $all_items_focus_sql = "SELECT allItems.cal_type, SUM(allItems.totalEvents) AS totalEvents,  allItems.calendar_date,
    allItems.calendar_year, allItems.website, allItems.description
    FROM
    (
          (
              SELECT
              websites.domain AS website,
              \"document\" AS cal_type,
              COUNT(documents.id) AS totalEvents,
              group_concat(TRIM(REPLACE(REPLACE(REPLACE(documents.description, '\n', ' '), '\r', ' '), '\t', ' ')) SEPARATOR '<hr>') as description,
              DATE_FORMAT(documents.date, '%m-%d') AS calendar_date,
              DATE_FORMAT(documents.date, '%Y') AS calendar_year
            FROM
              documents
              join websites on websites.id = documents.website_id
            WHERE             
            documents.active = 1              
              and documents.is_deleted = 0 and ISNULL(documents.deleted_at) and
            documents.website_id in (".$websiteIds.")  
            " . $condition_focus_teams . "   
            " . $condition_focus_modules . "   
            " . $condition_info_focus . "   
            GROUP BY 
            DATE_FORMAT(documents.date, '%m-%d')
          )
      ) AS allItems
      GROUP BY allItems.calendar_date
      ORDER BY  allItems.calendar_date
    ";

    $all_items_focus = DB::connection('setfacts')->select($all_items_focus_sql);
    //  echo $all_items_focus_sql;
    //  exit();

    $all_items_incidence_sql = "SELECT allItems.cal_type, SUM(allItems.totalEvents) AS totalEvents,  allItems.calendar_date,
        allItems.calendar_year, allItems.website, allItems.description
        FROM
        (             
           
          SELECT
          websites.domain AS website,
          \"other\" AS cal_type,
          COUNT(incidence_calendars.id) AS totalEvents,
          group_concat(TRIM(REPLACE(REPLACE(REPLACE(reports.heading, '\n', ' '), '\r', ' '), '\t', ' ')) SEPARATOR '<hr>') as description,                  
          DATE_FORMAT(
            incidence_calendars.date,
            '%m-%d'
          ) AS calendar_date,
          DATE_FORMAT(
            incidence_calendars.date,
            '%Y'
          ) AS calendar_year                
        FROM
          incidence_calendars
          INNER JOIN reports
            ON incidence_calendars.report_id = reports.id
          INNER JOIN teams
            ON reports.team_id = teams.id         
          join websites on websites.id = reports.website_id         
        WHERE 1=1
          AND incidence_calendars.active = 1
          AND incidence_calendars.website_id in (".$websiteIds.")  
          AND reports.is_deleted = 0
          AND reports.active = 1
          AND reports.website_id in (".$websiteIds.")  
          " . $condition_info_incidence . "
          #GROUP BY DATE_FORMAT(            incidence_calendars.date,            '%m-%d'          )
          GROUP BY DATE_FORMAT(incidence_calendars.date, '%Y'),    DATE_FORMAT(incidence_calendars.date, '%m-%d')
            
          ) AS allItems
          GROUP BY allItems.cal_type, allItems.calendar_year, allItems.calendar_date
          ORDER BY  allItems.cal_type, allItems.calendar_date, allItems.calendar_year DESC           
        ";

    // echo $all_items_incidence_sql;
    // exit();

    $all_items_incidence = DB::connection('setfacts')->select($all_items_incidence_sql);

    ///////////////////////////

    $all_items_sm_sql = "SELECT allItems.cal_type, SUM(allItems.totalEvents) AS totalEvents,  allItems.calendar_date,
    allItems.calendar_year, allItems.website, allItems.description
    FROM
    (                   
        SELECT
        websites.domain AS website,
        \"other\" AS cal_type,
        COUNT(reports.id) AS totalEvents,
        group_concat(TRIM(REPLACE(REPLACE(REPLACE(reports.description, '\n', ' '), '\r', ' '), '\t', ' ')) SEPARATOR '<hr>') as description,

        DATE_FORMAT(reports.publish_at, '%m-%d') AS calendar_date,
        DATE_FORMAT(reports.publish_at, '%Y') AS calendar_year
      FROM
        sm_calendar_masters
        join websites on websites.id = sm_calendar_masters.website_id
        ,reports
      WHERE 
      sm_calendar_masters.id = reports.sm_calendar_master_id and
      sm_calendar_masters.active = 1 and
      sm_calendar_masters.website_id in (".$websiteIds.")   and 
      reports.active = 1 and 
      reports.for_calendar = 1 and ISNULL(reports.deleted_at)
      " . $condition_info_sm_cal . "
      GROUP BY 
      DATE_FORMAT(reports.publish_at, '%Y'),
      DATE_FORMAT(reports.publish_at, '%m-%d')              
       
      ) AS allItems
      GROUP BY allItems.cal_type, allItems.calendar_year, allItems.calendar_date
      ORDER BY  allItems.cal_type, allItems.calendar_date, allItems.calendar_year DESC           
    ";

    // echo $all_items_sm_sql;
    // exit();

    $all_items_sm = DB::connection('setfacts')->select($all_items_sm_sql);
        
    $keywordTypeFinal = array_unique($keywordType);
    // dd($keywordTypeFinal);
    Log::info("keywordTypeFinal ". print_r($keywordTypeFinal, true));

    // if (count($keywords) == 0) {
    //     Log::info("count(keywords) == 0 = all_items show = documents + incidence + smcal");
    //     $all_items = array_merge($all_items_focus, $all_items_incidence, $all_items_sm);   
    // } else if (count($keywords) > 0  && in_array("sm_calendar_masters", $keywordTypeFinal) ) {
    //     Log::info("count(keywords) > 0 && sm_calendar_masters searched = all_items show = sm_calendar_masters");
    //     Log::info("all_items_sm_sql ". $all_items_sm_sql);
    //     $all_items = array_merge($all_items_sm);
    // } else if (count($keywords) > 0  && in_array("Document", $keywordTypeFinal) ) {
    //     Log::info("count(keywords) > 0 && Document searched = all_items show = documents");
    //     Log::info("all_items_sm_sql ". $all_items_focus_sql);
    //     $all_items = array_merge($all_items_focus);
    // } else {
    //     Log::info("all_items show documents, incidence, and smcal");
    //     $all_items = array_merge($all_items_focus, $all_items_incidence, $all_items_sm);            
    // }       
  
    // In your index method, replace the array_merge with:
    if (count($keywords) == 0) {
      $merged = array_merge($all_items_focus, $all_items_incidence, $all_items_sm);
      $all_items = $this->groupAndSumItems($merged);
    } else if (count($keywords) > 0 && in_array("sm_calendar_masters", $keywordTypeFinal)) {
      $all_items = $this->groupAndSumItems($all_items_sm);
    } else if (count($keywords) > 0 && in_array("Document", $keywordTypeFinal)) {
      $all_items = $this->groupAndSumItems($all_items_focus);
    } else {
      $merged = array_merge($all_items_focus, $all_items_incidence, $all_items_sm);
      $all_items = $this->groupAndSumItems($merged);
    }

    //daily Monitoring code
    $monitoring_data = $this->fetchDailyMonitoring($monitoring_date);

    $all_issues = $monitoring_data['all_issues'];
    // dd($all_issues); 
    
    $all_sm_cals = $monitoring_data['all_sm_cals'];
    $prevDate = $monitoring_data['prevDate'];
    $nextDate = $monitoring_data['nextDate'];
    $selectedDate = $monitoring_data['selectedDate'];
    $dailyPopupDate = $monitoring_data['dailyPopupDate'];
    $all_count_popup = $monitoring_data['all_count_popup'];
    // $nextDate = Carbon\Carbon::tomorrow()->format('d-m-Y');

    // dd($imageSize);
 
    // dump($keywords);
    return view('calendar.index', compact(
      'all_items',
      'keywords',
      'selected_month',
      'selected_week',
      'calendar_date',
      'type',
      'eventYear',
      'all_issues',
      'prevDate',
      'nextDate',
      'selectedDate',
      'all_sm_cals',
      'all_count_popup',
      'dailyPopupDate',
      'today',
      'nextDay',
      'all_items_search',
      'imageSize'
    ));
  }

  public function load(Request $request)
  {
    // dd($request->all());
    $websiteIds = $this->getWebsiteIdsFromSessionForCalendar();
    $allItemsSearch = "SELECT
        all_search_items.id,
        all_search_items.title,
        all_search_items.tableName
        FROM
        (
            (SELECT
            teams.id AS id,
            teams.`name` AS title,
            'teams' AS tableName
            FROM
            teams
            WHERE teams.`active` = 1
            AND teams.`is_public` = 1
            AND teams.`tdty_search` = 1
            AND teams.`deleted_at` IS NULL
            and teams.website_id in (".$websiteIds.") 
            )
            UNION
            (SELECT
            modules.id AS id,
            modules.`name` AS title,
            'modules' AS tableName
            FROM
            modules
            WHERE modules.`active` = 1
            AND modules.`tdty_search` = 1
            AND modules.`deleted_at` IS NULL
            and modules.website_id in (".$websiteIds.") 
            )
            UNION
            (SELECT
            sm_calendar_masters.id AS id,
            sm_calendar_masters.`title` AS title,
            'sm_calendar_masters' AS tableName
            FROM
            sm_calendar_masters
            WHERE sm_calendar_masters.`active` = 1
            AND sm_calendar_masters.`tdty_search` = 1
            AND sm_calendar_masters.website_id in (".$websiteIds.") 
            AND sm_calendar_masters.`deleted_at` IS NULL)
        ) AS all_search_items
        ORDER BY all_search_items.tableName DESC,
        all_search_items.id ASC";

      $all_items_search = DB::connection('setfacts')->select($allItemsSearch);
      // dd($all_items_search);

    $calendar_date = $request->date;     //2022-11-03
    $type = $request->type;
    $eventYear = $request->eventYear;
    $showall = $request->showall;

    $condition_info_sm_cal = "";
    $condition_info_incidence = "";

    $keywords = array();
    if(!empty($request->keywords)){
      $keywords = explode(",",$request->keywords);
    }

    $condition_info_sm_cal_arr = array();
    $condition_info_incidence_arr = array();

    $condition_info_focus_arr = array();
    $condition_info_focus = "";
    $condition_focus_teams="";
    $condition_focus_modules="";  

    $keywordType=array();

    if (count($keywords) > 0) {

      $showall = "yes";
     
      $isFocusSearch=false;
      $teamIdsForFocus = array();
      $moduleIdsForFocus = array(); 

      if(in_array("Document", $keywords)){
        $isFocusSearch = true;
        $keywordType[]="Document";
      }
  
      
      foreach($keywords as $keyword){
  
        if (!empty($keyword)) {
           
           $keywordTable = "";
           $keywordTableId = "";         
           foreach($all_items_search as $searchKeywordData){
              if(ucwords(strtolower($keyword)) == ucwords(strtolower($searchKeywordData->title))){
                $keywordTable = $searchKeywordData->tableName;
                $keywordTableId = $searchKeywordData->id;
  
                $keywordType[]= $keywordTable;
              }
           }
  
           if($keywordTable == "teams"){
              $condition_info_incidence_arr[] = "(teams.id = '".$keywordTableId."' OR incidence_calendars.heading like '%" . $keyword . "%' OR incidence_calendars.description like '%" . $keyword . "%' OR teams.name like '%" . $keyword . "%' )";
              
              $condition_info_sm_cal_arr[] = "(reports.team_id = '".$keywordTableId."' OR reports.heading like '%" . $keyword . "%' OR reports.description like '%" . $keyword . "%' )";
  
              if($isFocusSearch){
                $teamIdsForFocus[]=$keywordTableId;
              }
           }

           if($keywordTable == "modules"){
            // $condition_info_incidence_arr[] = "(teams.id = '.$keywordTableId.' )";  
  
            $condition_info_incidence_arr[] = "(reports.module_id = '".$keywordTableId."')";
            
            $condition_info_sm_cal_arr[] = "(reports.module_id = '".$keywordTableId."')";
  
            if($isFocusSearch){
              $moduleIdsForFocus[]=$keywordTableId;
            }
          }

  
           if($keywordTable == "sm_calendar_masters"){
              $condition_info_sm_cal_arr[] = "(sm_calendar_masters.id = '".$keywordTableId."' OR reports.heading like '%" . $keyword . "%' OR reports.description like '%" . $keyword . "%' )";
           }         
  
          if($isFocusSearch){
 
          }else{
            if($keyword != "Document"){
              $condition_info_focus_arr[] = "(documents.title like '%" . $keyword . "%' OR documents.description like '%" . $keyword . "%' )";
            }          
          }
        }
  
      }


      if(count($condition_info_sm_cal_arr) > 0){
        $condition_info_sm_cal = implode(" OR ", $condition_info_sm_cal_arr);
        $condition_info_sm_cal = rtrim($condition_info_sm_cal, ' OR');
        $condition_info_sm_cal = "  AND (".$condition_info_sm_cal." )";
      }

      if(count($condition_info_incidence_arr) > 0){
        $condition_info_incidence = "  ".implode(" OR ", $condition_info_incidence_arr);
        $condition_info_incidence = rtrim($condition_info_incidence, ' OR');
        $condition_info_incidence = " AND (".$condition_info_incidence." )";
      }     
      
      $calendar_date_db = "";
     
      if($showall == "yes"){
        // month wise
        if (!empty($calendar_date)) {
          $dateTime = DateTime::createFromFormat('Y-m-d', $calendar_date);
          $calendar_date_db = $dateTime->format('m');
          $loadingForYear = $dateTime->format('Y');
        }
        // dd($calendar_date_db);

        $date_condition_info_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m' ) = '" . $calendar_date_db . "' ";
        $date_condition_info_incidence = " AND DATE_FORMAT(incidence_calendars.date,'%m') = '" . $calendar_date_db . "' ";
        $date_condition_info_focus = " AND DATE_FORMAT(documents.date, '%m' ) = '" . $calendar_date_db . "' ";
      } else{
        // date wise
        $calendar_date_db = "";
        if (!empty($calendar_date)) {
          $dateTime = DateTime::createFromFormat('Y-m-d', $calendar_date);
          $calendar_date_db = $dateTime->format('m-d');
          $loadingForYear = $dateTime->format('Y');
        }
  
        $date_condition_info_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m-%d' ) = '" . $calendar_date_db . "' ";
        $date_condition_info_incidence = " AND DATE_FORMAT(incidence_calendars.date,'%m-%d') = '" . $calendar_date_db . "' ";
        $date_condition_info_focus = " AND DATE_FORMAT(documents.date, '%m-%d' ) = '" . $calendar_date_db . "' ";        
      }

      if(count($condition_info_focus_arr) > 0){
        $condition_info_focus = " AND (".$condition_info_focus." )";
      }      

      if($isFocusSearch && count($teamIdsForFocus) > 0){
        $condition_focus_teams = " and documents.team_id in (".implode(",", $teamIdsForFocus).")";
      }      

      $condition_focus_modules="";
      if($isFocusSearch && count($moduleIdsForFocus) > 0){
        $condition_focus_modules = " and documents.module_id in (".implode(",", $moduleIdsForFocus).")";
      }      


    } else {

      $calendar_date_db = "";
      if (!empty($calendar_date)) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $calendar_date);
        $calendar_date_db = $dateTime->format('m-d');
        $loadingForYear = $dateTime->format('Y');
      }

      $date_condition_info_sm_cal = " AND DATE_FORMAT(reports.publish_at, '%m-%d' ) = '" . $calendar_date_db . "' ";
      $date_condition_info_incidence = " AND DATE_FORMAT(incidence_calendars.date,'%m-%d') = '" . $calendar_date_db . "' ";
      $date_condition_info_focus = " AND DATE_FORMAT(documents.date, '%m-%d' ) = '" . $calendar_date_db . "' ";

    }

    // dd($calendar_date_db);
    
    $chapterReportData = $this->fetchChapterReportCount();
    
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
      allItems.chapter_name,
      allItems.report_type
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
          chapters.`name` AS chapter_name,
          reports.report_type               
                        
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
          AND reports.website_id in (".$websiteIds.")  
          " . $condition_info_incidence . "
          GROUP BY reports.id      


      ) AS allItems  
      ORDER BY allItems.date desc
    ";
// Main Sql Query is Here
    // echo $allItemsSQLIncidence;
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
      allItems.chapter_name,
      allItems.report_type

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
          chapters.`name` AS chapter_name,
          reports.report_type

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
          sm_calendar_masters.website_id in (".$websiteIds.") and                        
          reports.id IS NOT NULL and 
          reports.active = 1 and 
          reports.for_calendar = 1 and ISNULL(reports.deleted_at)
          " . $date_condition_info_sm_cal . "
          " . $condition_info_sm_cal . "  

      ) AS allItems  
      ORDER BY allItems.date desc
    ";
    $all_items_sm = DB::connection('setfacts')->select($allItemsSQLSM);

    
    $keywordTypeFinal = array_unique($keywordType);
    // dd($keywordTypeFinal);

    // Search keyword is == "document"
    $all_items_documents = array();
    if ((count($keywords) == 0) || in_array("Document", $keywordTypeFinal)) {
      $all_items_focus_sql = "
        SELECT allItems.id, allItems.website, allItems.type, allItems.title, allItems.description, allItems.tags, allItems.document, allItems.date, allItems.team_id, allItems.module_id, allItems.chapter_id
        , allItems.team_name, allItems.module_name, allItems.chapter_name, allItems.report_type
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
                  chapters.`name` AS chapter_name,
                  'News' as report_type                                          
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
                  and documents.website_id in (".$websiteIds.")  
                  " . $condition_focus_teams . "    
                  " . $condition_focus_modules . "   
                  " . $condition_info_focus . "                                        
              ) 
          ) AS allItems
          ORDER BY day(allItems.date) asc
      ";
      $all_items_documents = DB::connection('setfacts')->select($all_items_focus_sql);
    }

    $md5Key = md5($date_condition_info_incidence. $date_condition_info_sm_cal. $date_condition_info_focus);
    Log::info("md5Key ". $md5Key);

    // dump($all_items_incidence);
    // dump($all_items_sm);
    // dump($all_items_focus);
    // exit();
// $bindings=$all_items_incidence()->toSql();
//     $finalSql = Helper::interpolateQuery($all_items_incidence, $bindings);
//             dd($finalSql);


    $all_items = array();
    $all_items_focus = array();
    if(in_array("Document", $keywordTypeFinal) && in_array("teams", $keywordTypeFinal) && in_array("sm_calendar_masters", $keywordTypeFinal)) {
        // show documents, incidence, and smcal
        $all_items = array_merge($all_items_incidence, $all_items_sm);
        $all_items_focus = array_merge($all_items_documents);
      //  dd("1st if");
    } else if(in_array("Document", $keywordTypeFinal) && in_array("teams", $keywordTypeFinal)) {
        // show documents, incidence
        $all_items = array_merge($all_items_incidence);
        $all_items_focus = array_merge($all_items_documents);
      //  dd("2nd");
    } else if(in_array("Document", $keywordTypeFinal) && in_array("sm_calendar_masters", $keywordTypeFinal)) {
        // show documents, smcal
        $all_items = array_merge($all_items_sm);
        $all_items_focus = array_merge($all_items_documents);
      //  dd("3rd");
    } else if(in_array("teams", $keywordTypeFinal) && in_array("sm_calendar_masters", $keywordTypeFinal)) {
        // show incidence, smcal
        $all_items = array_merge($all_items_incidence, $all_items_sm);
      //  dd("4rth");
   /* } else if(in_array("teams", $keywordTypeFinal)) {
        // show incidence
        $all_items = array_merge($all_items_incidence);
        //dd("5th");
    } else if(in_array("sm_calendar_masters", $keywordTypeFinal)) {
        // show smcal
        $all_items = array_merge($all_items_sm);
       //dd("6th");
    } else if(in_array("Document", $keywordTypeFinal)) {
        // show documents
        $all_items_focus = array_merge($all_items_documents);
        //dd("7th");*/
    } else {
        // show nothing or default case
        // show documents, incidence, and smcal
        $all_items = array_merge($all_items_incidence, $all_items_sm);  
        // dd($all_items);
        $all_items_focus = array_merge($all_items_documents);        
    }

    
    // Sort $all_items by date in descending order
    usort($all_items, function($a, $b) {
      return strtotime($b->date) - strtotime($a->date);
    });

    // dump( $all_items);
    // dump( $all_items_focus);
    // exit();


    return view('calendar.load', compact(
      'all_items',
      'all_items_focus',
      'type',
      'eventYear',
      'keywords',
      'chapterReportData',
      'md5Key',
      'calendar_date_db'
    ));
  }

  public function viewLogin($id = "")
  {
    if (auth()->user()) {
      $user = auth()->user();
      if ($user) {
        return redirect()->route('/');
      }
    }

    $locations = DB::connection('setfacts')->select(DB::raw("SELECT 
      locations.id, 
      locations.name
      FROM 
      `locations`
      where locations.deleted_at is null
      order by locations.parent, locations.name "));

    return view('auth.login', compact('locations', 'id'));
  }

  public function createTdtySavedReport(Request $request)
  {
    $user_id = 0;
    // if(auth()){
    //     $user = auth()->user();
    //     $user_id = $user->id;
    // }

    if (!empty($request->url_params)) {

      $params = $request->url_params;
      // dump($params);
      //find if params exist, return matching row name
      $checkExist = TdtySavedReport::where('params', $params)->get()->count();
      if ($checkExist > 0) {
        $TdtySavedReportData = TdtySavedReport::where('params', $params)->get();
        foreach ($TdtySavedReportData as $data) {
          $name = $data->name;
          return response()->json(array("status" => "success", "namedReport" => $name));
        }
      } else {

        $name = strtoupper($this->str_random(10));
        $TdtySavedReportObj = new TdtySavedReport();
        $TdtySavedReportObj->name = $name;
        $TdtySavedReportObj->params = $params;
        $TdtySavedReportObj->user_id = $user_id;
        $TdtySavedReportObj->website_id = session()->get('website_id');
        $TdtySavedReportObj->save();

        return response()->json(array("status" => "success", "namedReport" => $name));
      }
    }
    return response()->json(array("status" => "error"));
  }

  function str_random($length = 16)
  {
    return Str::random($length);
  }

  public function viewSavedLink(Request $request, $name)
  {
    $TdtySavedReport = TdtySavedReport::where('name', $name)->get();
    if ($TdtySavedReport) {
      foreach ($TdtySavedReport as $key => $data) {
        $params = $data->params;
        // dd($params);

        $website_id = $data->website_id;
        if(empty($website_id)){
        $website_id = 1;
        }
        $website = DB::connection('setfacts')->select("select * from websites where active=1 and id = ".$website_id.";");
        $slug = $website[0]->domain;

        
        Log::info("params ". $params);

        // return redirect()->route('calendar.index', session()->get('website_slug'), [$params]);
        return redirect()->route('calendar.index',[$slug."/?".$params]);
      }
    }
  }

  function getUrlMimeType($url)
  {
    $buffer = file_get_contents($url);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($buffer);
  }

  public function download($fileName)
  {

    $fileUrl = base64_decode($fileName);
    $fileType = $this->getUrlMimeType($fileUrl);
    $fileInfo = pathinfo($fileUrl);

    // dump($fileInfo);
    // dump($fileType);
    // dump($fileUrl);
    // exit();

    $name = $fileInfo['basename'];

    $headers = [
      'Content-Type'        => $fileType,
      'Content-Disposition' => 'attachment; filename="' . $name . '"',
    ];

    $contents = file_get_contents($fileUrl);
    return Response::make($contents, 200, $headers);
    exit();
    // return Response::make($this->getUrlMedia($fileUrl), 200, $headers);

  }
}
