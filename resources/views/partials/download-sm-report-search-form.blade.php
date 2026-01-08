<style>
    .invalid-feedback {
        display: block !important;
    }

    .select2-container--default .select2-selection--single {
        height: 35px !important;
        border: 1px solid #ced4da;
    }

    .select2-container .select2-selection--multiple {
        min-height: 35px !important;
    }

    .column-5 {
        width: 50%;
        float: left;    
        /* Force width to take into account border size */
        box-sizing: border-box;
    }    

    .border-light {
        border: 1px solid black;
    }    

    .categoryHeading{
        width: 100%;
        text-align: center;
        background-color: aliceblue;
    }

    .sm_cat_checkbox {
        text-align: left;
        width: 93%;
    }    

    #sm_cat_container{
        text-align: left;
        /* width: 100%;         */
    }
    #sm_sub_cat_container{
        text-align: left;
        /* width: 100%;         */
    }     
    

    .dm_report_icon{
        position: relative;
        top: 2px;
        margin-left: 4px;
        font-size: 20px;
    }    
    
</style>
@php
    // dump($params);
    $app_domain = config('app.app_domain');
    $domain = $app_domain.$website_slug."/";
@endphp



<div class="row">
    <form target="_blank" action="{{ route('smmonitoring.pdfview', $website_slug, ['downloadWord' => 'word']) }}" method="get"
        autocomplete="off" id="sm_downloadForm">

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <h1 class="search_heading_{{ config('app.color_scheme') }}">Search social media database</h1>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card cardFullHeight">
                    <div class="card-body">

                         {{-- Issues --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{-- Issues --}}
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="sm_team_name" class="form-label">Issues</label> <br />
                                        <div role="group" aria-label="Basic radio toggle button group">
                                            @foreach ($sm_team as $key => $teams)
                                                @php
                                                  $viewReportUrl = $domain.$teams->slug;
                                                @endphp
                                            <input value="{{ $teams->id }}" type="radio" @if($teams->id==$defaultTeamId) checked @endif
                                                class="btn-check sm_team_name" name="team_name" data-slug="{{ $teams->slug }}"
                                                id="sm_btnradio{{ $teams->id }}" autocomplete="off">
                                            <label class="btn btn-outline-primary"
                                                for="sm_btnradio{{ $teams->id }}">{{ $teams->name }}
                                                <br>({{ $teams->total_reports }} Reports)
                                                <a class="dm_report_icon" href="{{$viewReportUrl}}" target="_blank"><i class="mdi mdi-search-web"></i></a>
                                            </label>
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       
                        <div class="row mb-3" style="display: none;" id="sm_cat_subcat_row">
                            {{-- Category + Sub Category  --}}

                            <div class="col-md-8" id="sm_cat_subcat_container">
                                <div class="row">
                                    {{-- Category --}}
                                    <div class="col-md-6 column-5 border-light" id="sm_cat_container">
                                        <label for="module" class="form-label categoryHeading">
                                            Category - <span id="sm_cat_count">{{count($categoriesData)}}</span>
                                        </label>
                                        <div id="sm_category-btn-group">                                               
                                            @foreach ($categoriesData as $key => $category)
                                            <input value="{{ $category->id }}" type="radio" @if($category->id==$defaultModuleId) checked @endif
                                                class="btn-check sm_category_checkbox" name="module[]"
                                                id="sm_cb{{ $category->id }}" autocomplete="off">
                                            <label class="btn btn-outline-primary sm_cat_checkbox"
                                                for="sm_cb{{ $category->id }}">{{$key+1}}. {{ $category->name }} ({{ $category->total_reports }} Reports)</label>

                                            
                                            @endforeach                                                    
                                        </div>
                                    </div>


                                    {{-- Sub-Category --}}
                                    <div class="col-md-6 column-5 border-light" id="sm_sub_cat_container">
                                        <label for="chapter_id" class="form-label categoryHeading">
                                            Sub-Category - <span id="sm_subcat_count">{{count($subCategoriesData)}}</span>
                                        </label>
                                        <div id="sm_sub-category-btn-group"> 
                                            @foreach ($subCategoriesData as $key => $subCategory)
                                            <input value="{{ $subCategory->id }}" type="radio"
                                                class="btn-check sm_sub_category_checkbox" name="chapter_id[]"
                                                id="sm_scb{{ $subCategory->id }}" autocomplete="off">
                                            <label class="btn btn-outline-primary sm_cat_checkbox"
                                                for="sm_scb{{ $subCategory->id }}">{{$key+1}}. {{ $subCategory->name }} ({{ $subCategory->total_reports }} Reports)</label>
                                            @endforeach                                                                                                     
                                        </div>                                            
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {{-- View Report Button --}}
                                <div class="row">                                    
                                    <div class="col-md-6" style="margin-top: 26px;">
                                        <button id="sm_filter" name="filter"
                                            class="btn btn-{{ config('app.color_scheme') }} form-control"><i
                                                class="mdi mdi-download"></i> View SM Report</button>
                                    </div>                                  
                                    <div class="col-md-6" style="margin-top: 26px;">
                                        <a class="btn btn-secondary" href=" {{ route('smmonitoring.pdf', $website_slug) }}">
                                            <i class="mdi mdi-eraser"></i> Clear All Filters
                                        </a>
                                    </div>                                    
                                </div>

                                <div class="row" style="display: none" id="sm_clear_category_container">      
                                    <div class="col-md-4" style="margin-top: 26px;">
                                        <a class="btn btn-secondary" href="javascript:void(0);" onclick="clearSMCategories();">
                                            <i class="mdi mdi-eraser"></i> Clear Categories
                                        </a>
                                    </div>        
                                </div>                        

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>




    </form>
</div>


<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

<script>

    function clearSMCategories(){
        $('form#sm_downloadForm input[name="team_name"]').removeAttr("checked");
        $('form#sm_downloadForm input[name="module[]"]').removeAttr("checked");
        $('form#sm_downloadForm input[name="chapter_id[]"]').removeAttr("checked");
    }

    function matchCustom(params, data) {
        // If there are no search terms, return all of the data
        if ($.trim(params.term) === '') {
            return data;
        }

        // Do not display the item if there is no 'text' property
        if (typeof data.text === 'undefined') {
            return null;
        }

        // `params.term` should be the term that is used for searching
        // `data.text` is the text that is displayed for the data object
        if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
            return data;
        }

        // custom search using lookup data
        if (typeof $(data.element).data('lookup') !== 'undefined' && $(data.element).data('lookup').toUpperCase()
            .indexOf(params.term.toUpperCase()) == 0) {
            return data;
        }

        // Return `null` if the term should not be displayed
        return null;
    }

    $(document).ready(function() {

        $('#overlay').hide();
        $('#page_content').show();

        $("#sm_from_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: "+0D",
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {

                if (selectedDate) {
                    var idTeam = $('#sm_team_name').val();
                    let idModules = getSMSelectedIdsByClass("sm_category_checkbox");
                    let idChapters = getSMSelectedIdsByClass("sm_sub_category_checkbox");

                    let idLocations = "";
                    var idLocationArr = $('#sm_location_id').select2("val");
                    if (idLocationArr) {
                        idLocations = idLocationArr.toString();
                    }

                    let idLocationStates = "";
                    var idLocationStateArr = $('#sm_location_state_id').select2("val");
                    if (idLocationStateArr) {
                        idLocationStates = idLocationStateArr.toString();
                    }

                    let idLanguages = "";
                    var idLanguageArr = $('#sm_language_id').select2("val");
                    if (idLanguageArr) {
                        idLanguages = idLanguageArr.toString();
                    }

                    var from_user = $('#sm_from_user').val();

                    var to_dateVal = $('#sm_to_date').val();

                    // console.log('from_date', selectedDate);
                    // console.log('to_date', to_dateVal);

                    $.ajax({
                        url: "{{ route('smmonitoring.api.fetch-download-options', $website_slug) }}",
                        // type: "GET",                
                        type: "POST",
                        data: {
                            team_id: idTeam,
                            module_ids: idModules,
                            chapter_ids: idChapters,
                            location_ids: idLocations,
                            location_state_ids: idLocationStates,
                            language_ids: idLanguages,
                            from_date: selectedDate,
                            to_date: to_dateVal,
                            from_user: from_user,
                            mode: 'use_date',
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {

                            // update date_reports
                            $.each(result.date_reports, function(key, value) {
                                $("#sm_from_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                                $("#sm_to_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // update first_time_reports
                            $.each(result.first_time_reports, function(key, value) {
                                $("#sm_first_time_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // update followup_reports
                            $.each(result.followup_reports, function(key, value) {
                                $("#sm_followup_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });


                            // calendar_date_reports
                            $.each(result.calendar_date_reports, function(key, value) {
                                $("#sm_calendar_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // fir_documents_reports
                            $.each(result.fir_documents_reports, function(key, value) {
                                $("#sm_fir_documents_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // total report
                            $.each(result.total_reports, function(key, value) {
                                $("#sm_total_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            //update users
                            $('#sm_from_user').html(
                                '<option value="">Select User</option>');
                            $.each(result.users, function(key, value) {
                                $("#sm_from_user").append('<option value="' + value
                                    .id + '">' + value.name + ' [ ' + value
                                    .email + ' ] ' + ' ( ' + value
                                    .total_reports + ' Reports )' +
                                    '</option>');
                            });


                        }
                    });
                }
            }
        });

        $("#sm_to_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: "+0D",
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {

                if (selectedDate) {

                    var idTeam = $('#sm_team_name').val();
                    let idModules = getSMSelectedIdsByClass("sm_category_checkbox");
                    let idChapters = getSMSelectedIdsByClass("sm_sub_category_checkbox");
                    
                    let idLocations = "";
                    var idLocationArr = $('#sm_location_id').select2("val");
                    if (idLocationArr) {
                        idLocations = idLocationArr.toString();
                    }

                    let idLocationStates = "";
                    var idLocationStateArr = $('#sm_location_state_id').select2("val");
                    if (idLocationStateArr) {
                        idLocationStates = idLocationStateArr.toString();
                    }

                    let idLanguages = "";
                    var idLanguageArr = $('#sm_language_id').select2("val");
                    if (idLanguageArr) {
                        idLanguages = idLanguageArr.toString();
                    }

                    var from_user = $('#sm_from_user').val();

                    var from_dateVal = $('#sm_from_date').val();

                    // console.log('from_date', from_dateVal);
                    // console.log('to_date', selectedDate);

                    $.ajax({
                        url: "{{ route('smmonitoring.api.fetch-download-options', $website_slug) }}",
                        // type: "GET",                
                        type: "POST",
                        data: {
                            team_id: idTeam,
                            module_ids: idModules,
                            chapter_ids: idChapters,
                            location_ids: idLocations,
                            location_state_ids: idLocationStates,
                            language_ids: idLanguages,
                            from_date: from_dateVal,
                            to_date: selectedDate,
                            from_user: from_user,
                            mode: 'use_date',
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {

                            // update date_reports
                            $.each(result.date_reports, function(key, value) {
                                $("#sm_from_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                                $("#sm_to_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // update first_time_reports
                            $.each(result.first_time_reports, function(key, value) {
                                $("#sm_first_time_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // update followup_reports
                            $.each(result.followup_reports, function(key, value) {
                                $("#sm_followup_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });


                            // calendar_date_reports
                            $.each(result.calendar_date_reports, function(key, value) {
                                $("#sm_calendar_date_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // fir_documents_reports
                            $.each(result.fir_documents_reports, function(key, value) {
                                $("#sm_fir_documents_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });

                            // total report
                            $.each(result.total_reports, function(key, value) {
                                $("#sm_total_reports").html('(' + value
                                    .total_reports + ' Reports)');
                            });


                            //update users
                            $('#sm_from_user').html(
                                '<option value="">Select User</option>');
                            $.each(result.users, function(key, value) {
                                $("#sm_from_user").append('<option value="' + value
                                    .id + '">' + value.name + ' [ ' + value
                                    .email + ' ] ' + ' ( ' + value
                                    .total_reports + ' Reports )' +
                                    '</option>');
                            });


                        }
                    });

                }
            }
        });

     
        // Issues
        // $('#sm_team_name').on('change', function () {
        $('form#sm_downloadForm input[name="team_name"]').click(function() {

            console.log("inside sm team_name");

            $('#overlay').show();

            var idTeam = this.value;

            console.log('idTeam', idTeam);

            $("#sm_category-btn-group").html('');
            $("#sm_sub-category-btn-group").html('');
           
            $('#sm_total_reports').empty();
        

            if (idTeam) {

                //clear dates if report_type is daily_report
                var selectedReportType = $('form#sm_downloadForm input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    $('#sm_from_date').val('');
                    $('#sm_to_date').val('');
                }

                $.ajax({
                    url: "{{ route('smmonitoring.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        team_id: idTeam,
                        mode: 'modules',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                      
                        var container = $('#sm_category-btn-group');
                        var inputs = container.find('input');                       
                        $.each(result.modules, function(key, value) {
                            var id = key+1;                    
                            
                            $('<input />', { type: 'checkbox', class: 'btn-check sm_category_checkbox', id: 'sm_cb'+id, name: 'module[]', value: value.id, checked: (id==1) ? false : false }).appendTo(container);
                            $('<label />', { 'for': 'sm_cb'+id, class:'btn btn-outline-primary sm_cat_checkbox', text: id + '. ' + value.name + '\n' + '( ' + value.total_reports + ' Reports )' }).appendTo(container);                            

                            var spanIcon = '<i class="mdi mdi-search-web"></i>';
                            // var hrefUrl = '{{config('app.app_domain').$website_slug}}/pdfview?fromPage=dmr&team_name='+idTeam+'&module[]='+value.id+'&from_date=&to_date=&from_user=&download_file_name=&download_file_heading=&download_file_title=&tags=&view_reports=yes&report_type=word&news_order_by_order=desc&report_data_type=daily_report&table_report_state_order=state_asc&table_report_state_min_cases=5&hashtags=&document_report_tags='
                            var hrefUrl = '{{config('app.app_domain').$website_slug}}/'+value.team_slug + "/" + value.module_slug;
                            

                            $('<a />', { 'for': 'sm_sp_cb'+id, class:'dm_report_icon', html: spanIcon , href:hrefUrl, target:'_blank' }).appendTo(container);

                        });
                        
                        
                        $("#sm_cat_count").html(result.modules.length);

                        //set sub categories
                        // var container = $('#sm_sub-category-btn-group');
                        // var inputs = container.find('input');                       
                        // $.each(result.chapters, function(key, value) {
                        //     var id = key+1;                    
                        //     $('<input />', { type: 'checkbox', class: 'btn-check sm_sub_category_checkbox', id: 'sm_scb'+id, name: 'chapter_id[]', value: value.id }).appendTo(container);
                        //     $('<label />', { 'for': 'sm_scb'+id, class:'btn btn-outline-primary sm_cat_checkbox', text: id + '. ' + value.name + '\n' + '( ' + value.total_reports + ' Reports )' }).appendTo(container);                            
                        //     // $('<br /><br />').appendTo(container);

                        //     var spanIcon = '<i class="mdi mdi-search-web"></i>';
                        //     //chapter_id
                        //     // var hrefUrl = '{{config('app.app_domain').$website_slug}}/pdfview?fromPage=dmr&team_name='+idTeam+'&chapter_id[]='+value.id+'&from_date=&to_date=&from_user=&download_file_name=&download_file_heading=&download_file_title=&tags=&view_reports=yes&report_type=word&news_order_by_order=desc&report_data_type=daily_report&table_report_state_order=state_asc&table_report_state_min_cases=5&hashtags=&document_report_tags='
                        //     var hrefUrl = '{{config('app.app_domain').$website_slug}}/'+value.team_slug+ "/" + value.module_slug+ "/" + value.chapter_slug;

                        //     $('<a />', { 'for': 'sm_sp_cb'+id, class:'dm_report_icon', html: spanIcon , href:hrefUrl, target:'_blank'  }).appendTo(container);


                        // });

                        // $("#sm_subcat_count").html(result.chapters.length);

                        $("#sm_subcat_count").html(0)

                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#sm_total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        $('#sm_cat_subcat_row').show();                        

                        $('#overlay').hide();
                    }
                });
            } else {
                //clear dates if report_type is daily_report
                var selectedReportType = $('form#sm_downloadForm input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    setDefaultDate();
                }
                $('#overlay').hide();
            }
        });

        // Category
        // $('#sm_module').on('change', function() {
        $( 'body' ).on( 'click', '.sm_category_checkbox', function () { 

            // console.log("inside sm_category_checkbox");

            var idTeam = $('form#sm_downloadForm input[name="team_name"]:checked').val();
            console.log("inside sm_category_checkbox ", idTeam);

            
            let idModules = getSMSelectedIdsByClass("sm_category_checkbox");
            // console.log('idModules', idModules);   

            $('#overlay').show();
            
            // clear sub category container
            $("#sm_sub-category-btn-group").html('');
            
            if (idModules) {

                //clear dates if report_type is daily_report
                var selectedReportType = $('form#sm_downloadForm input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    $('#sm_from_date').val('');
                    $('#sm_to_date').val('');
                }


                $.ajax({
                    url: "{{ route('smmonitoring.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        module_ids: idModules,
                        mode: 'chapters',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                       

                        var container = $('#sm_sub-category-btn-group');
                        var inputs = container.find('input');                       
                        $.each(result.chapters, function(key, value) {
                            var id = key+1;                    
                            $('<input />', { type: 'checkbox', class: 'btn-check sm_sub_category_checkbox', id: 'sm_scb'+id, name: 'chapter_id[]', value: value.id }).appendTo(container);
                            $('<label />', { 'for': 'sm_scb'+id, class:'btn btn-outline-primary sm_cat_checkbox', text: id + '. ' + value.name + '\n' + '( ' + value.total_reports + ' Reports )' }).appendTo(container);                            
                            // $('<br /><br />').appendTo(container);

                            var spanIcon = '<i class="mdi mdi-search-web"></i>';
                            //chapter_id
                            // var hrefUrl = '{{config('app.app_domain').$website_slug}}/pdfview?fromPage=dmr&team_name='+idTeam+'&chapter_id[]='+value.id+'&from_date=&to_date=&from_user=&download_file_name=&download_file_heading=&download_file_title=&tags=&view_reports=yes&report_type=word&news_order_by_order=desc&report_data_type=daily_report&table_report_state_order=state_asc&table_report_state_min_cases=5&hashtags=&document_report_tags='
                            
                            var hrefUrl = '{{config('app.app_domain').$website_slug}}/'+value.team_slug+ "/" + value.module_slug+ "/" + value.chapter_slug;

                            $('<a />', { 'for': 'sm_sp_cb'+id, class:'dm_report_icon', html: spanIcon , href:hrefUrl, target:'_blank'  }).appendTo(container);

                        });

                        $("#sm_subcat_count").html(result.chapters.length);

                        // $("#sm_sub_cat_container").show();


                        $('#overlay').hide();
                    }
                });
            } else {
                // var idTeam = $('#sm_team_name').val();
                // $('#sm_team_name').val(idTeam); // Select the option with a value of '1'
                // $('#sm_team_name').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }

        });

        // Sub Category
        // $('#sm_chapter_id').on('change', function() {
        $( 'body' ).on( 'click', '.sm_sub_category_checkbox', function () {             

            // console.log("inside sm_sub_category_checkbox");
            $('#overlay').show();

            let idChapters = getSMSelectedIdsByClass("sm_sub_category_checkbox");
            console.log('idChapters', idChapters);   


            if (idChapters) {

                //clear dates if report_type is daily_report
                var selectedReportType = $('form#sm_downloadForm input[name="report_data_type"]:checked').val();
                if (selectedReportType == "daily_report") {
                    $('#sm_from_date').val('');
                    $('#sm_to_date').val('');
                }

                $.ajax({
                    url: "{{ route('smmonitoring.api.fetch-download-options', $website_slug) }}",
                    // type: "GET",                
                    type: "POST",
                    data: {
                        chapter_ids: idChapters,
                        mode: 'sub-category',
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {


                        // total report
                        $.each(result.total_reports, function(key, value) {
                            $("#sm_total_reports").html('(' + value.total_reports +
                                ' Reports)');
                        });

                        $('#overlay').hide();
                    }
                });
            } else {
                // var idModule = $('#sm_module').val();
                // $('#sm_module').val(idModule); // Select the option with a value of '1'
                // $('#sm_module').trigger('change'); // Notify any JS components that the value changed
                $('#overlay').hide();
            }

        });

        $("#sm_filter").click(function() {
            var selectedReportType = $('form#sm_downloadForm input[name="report_data_type"]:checked').val();

            var selectedReportTypeFormated = selectedReportType.replace(/\_/g, ' ');

            var fromDate = $("#sm_from_date").val();
            var toDate = $("#sm_to_date").val();
            var idTeam = $('#sm_team_name').val();
            let idModules = getSMSelectedIdsByClass("sm_category_checkbox");
            let idChapters = getSMSelectedIdsByClass("sm_sub_category_checkbox");

            var selectedTeamText = $('#sm_team_name option:selected').text();
            var selectedModuleText = $('#sm_module option:selected').text();
            var selectedChapterText = $('#sm_chapter_id option:selected').text();

            var finalMsgText = '';
            if (idChapters != '') {
                finalMsgText = selectedChapterText;
            } else if (idModules != '') {
                finalMsgText = selectedModuleText;
            } else {
                finalMsgText = selectedTeamText;
            }

            if (selectedReportType == 'tag_report') {
                const myArray = finalMsgText.split(" (");
                finalMsgText = myArray[0];
            }

            if ((fromDate == '' || toDate == '') && idTeam == '') {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops',
                    html: 'Please select dates or an Issue to download ' +
                        selectedReportTypeFormated,
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {

                    } else if (result.isDenied) {

                    }
                })
            } else if ((fromDate == '' || toDate == '') && idTeam != '') {

                event.preventDefault();
                Swal.fire({
                    icon: 'success',
                    title: 'Note',
                    html: 'No date is selected, this will download ' +
                        selectedReportTypeFormated + ' for ' + finalMsgText,
                    showCancelButton: true,
                    confirmButtonColor: '#2DA5D1',
                    confirmButtonText: 'OK',
                    cancelButtonText: "No, cancel it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // $('form#sm_downloadForm').submit();

                        var datastring = $("form#sm_downloadForm").serialize();
                        checkSMDownloadReportCount(datastring);


                    } else if (result.isDenied) {
                        return false;
                    }
                })
            } else {
                event.preventDefault();
                var datastring = $("form#sm_downloadForm").serialize();
                checkSMDownloadReportCount(datastring);
            }
        });

        function checkSMDownloadReportCount(datastring) {
            var datastringUpdated = datastring + "&mode=getcount";
            $.ajax({
                type: "GET",
                url: "{{ route('smmonitoring.pdfview', $website_slug, ['downloadWord' => 'word']) }}",
                data: datastringUpdated,
                success: function(result) {
                    //  alert('Data send');
                    if (result > 0) {
                        // console.log('Download result', result);
                        $('form#sm_downloadForm').submit();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            html: 'No reports found for selected filter attributes',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            } else if (result.isDenied) {

                            }
                        })
                    }
                }
            });
        }

        //setDefaultDate();

        function setDefaultDate() {

            var date = new Date();
            console.log('date', date);

            var date = new Date(Date.now() - 864e5);
            console.log('date', date);

            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            if (month < 10) month = "0" + month;
            if (day < 10) day = "0" + day;
            var yesterday = day + "-" + month + "-" + year;
            document.getElementById("from_date").value = yesterday;
            document.getElementById("to_date").value = yesterday;
            console.log('yesterday', yesterday);

        }

        function getSMSelectedIdsByClass(className){
            console.log('getSMSelectedIdsByClass className', className);        
            let idSelected = "";
            let elementChecked = "."+className+":checkbox:checked";

            var checkedVals = $(elementChecked).map(function() {
                return this.value;
            }).get();
            idSelected = checkedVals.join(",");            

            console.log('idSelected', idSelected);               
            return idSelected;
        }
    });
</script>
