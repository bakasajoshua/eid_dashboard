@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Testing Trends <div class="display_date"></div>
            </div>
            <div class="panel-body">
                <div id="testing_trends"><center><div class="loader"></div></center></div>
                <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            Summary Outcomes <div class="display_date" ></div>
          </div>
          <div class="panel-body" id="eid_outcomes">
            <center><div class="loader"></div></center>
          </div>
          
        </div>
    </div>

    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            Outcomes By EntryPoint <div class="display_date" ></div>
          </div>
          <div class="panel-body" id="entrypoints">
            <center><div class="loader"></div></center>
          </div>
          
        </div>
    </div>

    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            Outcomes By Age <div class="display_date" ></div>
          </div>
          <div class="panel-body" id="ages">
            <center><div class="loader"></div></center>
          </div>
          
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                County Outcomes <div class="display_date"></div>
            </div>
            <div class="panel-body">
                <div id="county_outcomes"><center><div class="loader"></div></center></div>
                <div class="col-md-12" style="margin-top: 1em;margin-bottom: 1em;">
                </div>
            </div>
        </div>
    </div>
</div>

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

<div id="my_empty_div"></div>
@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(countyFilter = null)
  {
    $("#testing_trends").html("<center><div class='loader'></div></center>");
    $("#eid_outcomes").html("<center><div class='loader'></div></center>");
    $("#entrypoints").html("<center><div class='loader'></div></center>");
    $("#ages").html("<center><div class='loader'></div></center>");
    $("#county_outcomes").html("<center><div class='loader'></div></center>");
    $("#poc").html("<center><div class='loader'></div></center>");

    $("#testing_trends").load("{{ url('poc/testing_trends') }}");
    $("#eid_outcomes").load("{{ url('poc/eid_outcomes') }}");
    $("#entrypoints").load("{{ url('poc/entrypoints') }}");
    $("#ages").load("{{ url('poc/ages') }}");
    $("#county_outcomes").load("{{ url('poc/county_outcomes') }}");
    $("#poc").load("{{ url('lab/poc_performance_stat') }}");
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