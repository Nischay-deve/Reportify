<?php
/*--------------------
https://github.com/jazmy/laravelformbuilder
Licensed under the GNU General Public License v3.0
Author: Jasmine Robinson (jazmy.com)
Last Updated: 12/29/2018
----------------------*/

namespace App\Traits;
use Illuminate\Http\Request;
use DateTime;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


trait CommonMethodsTraits
{

    public function fetchChapterReportCount()
    {
        ///////////////////////////////////////////
        //get chapter wise report counts 
        ///////////////////////////////////////////
        $chapterReports = DB::connection('setfacts')->select("SELECT reports.chapter_id, COUNT(reports.id) AS chapter_reports FROM `reports` 
        join chapters on chapters.id = reports.chapter_id
        where reports.active=1 and 
        reports.deleted_at is null and 
        chapters.active = 1 and
        chapters.deleted_at is null 
        GROUP BY reports.chapter_id");
        // dd($chapterReports);
        $chapterReportData = array();
        foreach ($chapterReports as $key => $chapterReport) {
            $chapterReportData[$chapterReport->chapter_id] = $chapterReport->chapter_reports;
        }
        return $chapterReportData;
    }

    public function fetchDailyMonitoring($date)
    {
        $websiteIds = $this->getWebsiteIdsFromSessionForDailyMonitoring();
        // dump($websiteIds);

        // $currentTime = date("H:i:s");
        // echo "<BR>".$currentTime;
        date_default_timezone_set('Asia/Kolkata');
        
        // dd($date);

        if (empty($date)) {

            // check for time. If time > 19:00 we show today date
            // Else we show yesterday as today
            $currentTime = date("H");
            // echo "<BR>".$currentTime;
            // exit();
            if ($currentTime >= 19) {
                $today = Carbon::now()->format('d-m-Y');
                $prevDate = Carbon::yesterday()->format('d-m-Y');
                $nextDate = Carbon::tomorrow()->format('d-m-Y');

                $dailyPopupDate =  Carbon::createFromFormat('d-m-Y', $today)->addDays(1)->format('d-m-Y');

            } else {
                $today = Carbon::yesterday()->format('d-m-Y');
                $prevDate = Carbon::createFromFormat('d-m-Y', $today)->subDays(1)->format('d-m-Y');
                $nextDate =  Carbon::createFromFormat('d-m-Y', $today)->addDays(1)->format('d-m-Y');

                $dailyPopupDate =  Carbon::createFromFormat('d-m-Y', $today)->addDays(2)->format('d-m-Y');
            }

            $selectedDate = $today;

           
            

        } else {
          
            $today = Carbon::createFromFormat('d-m-Y', $date);
            $prevDate = Carbon::createFromFormat('d-m-Y', $date)->subDays(1)->format('d-m-Y');
            $nextDate =  Carbon::createFromFormat('d-m-Y', $date)->addDays(1)->format('d-m-Y');

            $selectedDate = $date;

            $dailyPopupDate = $nextDate;
        }

        $fromdate = DateTime::createFromFormat('d-m-Y', $selectedDate);
        $from = $fromdate->format('Y-m-d');

        $todate = DateTime::createFromFormat('d-m-Y', $selectedDate);
        $to = $fromdate->format('Y-m-d');

        $teamIdsToExclude = 13;
        $allTeamSql = "
        SELECT
        teams.id,teams.name, teams.slug, count(reports.id) as totalReports
        FROM
            teams
        left join reports on reports.team_id = teams.id  and reports.publish_at between '" . $from . "' and '" . $to . "'        and ISNULL( reports.deleted_at)             and reports.active = 1   AND reports.sm_calendar_master_id = 0
        WHERE 
        teams.active = 1 
        and teams.is_public = 1 
        and ISNULL( teams.deleted_at)             
        and teams.website_id in (".$websiteIds.")
        and teams.id not in (".$teamIdsToExclude.")
        
        group by teams.id
        ORDER BY  teams.id asc       
        ";

        // echo $allTeamSql;
        // exit();
        $all_issues = DB::connection('setfacts')->select($allTeamSql);

        //All SM media count
        $allSMMediaCountSql = "
        SELECT
            count(reports.id) as totalReports
            FROM
            sm_calendar_masters
            join websites on websites.id = sm_calendar_masters.website_id         
            , reports
            WHERE 
            sm_calendar_masters.id = reports.sm_calendar_master_id and
            sm_calendar_masters.active = 1 and
            sm_calendar_masters.website_id in (".$websiteIds.") and                        
            reports.id IS NOT NULL and 
            reports.active = 1 and 
            ISNULL(reports.deleted_at) and
            reports.publish_at = '" . $from . "'
            ORDER BY  reports.id asc       
        ";        
        $all_sm_cals = DB::connection('setfacts')->select($allSMMediaCountSql);



        //All daily popup count
        
        // dump($dailyPopupDate);

        $fromdateforPopup = DateTime::createFromFormat('d-m-Y', $dailyPopupDate);
        $fromForPopup = $fromdateforPopup->format('m-d');       
        // dump($fromForPopup);
       
        $all_items_focus_sql = "
            SELECT
                COUNT(documents.id) AS totalReports
            FROM
                documents
                join websites on websites.id = documents.website_id
            WHERE             
            documents.active = 1              
                and documents.is_deleted = 0 and ISNULL(documents.deleted_at) and
            documents.website_id in (".$websiteIds.")   and                
            DATE_FORMAT(documents.date, '%m-%d') = '".$fromForPopup."'
        ";
    
        $all_items_focus = DB::connection('setfacts')->select($all_items_focus_sql);        
        // dump($all_items_focus);
        // dump($all_items_focus_sql);

        $all_incidents_sql = "
            SELECT
               
                COUNT(incidence_calendars.id) AS totalReports
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
                AND DATE_FORMAT(incidence_calendars.date, '%m-%d') = '".$fromForPopup."'
        ";        
        $all_items_incidents = DB::connection('setfacts')->select($all_incidents_sql);        
        // dump($all_items_incidents);
        // dump($all_incidents_sql);


        $all_smcal_sql = "
            SELECT
                COUNT(reports.id) AS totalReports
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
            AND DATE_FORMAT(reports.publish_at, '%m-%d') = '".$fromForPopup."'
        ";        
        $all_items_smcal = DB::connection('setfacts')->select($all_smcal_sql);        
        // dump($all_items_smcal);
        // dump($all_smcal_sql);


        $all_count_popup =0;
        if($all_items_focus){
            $all_count_popup += $all_items_focus['0']->totalReports;
        }
        if($all_items_incidents){
            $all_count_popup += $all_items_incidents['0']->totalReports;
        }
        if($all_items_smcal){
            $all_count_popup += $all_items_smcal['0']->totalReports;
        }                
        

        $data=array();

        $data['all_issues'] =  $all_issues;
        $data['all_sm_cals'] =  $all_sm_cals;
        $data['prevDate'] =  $prevDate;
        $data['nextDate'] =  $nextDate;
        $data['selectedDate'] =  $selectedDate;
        $data['dailyPopupDate'] =  $dailyPopupDate;        
        $data['all_count_popup'] =  $all_count_popup;

        // dump($data);
        return $data;
    }

    public function getWebsiteIdsFromSessionForDailyMonitoring(){
        $websiteIds = "";
        if(session()->get('website_id') == 0){
            $this->loadCenterWebsiteFromRequest();
            $websiteIds=session()->get('website_id');
        }else{
          $websiteIds=session()->get('website_id');
        }       
        
        return $websiteIds;
    }

    public function getWebsiteIdsFromSessionForCalendar(){
        $websiteIds = "";
        if(session()->get('website_id_calendar') == 0){
            // echo "In if";
            $this->loadCenterWebsiteFromRequest();
            $websiteIds=session()->get('website_id_calendar');
        }else{
            // echo "In else";
          $websiteIds=session()->get('website_id_calendar');
        }       
        
        return $websiteIds;
    }    

    public function getWebsiteIdsFromSessionForDatabaseSearch(){
        $websiteIds = "";
        if(session()->get('website_id_databse_search') == 0){
            $this->loadCenterWebsiteFromRequest();
            $websiteIds=session()->get('website_id_databse_search');
        }else{
          $websiteIds=session()->get('website_id_databse_search');
        }       
        
        return $websiteIds;
    }      

    public function loadCenterWebsiteFromRequest($centerIdsArr=array()){
        // dump($centerIdsArr);
        $centerArr = array();
        $centerSlugArr = array();
        $centerArrCalendar = array();
        $centerArrDatabaseSearch = array();

        if(count($centerIdsArr) > 0){

            foreach($centerIdsArr as $centerId){
                $centerData = DB::connection('setfacts')->select("select * from websites where id = '".$centerId."' and active=1");
                if($centerData){
                    $centerArr[]=$centerData[0]->id;
                    $centerSlugArr[]=$centerData[0]->domain;

                    $websiteShareApprovedDataCalendar = $this->loadCenterShareAllowedData($centerData[0]->id, 'calendar');
                    // dump("websiteShareApprovedData ** = ", $websiteShareApprovedData);
                    foreach($websiteShareApprovedDataCalendar as $data){
                        $centerArrCalendar[]=$data;
                    }  
                    
                    $websiteShareApprovedDataDatabaseSearch = $this->loadCenterShareAllowedData($centerData[0]->id, 'database_search');
                    // dump("websiteShareApprovedData ** = ", $websiteShareApprovedData);
                    foreach($websiteShareApprovedDataDatabaseSearch as $data){
                        $centerArrDatabaseSearch[]=$data;
                    }                      
                }
            }

            

        }else{
            $centerData = DB::connection('setfacts')->select("select * from websites where active=1 order by id asc limit 1");
            // dd($centerData);
            if($centerData){
                $centerArr[]=$centerData[0]->id;
                $centerSlugArr[]=$centerData[0]->domain;

                $websiteShareApprovedDataCalendar = $this->loadCenterShareAllowedData($centerData[0]->id, 'calendar');
                // dump("websiteShareApprovedData ** = ", $websiteShareApprovedData);
                foreach($websiteShareApprovedDataCalendar as $data){
                    $centerArrCalendar[]=$data;
                }  
                
                $websiteShareApprovedDataDatabaseSearch = $this->loadCenterShareAllowedData($centerData[0]->id, 'database_search');
                // dump("websiteShareApprovedData ** = ", $websiteShareApprovedData);
                foreach($websiteShareApprovedDataDatabaseSearch as $data){
                    $centerArrDatabaseSearch[]=$data;
                }               
            }    
            
          
        }

        $centerIdsStr = implode(",",$centerArr);
        // dump($centerIdsStr);
        Session::put('website_id', $centerIdsStr);

        $centerSlugsStr = implode("_",$centerSlugArr);
        // dd($centerSlugsStr);
        Session::put('website_slug', $centerSlugsStr);

        $centerIdsCalendarStr = implode(",",array_merge($centerArr, $centerArrCalendar));
        // dump($centerIdsCalendarStr);
        Session::put('website_id_calendar', $centerIdsCalendarStr);
        
        $centerIdsDatabaseSearchStr = implode(",",array_merge($centerArr, $centerArrDatabaseSearch));
        // dump($centerIdsDatabaseSearchStr);
        Session::put('website_id_databse_search', $centerIdsDatabaseSearchStr);        
      
        return;   
    }

    public function loadCenterShareAllowedData($websiteId, $mode="calendar"){
        // dump("websiteId = ", $websiteId);

        if($mode == "calendar"){
            // show_data_in_calendar
            $sqlForShowInCalendar = "select website_id from website_shares where website_shares.approved=1 and website_shares.show_data_in_calendar=1 and website_shares.keep_data_in_calendar=1 and website_shares.requested_by_website_id = ".$websiteId.";";
            // dd($sql);

            $websiteShareApprovedForShowInCalendar = array();
            $websiteShareApprovedDataForShowInCalendar = DB::connection('setfacts')->select($sqlForShowInCalendar);
            // dump("websiteShareApprovedData = ", $websiteShareApprovedData);
            foreach($websiteShareApprovedDataForShowInCalendar as $data){
                $websiteShareApprovedForShowInCalendar[] = $data->website_id;
            }

            // dump("websiteShareApprovedForShowInCalendar = ", $websiteShareApprovedForShowInCalendar);

            return $websiteShareApprovedForShowInCalendar;
        }

        if($mode == "database_search"){
            // show_data_in_database_search
            $sqlForShowInDatabaseSearch = "select website_id from website_shares where website_shares.approved=1 and website_shares.show_data_in_database_search=1 and website_shares.keep_data_in_database_search=1 and website_shares.requested_by_website_id = ".$websiteId.";";
            // dd($sql);

            $websiteShareApprovedForShowInDatabaseSearch = array();
            $websiteShareApprovedDataForShowInDatabaseSearch = DB::connection('setfacts')->select($sqlForShowInDatabaseSearch);
            // dump("websiteShareApprovedData = ", $websiteShareApprovedData);
            foreach($websiteShareApprovedDataForShowInDatabaseSearch as $data){
                $websiteShareApprovedForShowInDatabaseSearch[] = $data->website_id;
            }

            // dump("websiteShareApprovedForShowInDatabaseSearch = ", $websiteShareApprovedForShowInDatabaseSearch);      
            
            return $websiteShareApprovedForShowInDatabaseSearch;
        }
    }
    
}
