<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

//WithHeadings, WithMapping, 
class DocumentExport implements FromView, WithTitle,  WithStyles
{
    protected $businesses;
    protected $listType;
    protected  $dataCalendar;
    protected  $calDates;

    public function __construct($documents, $listType, $dataCalendar, $calDates)
    {
       $this->documents = $documents;
       $this->listType = $listType;
       $this->dataCalendar = $dataCalendar;
       $this->calDates = $calDates;
    }

    public function view(): View
    {
        return view('member.documents.excel_export', [
            'documents' => $this->documents,
            'listType' => $this->listType,
            'dataCalendar' => $this->dataCalendar,
            'calDates' => $this->calDates,
        ]);
    }

    public function title(): string
    {
        $title = "Social Media Calendar";
        if ($this->listType == 'year'){
            $title .= " Date Year Wise Listing";
        } else {
        $title .= " Date Day Month Wise Listing";
        }

        return $title;
    }

    // public function headings(): array
    // {
    //     return [
    //         'Business Id',
    //         'Name',
    //         'Business Type',
    //         'Address',
    //         'Distance',
    //         'Updated At',
    //         'Reviews',
    //         'Total Photos',
    //         'Flagged',
    //         'Tagged Categories Count',
    //         'Has Accessibility Tags',
    //     ];
    // }

    public function styles(Worksheet $sheet)
    {
        return [
           // Style the first row as bold text.
           1    => ['font' => ['bold' => true]],
        ];
    }    

    // public function map($businesses): array
    // {

    //     $review_details_submitted = "0";
    //     if($businesses->review_details_submitted > 0){
    //         $review_details_submitted = $businesses->review_details_submitted;
    //     }

    //     $total_photos = "0";
    //     if($businesses->total_photos > 0){
    //         $total_photos = $businesses->total_photos;
    //     }
        
    //     $issues_count = "0";
    //     if($businesses->issues_count > 0){
    //         $issues_count = $businesses->issues_count;
    //     }

    //     $countTaggingCategories = "0";
    //     if($businesses->countTaggingCategories > 0){
    //         $countTaggingCategories = $businesses->countTaggingCategories;
    //     }        
                
    //     $reviewsSubmitted = "No";
    //     if($businesses->reviewsSubmitted > 0){
    //         $reviewsSubmitted = "Yes";
    //     }

    //     return [
    //         $businesses->business->id,
    //         $businesses->name,
    //         $businesses->businessType,
    //         $businesses->address,
    //         round($businesses->distance,2). " km",
    //         \Carbon\Carbon::parse($businesses->updated_at)->format('m/d/Y H:i:s'),
    //         $review_details_submitted,
    //         $total_photos,
    //         $issues_count,
    //         $countTaggingCategories,
    //         $reviewsSubmitted,
    //     ];
    // }


    /**
     * @return Collection
     */
    public function collection()
    {
       return $this->documents;
    }
}
