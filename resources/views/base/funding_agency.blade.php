@extends('layouts.master')

@section('content')
<div class="row" id="second">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" style="min-height: 5em;">
		  	<div class="col-sm-3">
			    Testing Trends <div id="samples_heading">(Initial PCR)</div>
			    <div class="display_range"></div>
		    </div> 
		    
		    <div class="col-sm-3">
		    	<input type="submit" class="btn btn-primary" id="switchButton" onclick="switch_source()" value="Click to Switch To 2nd/3rd PCR">
		    </div>
		  </div>
		  <div class="panel-body" id="testing_trends">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-4 col-sm-3 col-xs-12">
		<div class="panel panel-default">
		 	<div class="panel-heading">
		  		EID Outcomes <div class="display_date" ></div>
			</div>
		  	<div class="panel-body" id="eidOutcomes">
		  		<center><div class="loader"></div></center>
		  	</div>
		  
		</div>
	</div>
	<div class="col-md-3 col-sm-3 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Actual Infants Tested Positive Validation at Site Outcomes  <div class="display_date" ></div>
		  </div>
		  <div class="panel-body" id="hei_outcomes">
		  	<center><div class="loader"></div></center>
		  </div>
		  
		</div>
	</div>
	<div class="col-md-2 col-sm-4 col-xs-12">
		<div class="panel panel-default">
		  	<div class="panel-heading">
			 	Status of Actual Confirmed Positives at Site <div class="display_date"></div>
		  	</div>
		  	<div class="panel-body" id="hei_follow_up" style="/*height:500px;">
		    	<center><div class="loader"></div></center>
		  	</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
		  	<div class="panel-heading">
		    	EID Outcomes by Age  (Initial PCR) <div class="display_date"></div>
		  	</div>
		  	<div class="panel-body" id="ageGroups" style="height:560px;">
		    	<center><div class="loader"></div></center>
		  	</div>
		</div>
	</div>
	
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Funding Agency Partner Positivity (Initial PCR) <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_positivity">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Funding Agencies Partner Testing Analysis Trends <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_test_analysis_trends">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Funding Agencies Partner Testing Analysis <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_test_analysis">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
		
<div class="row" id="first">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Funding Agencies Positivity (Initial PCR) <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="positivity">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Funding Agencies Testing Analysis <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="test_analysis">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>

	<!-- <div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Partner Test Analysis <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_test_analysis">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div> -->
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page()
	{
        var filter_value = $("#{{ $filter_name }}").val();

		$("#positivity").html("<center><div class='loader'></div></center>");
		$("#test_analysis").html("<center><div class='loader'></div></center>");

		$("#partner_positivity").html("<center><div class='loader'></div></center>");
		$("#eidOutcomes").html("<center><div class='loader'></div></center>");
		$("#hei_outcomes").html("<center><div class='loader'></div></center>");
		$("#hei_follow_up").html("<center><div class='loader'></div></center>");
		$("#ageGroups").html("<center><div class='loader'></div></center>");
		$("#testing_trends").html("<center><div class='loader'></div></center>");
		$("#partner_test_analysis").html("<center><div class='loader'></div></center>");

        if(filter_value && filter_value != 'null'){
			$("#first").hide();
			$("#second").show();

			$("#testing_trends").load("{{ url('summary/test_trends') }}");
			$("#eidOutcomes").load("{{ url('summary/eid_outcomes') }}");
			$("#hei_outcomes").load("{{ url('summary/hei_validation') }}");
			$("#hei_follow_up").load("{{ url('summary/hei_follow') }}");
			$("#ageGroups").load("{{ url('summary/age2') }}");
            $("#partner_positivity").load("{{ url('summary/dynamic_outcomes/agency') }}");
            $("#partner_test_analysis_trends").load("{{ url('county/test_analysis_trends') }}");
            $("#partner_test_analysis").load("{{ url('county/test_analysis/4') }}");
			
        }else{
			$("#first").show();
			$("#second").hide();

            $("#positivity").load("{{ url('summary/dynamic_outcomes/agency') }}");
            $("#test_analysis").load("{{ url('county/test_analysis/4') }}");
        }
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection