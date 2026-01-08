@extends($viewLayout)

@section('content')

    <div class="page-content">

        <div class="container-fluid" id="overlay">
            <div class="row">
                <div class="col-12" style="text-align: center;">
                    <img src="{{ asset('public/images/loading.svg') }}" alt="Loading" />
                    Loading...
                </div>
            </div>
        </div>

        <div class="container-fluid" id="page_content" style="display: none;">


            <div class="row">

                <div class="col-md-12 col-xl-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive">
                                <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <table class="tableStyleHeading" cellspacing="0" cellpadding="2"
                                            style="text-align: center;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center;font-size: 15px;font-weight:bold">
                                                        {{ $reportTitle }}</td>
                                                </tr>
                                                @if ($reportHeading)
                                                    <tr>
                                                        <td style="text-align: center; font-weight:bold">
                                                            {{ $reportHeading }}</td>
                                                    </tr>
                                                @endif
                                                @if ($reportDescription)
                                                    <tr>
                                                        <td style="text-align: center">{{ $reportDescription }}</td>
                                                    </tr>
                                                @endif

                                                @if ($from == $to && !empty($to))
                                                    <tr>
                                                        <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
                                                    </tr>
                                                @elseif(!empty($from) && !empty($to))<tr>
                                                        <td style="text-align: center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($from))->format('M d, Y') }} To
                                                            {{ \Carbon\Carbon::createFromTimestamp(strtotime($to))->format('M d, Y') }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        <div class="accordion" id="accordionOne">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    <b>Table Type 1: Issue / Category Table</b>: {{ $teamName->name }}
                                                </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionOne">
                                                    <div>         

                                                        <table align="left" cellspacing="5" cellpadding="5"  style="margin:10pt;width:100%;text-align:left;white-space: nowrap;">
                                                        <tbody>
                                                        @php
                                                            $sno=1;
                                                        @endphp                                                
                                                        @foreach ($locations as $key => $location)
                                                            {{-- @if($location->id == 2) --}}
                                                            @if(isset($reportsData[$location->id]))      
                                                            <tr>
                                                                <td align="left" valign="top" style="text-align: left;font-weight:bold;color:red; width:30%; ">{{$sno}}. {{ $location->name }}</td>        
                                                            </tr>
                                                            @php
                                                                $sno++;
                                                            @endphp       

                                                                @foreach ($reportsData[$location->id] as $keyReportsDataLocation => $reportsDataLocation)
                                                                @php
                                                                    //dump($reportsDataLocation);
                                                                @endphp                                    
                                                                    <tr>
                                                                <td style="white-space: nowrap; width:70%;">
                                                                    {{$categoriesData[$reportsDataLocation['category']]}}
                                                                    
                                                                </td>                                 
                                                                <td>
                                                                    <p style="margin-top:10px; ">{{$reportsDataLocation['totalReports']}} Reports</p>
                                                                </td>
                                                                </tr>
                                                                @endforeach                
                                                            
                                                            @endif
                                                            {{-- @endif --}}
                                                        @endforeach
                                                        </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>           
                                        </div>       


                                        <div class="accordion" id="accordionTwo" style="margin-top:10px;">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    <b>Table Type 2: Category / Sub-Category Table</b>
                                                </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTwo">
                                                    <div>      
                                                        <table align="left" cellspacing="5" cellpadding="5"  style="margin:10pt;width:100%;text-align:left;white-space: nowrap;">
                                                        <tbody>
                                                        @php
                                                            $sno=1;
                                                        @endphp 
                                                        {{-- iterate modules --}}
                                                        @if(count($module_ids_arr)>0)
                                                            @foreach ($module_ids_arr as $key_module_id => $module_id)
                                                                @if(isset($reportsTwoData[$module_id]))      
                                                                <tr>
                                                                    <td align="left" valign="top" style="text-align: left;font-weight:bold;color:red;"  colspan="2">{{$sno}}. Category: {{ $categoriesData[$module_id] }}</td>        
                                                                </tr>
                                                                @php
                                                                    $sno++;
                                                                @endphp   

                                                                @foreach ($reportsTwoData[$module_id] as $location_id => $reportsDataLocation)
                                                                    <tr>
                                                                        <td align="left" valign="top" style="text-align: left;font-weight:bold;color:black;" colspan="2">{{ $locationsData[$location_id] }}</td>        
                                                                    </tr>

                                                                    @foreach ($reportsDataLocation as $keyReportsDataLocation => $reportDataLocation)
                                                                        @if(isset($subcategoriesData[$reportDataLocation['sub_category']]))
                                                                        <tr>
                                                                            <td style="white-space: nowrap; width:70%;">
                                                                                {{$subcategoriesData[$reportDataLocation['sub_category']]}}
                                                                                
                                                                            </td>                                 
                                                                            <td>
                                                                                <p style="margin-top:10px; ">{{$reportDataLocation['totalReports']}} Reports</p>
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                    @endforeach    
                                                                @endforeach

                                                                @endif                                                        
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td align="left" valign="top" style="text-align: left;font-weight:bold;color:red;"  colspan="2">No data found!</td>        
                                                            </tr>                                                            
                                                        @endif
                                                       
                                                        </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>           
                                        </div>                   

                                        
                                        <div class="accordion" id="accordionThree" style="margin-top:10px;">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                    <b>Table Type 3: Sub-Category / Tags Table</b>: {{ $teamName->name }}
                                                </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionThree">
                                                    <div>      
                                                        <table align="left" cellspacing="5" cellpadding="5"  style="margin:10pt;width:100%;text-align:left;white-space: nowrap;">
                                                        <tbody>
                                                        @php
                                                            $sno=1;
                                                        @endphp 
                                                        {{-- iterate modules --}}
                                                        @if(count($chapter_ids_arr)>0)
                                                            @foreach ($chapter_ids_arr as $key_chapter_id => $chapter_id)
                                                                @if(isset($reportsThreeData[$chapter_id]))      
                                                                <tr>
                                                                    <td align="left" valign="top" style="text-align: left;font-weight:bold;color:red;"  colspan="2">{{$sno}}. Sub-Category: {{ $subcategoriesDataThree[$chapter_id] }}</td>        
                                                                </tr>
                                                                @php
                                                                    $sno++;
                                                                @endphp   

                                                                @foreach ($reportsThreeData[$chapter_id] as $location_id => $reportsDataLocation)
                                                                    <tr>
                                                                        <td align="left" valign="top" style="text-align: left;font-weight:bold;color:black;" colspan="2">{{ $locationsData[$location_id] }}</td>        
                                                                    </tr>

                                                                    @foreach ($reportsDataLocation as $keyReportsDataLocation => $reportDataLocation)                                                                       
                                                                        @if(isset($subcategoriesDataThree[$reportDataLocation['sub_category']]))
                                                                            <tr>
                                                                                <td style="white-space: nowrap; width:70%;">
                                                                                    {{$subcategoriesDataThree[$reportDataLocation['sub_category']]}}
                                                                                    
                                                                                </td>                                 
                                                                                <td>
                                                                                    <p style="margin-top:10px; ">{{$reportDataLocation['totalReports']}} Reports</p>
                                                                                </td>
                                                                            </tr>

                                                                            {{-- Team tags selectedReportTeamTag --}}
                                                                            @if($teamTagsData && count($teamTagsData)> 0 && (count($reportDataLocation['selectedReportTeamTag']) > 0))
                                                                                @foreach($reportDataLocation['selectedReportTeamTag'] as $key_selected_report_tag_id => $selected_report_tag_id)                                                                                                                                                           

                                                                                    @foreach($teamTagsData as $team_id_key => $teamTagData)
                                                                                        @php
                                                                                            // echo "TEAM ID".$teamName->id;
                                                                                            // exit();
                                                                                        @endphp
                                                                                        @if($teamTagData && count($teamTagData)> 0 && ($teamName->id == $team_id_key))
                                                                                            @foreach($teamTagData as $key => $team_tag_data)   
                                                                                                @foreach($team_tag_data['tags'] as $tag_key => $tag)
                                                                                                @if($tag['id'] == $selected_report_tag_id)
                                                                                                    <tr>
                                                                                                        <td>{{$team_tag_data['label']}}</td>
                                                                                                        <td>     
                                                                                                            {{$tag['tag']}}
                                                                                                        </td>
                                                                                                    </tr>                                                                                                   
                                                                                                @endif
                                                                                                @endforeach                                                                         
                                                                                            @endforeach                                                                  
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endforeach             
                                                                            @endif
                                                                            <tr>
                                                                                <td colspan="2">
                                                                                    <hr>
                                                                                </td>
                                                                            </tr>                                                                                 
                                                                        @endif
                                                                    @endforeach    
                                                                @endforeach

                                                                @endif                                                        
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td align="left" valign="top" style="text-align: left;font-weight:bold;color:red;"  colspan="2">No data found!</td>        
                                                            </tr>                                                            
                                                        @endif
                                                       
                                                        </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>           
                                        </div>                   


                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>

                </div>

            </div>

        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#overlay').hide();
            $('#page_content').show();
            $('.dropdown-toggle').dropdown();

            $('a[href="#word"]').click(function(){
                $('#report_type').val('word');
                $("#downloadForm").submit();
            }); 
            $('a[href="#pdf"]').click(function(){
                $('#report_type').val('pdf');
                $("#downloadForm").submit();
            }); 
            $('a[href="#googledoc"]').click(function(){
                $('#report_type').val('googledoc');
                $("#downloadForm").submit();
            });                         
        });
    </script>
@endsection
