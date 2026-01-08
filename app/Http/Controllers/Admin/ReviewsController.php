<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use App\Models\Auditlog;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::withTrashed()->get();

        $smCalIdsInfo=array(0);
        $smCalIdsIssue=array(0);

        $incidenceCalIdsInfo=array(0);
        $incidenceCalIdsIssue=array(0);

        foreach($reviews as $key => $review){
            if ($review->type == "sm_cal"){
                if ($review->website == "info"){
                    $smCalIdsInfo[]=$review->item_id;
                }else{
                $smCalIdsIssue[]=$review->item_id;
                }
            }else{
                if ($review->website == "info"){
                    $incidenceCalIdsInfo[]=$review->item_id;
                } else{
                    $incidenceCalIdsIssue[]=$review->item_id;
                }
            }
        }

        $smCalsInfo = DB::connection('setfacts')->select("select reports.id, reports.heading as title from reports where reports.id in (".implode(",",$smCalIdsInfo).")");
        $smCalsIssue = DB::connection('setfacts')->select("select reports.id, reports.heading as title from reports where reports.id in (".implode(",",$smCalIdsIssue).")");
        
        $incidenceCalsInfo = DB::connection('setfacts')->select("select incidence_calendars.id, incidence_calendars.heading as title from incidence_calendars where incidence_calendars.id in (".implode(",",$incidenceCalIdsInfo).")");
        $incidenceCalsIssue = DB::connection('setfacts')->select("select incidence_calendars.id, incidence_calendars.heading as title from incidence_calendars where incidence_calendars.id in (".implode(",",$incidenceCalIdsIssue).")");

        return view('admin.reviews.index', compact('reviews',
        'smCalsInfo','smCalsIssue','incidenceCalsInfo','incidenceCalsIssue'
    ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $review = Review::create($request->all());

        // Audit Log Entry
        $resultMessage = sprintf('Created issue: %s | name: %s', $review->id, $review->name);
        Auditlog::info(Auditlog::CATEGORY_REVIEW, $resultMessage, $review->id);

        return redirect()->route('admin.review.index')->with('success', 'Issue Successfully created!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $review = Review::withTrashed()->with(['user'])->find($id);
        if($review){
            return view('admin.reviews.edit', compact('review'));
        }else{
            return redirect()->route('admin.review.index')->with('error', 'Review not found!');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $review = Review::withTrashed()->find($id);
        if($review){
            $review->update($request->all());

            $resultMessage = sprintf('Updated review: %s | name: %s', $review->id, $review->name);
            Auditlog::info(Auditlog::CATEGORY_REVIEW, $resultMessage, $review->id);

            return redirect()->route('admin.review.index')->with('success', 'Review Successfully updated!');
        }else{
            return redirect()->route('admin.review.index')->with('error', 'Review not found!');
        }        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = Review::find($id);
        if($review){
            $review->delete();

            // Audit Log Entry
            $resultMessage = sprintf('Deleted review: %s | name: %s', $review->id, $review->name);
            Auditlog::critical(Auditlog::CATEGORY_REVIEW, $resultMessage, $review->id);

        
            return redirect()->route('admin.review.index')->with('success', 'Review Successfully deleted!');
        }else{
            return redirect()->route('admin.review.index')->with('error', 'Review not found!');
        }     
    }

    /**
     * restore the specified resource
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        // $review = Review::find($id);
        $review = Review::withTrashed()->find($id);
        if($review){
            $review->restore();

            // Audit Log Entry
            $resultMessage = sprintf('Restored review: %s | name: %s', $review->id, $review->name);
            Auditlog::warning(Auditlog::CATEGORY_REVIEW, $resultMessage, $review->id);

            return redirect()->route('admin.review.index')->with('success', 'Review Successfully restored!');
        }else{
            return redirect()->route('admin.review.index')->with('error', 'Review not found!');
        }     
    }    



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delperm($id)
    {
        $review = Review::withTrashed()->find($id);
        if($review){
            $review->forceDelete();

            // Audit Log Entry
            $resultMessage = sprintf('Permanently deleted review: %s | name: %s', $review->id, $review->name);
            Auditlog::critical(Auditlog::CATEGORY_REVIEW, $resultMessage, $review->id);

            return redirect()->route('admin.review.index')->with('success', 'Review Successfully deleted permanently!');
        }else{
            return redirect()->route('admin.review.index')->with('error', 'Review not found!');
        }     
    }       
}
