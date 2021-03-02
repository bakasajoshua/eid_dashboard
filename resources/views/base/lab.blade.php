@extends('layouts.master')

@section('content')
<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    LAB PERFORMANCE STATS ON EID <div class="display_date"></div>
                </div>
                <div class="panel-body">
                    <div id="lab_perfomance_stats"><center><div class="loader"></div></center></div>
                    <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Tests with valid outcomes (Trends) <div class="display_date"></div>
                </div>
                <div class="panel-body">
                    <div id="test_trends"><center><div class="loader"></div></center></div>
                    <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Tests with valid outcomes <div class="display_date"></div>
                </div>
                <div class="panel-body">
                    <div id="test_outcomes"><center><div class="loader"></div></center></div>
                    <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Positivity Trends (Initial PCR) <div class="display_date"></div>
                </div>
                <div class="panel-body">
                    <div id="positivity_trends"><center><div class="loader"></div></center></div>
                    <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Rejected Trends <div class="display_date"></div>
                </div>
                <div class="panel-body">
                    <div id="rejected_trends"><center><div class="loader"></div></center></div>
                    <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                    </div>
                </div>
            </div>
        </div>
        <div id="graphs"></div>
        <div id="stacked_graph" class="col-md-6"></div>
    </div>
    <div id="lineargauge"></div>
</div>

<div id="second">
    <div id="lab_summary_two_years"></div>
    <div id="trends_lab"></div>
</div>

<div id="third">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Samples Rejections <div class="display_date"></div>
                </div>
                <div class="panel-body" id="lab_rejections">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fourth">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Mapping <div class="display_date"></div>
                </div>
                <div class="panel-body" id="mapping" style="height: 700px">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fifth">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    POC Hub-Spoke Stats <div class="display_date"></div>
                </div>
                <div class="panel-body" id="poc">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    POC Outcomes <div class="display_date"></div>
                </div>
                <div class="panel-body" id="poc_outcomes">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="my_empty_div"></div>
@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(labFilter = null)
  {
        var filter_value = $("#{{ $filter_name }}").val();

        if (filter_value && filter_value != 'null') {
	        $("#first").hide();
	        $("#second").show();
	        $("#fourth").show();
	        $("#fifth").hide();
	        $("#breadcrum").show();

	        if(filter_value==11 || filter_value =='11'){
	          $("#fourth").hide();
	          $("#fifth").show();      
                $("#poc").load("{{ url('lab/poc_performance_stat') }}");
                $("#poc_outcomes").load("{{ url('lab/poc_outcomes') }}");
	        }
            else{
                $("#mapping").html("<center><div class='loader'></div></center>");
                $("#mapping").load("{{ url('lab/mapping') }}");
            }

        } else {
	        $("#first").show();
	        $("#second").hide();
	        $("#fourth").hide();
	        $("#fifth").hide(); 

            $("#lab_perfomance_stats").html("<center><div class='loader'></div></center>");
            $("#test_trends").html("<center><div class='loader'></div></center>");
            $("#test_outcomes").html("<center><div class='loader'></div></center>");
            $("#positivity_trends").html("<center><div class='loader'></div></center>");
            $("#rejected_trends").html("<center><div class='loader'></div></center>");
            $("#lineargauge").html("<center><div class='loader'></div></center>");

            $("#lab_perfomance_stats").load("{{ url('lab/lab_performance_stat') }}");
            $("#test_trends").load("{{ url('lab/lab_testing_trends/testing') }}");
            $("#test_outcomes").load("{{ url('county/county_outcomes/5') }}");
            $("#positivity_trends").load("{{ url('lab/lab_testing_trends/positivity') }}");
            $("#rejected_trends").load("{{ url('lab/lab_testing_trends/rejection') }}");
            $("#lineargauge").load("{{ url('lab/labs_turnaround') }}");
        }
        
        $("#lab_rejections").html("<center><div class='loader'></div></center>");
	    $("#lab_rejections").load("{{ url('lab/rejections') }}");


  }

    function expand_poc(facility_id)
    {
        $("#my_empty_div").load("{{ url('lab/poc_performance_details') }}/"+facility_id);
    }


  $().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
  });

</script>

@endsection