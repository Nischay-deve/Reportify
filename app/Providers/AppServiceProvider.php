<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\CommonComposer;
use App\Http\View\Composers\ReportSearchFormComposer;
use App\Http\View\Composers\SMReportSearchFormComposer;
use AWS\CRT\Log as CRTLog;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

use Config;
use Illuminate\Support\Facades\View;

use DB;
use Dotenv\Dotenv;
use Log;
use File;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log as FacadesLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $newEnvPath = 'E:/xampp-8/htdocs/teamwork/thisdatethatyear2/config';
        $newEnvPath = '/var/www/vhosts/env_files/reportify';
       
        $dotenv = Dotenv::createImmutable($newEnvPath);
        $dotenv->load();

     

        View::composer(['member.*'], CommonComposer::class);

        View::composer(['admin.*'], CommonComposer::class);

        View::composer(['*'], CommonComposer::class);

        View::composer(['report.*'], CommonComposer::class);

        View::composer(['partials.report-search-form'], ReportSearchFormComposer::class);
        View::composer(['partials.download-report-search-form'], ReportSearchFormComposer::class);

        View::composer(['partials.download-sm-report-search-form'], SMReportSearchFormComposer::class);

        Paginator::useBootstrap();
    }
}
