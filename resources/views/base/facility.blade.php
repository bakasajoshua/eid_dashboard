@extends('layouts.master')

@section('content')

<div id="first">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          Facilities Outcomes <div class="display_date"></div>
        </div>
          <div class="panel-body" id="siteOutcomes">
            <center><div class="loader"></div></center>
          </div>
      </div>
    </div>
  </div>
 
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Facilities Without Supporting Partner <div class="display_date"></div>
                </div>
                <div class="panel-body" id="unsupportedSites">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="second">
  <div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="panel panel-default">
        
        <div class="panel-body" id="tsttrends">
          <center><div class="loader"></div></center>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="panel panel-default">
        
        <div class="panel-body" id="stoutcomes">
          <center><div class="loader"></div></center>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <!-- Map of the country -->
    <div class="col-md-4 col-sm-12 col-xs-12">
      <div class="panel panel-default">
      <div class="panel-heading">
        EID Outcomes <div class="display_date" ></div>
      </div>
       
        <div id="eidOutcomes" style="height: 623px;">
          <center><div class="loader"></div></center>
        </div>
        
      </div>
    </div>

    <div class="col-md-3 col-sm-12 col-xs-12">
      <div class="panel panel-default">
      <div class="panel-heading">
        Actual Infants Tested Positive Validation at Site Outcomes <div class="display_date" ></div>
      </div>
       
        <div id="heiOutcomes" style="height: 623px;">
          <center><div class="loader"></div></center>
        </div>
        
      </div>
    </div>
    
    <div class="col-md-2">
      <div class="panel panel-default">
      <div class="panel-heading">
        Status of Actual Confirmed Positives at Site <div class="display_date"></div>
      </div>
        
        <div class="panel-body" id="heiFollowUp" style="height: 623px;">
          <center><div class="loader"></div></center>
        </div>
        
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
      <div class="panel-heading">
        EID Outcomes by Age  (Initial PCR) <div class="display_date"></div>
      </div>
        
        <div class="panel-body" id="agebreakdown" style="height: 623px;">
          <center><div class="loader"></div></center>
        </div>
        
      </div>
    </div>
    <!-- Entry Point; Mother Prophylazis; Infant Prophylaxis -->
    <div class="col-md-4">
      <div class="panel panel-default">
      <div class="panel-heading">
        EID Outcomes by Entry Point (Initial PCR) <div class="display_date" ></div>
      </div>
        <div class="panel-body" id="entrypoint">
          <center><div class="loader"></div></center>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-default">
      <div class="panel-heading">
        EID Outcomes by Mother PMTCT Regimen (Initial PCR)<div class="display_date"></div>
      </div>
        <div class="panel-body" id="mprophylaxis">
          <center><div class="loader"></div></center>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-default">
      <div class="panel-heading">
        EID Outcomes  by Infant Prophylaxis (Initial PCR) <div class="display_date"></div>
      </div>
        <div class="panel-body" id="iprpophylaxis">
          <center><div class="loader"></div></center>
        </div>
      </div>
    </div>
    <!-- Entry Point; Mother Prophylazis; Infant Prophylaxis -->   
  </div>

</div>

@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(countyFilter = null)
  {
        var filter_value = $("#{{ $filter_name }}").val();

        if (filter_value && filter_value != 'null') {
            $("#first").hide();
            $("#second").show(); 

            $("#tsttrends").html("<center><div class='loader'></div></center>");
            $("#stoutcomes").html("<center><div class='loader'></div></center>");
            $("#eidOutcomes").html("<center><div class='loader'></div></center>");
            $("#heiOutcomes").html("<center><div class='loader'></div></center>");
            $("#heiFollowUp").html("<center><div class='loader'></div></center>");
            $("#agebreakdown").html("<center><div class='loader'></div></center>");
            $("#entrypoint").html("<center><div class='loader'></div></center>");
            $("#mprophylaxis").html("<center><div class='loader'></div></center>");
            $("#iprpophylaxis").html("<center><div class='loader'></div></center>");

            $("#tsttrends").load("{{ url('facility/get_trends') }}");
            $("#stoutcomes").load("{{ url('facility/get_positivity') }}");
            $("#eidOutcomes").load("{{ url('summary/eid_outcomes') }}");
            $("#heiOutcomes").load("{{ url('summary/hei_validation') }}");
            $("#heiFollowUp").load("{{ url('summary/hei_follow') }}");
            $("#agebreakdown").load("{{ url('summary/age2') }}");

            $("#entrypoint").load("{{ url('summary/dynamic_detailed/entry_points') }}");
            $("#mprophylaxis").load("{{ url('summary/dynamic_detailed/mprophylaxis') }}");
            $("#iprpophylaxis").load("{{ url('summary/dynamic_detailed/iprophylaxis') }}");
        } else {
            $("#second").hide();
            $("#first").show();  

            // first
            $("#siteOutcomes").html("<center><div class='loader'></div></center>");
            $("#siteOutcomes").load("{{ url('county/county_outcomes/4') }}");
        }


  }


  $().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");

        $("#unsupportedSites").html("<center><div class='loader'></div></center>");
        $("#unsupportedSites").load("{{ url('facility/unsupported_sites') }}");
  });

</script>

@endsection