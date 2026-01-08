<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Role;
use App\Models\Team;
use App\Models\UserProfile;
use App\Models\UserAssociation;
use DB;
use App\Traits\CommonMethodsTraits;


class ReportSearchFormComposer
{
    use CommonMethodsTraits;
    protected $connection = 'setfacts';

    public function compose(View $view)
    {
        $websiteIds = $this->getWebsiteIdsFromSessionForDatabaseSearch();
        // dump($websiteIds);

        $team = DB::connection('setfacts')->select(DB::raw("SELECT teams.id, teams.name , teams.slug, COUNT(reports.id) AS total_reports FROM `teams` 
        INNER JOIN `reports` ON `reports`.`team_id` = `teams`.`id` where teams.active=1 and teams.website_id in (".$websiteIds.") and teams.is_public=1 GROUP BY `teams`.`id`"));

        $teamId = "";

        $locations = DB::connection('setfacts')->select(DB::raw("SELECT 
                locations.id, 
                locations.name, 
                COUNT(reports.id) AS total_reports 
            FROM 
                `locations` 
            INNER JOIN `reports` ON `reports`.`location_id` = `locations`.`id` 
            where locations.deleted_at is null
            GROUP BY `locations`.`id` 
            order by locations.parent, locations.name "));



        $languages = DB::connection('setfacts')->select(DB::raw("SELECT
                reports.`language_id`,
                languages.id as language_id,
                languages.name as language_name,
                COUNT(reports.id) AS total_reports
            FROM
                `reports`
            join languages on languages.id = reports.language_id
            WHERE languages.deleted_at is null 
            GROUP BY `reports`.`language_id` 
            ORDER BY `languages`.`id` ASC"));


        $users = DB::connection('setfacts')->select( DB::raw("SELECT users.id, users.name, users.email, COUNT(reports.id) AS total_reports FROM `users` 
        INNER JOIN `reports` ON `reports`.`user_id` = `users`.`id`
        INNER JOIN `teams` ON `reports`.`team_id` = `teams`.`id` 
        where 
        teams.active = 1 
        and 
        reports.deleted_at is null
        and users.deleted_at is null        
        GROUP BY `users`.`id`"));                  

        // dd($users);
                    

        $view->with(['team' => $team, 'teamId' => $teamId, 'locations' => $locations, 'languages' => $languages, 'users' => $users]);
    }
}
