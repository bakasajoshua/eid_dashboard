@extends('layouts.master')

@section('content')
<style type="text/css">
	/*.display_date {
		width: 130px;
		display: inline;
	}
	.display_range {
		width: 130px;
		display: inline;
	}*/
	.title-name {
		color: blue;
	}
	#title {
		padding-top: 1.5em;
	}
	
</style>

<div id="first">
	<div class="row">
		<!-- Map of the country -->
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Outcomes by Regimen <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="regimen_outcomes">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>
</div>

<div id="second">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Regimen Testing Trends <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="outcomesRegimen">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Regimen Outcomes by County <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="outcomesBycounty">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Regimen Outcomes by Sub-county<div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="outcomesBysubcounty">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Regimen Outcomes by Partner<div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="outcomesBypartner">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Map of the country -->
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  	Regimen County Outcomes <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="coutnyRegimenOutcomes">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>
</div>


@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(countyFilter = null)
	{
        var filter_value = $("#{{ $filter_name }}").val();

		$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
		$("#outcomesRegimen").html("<center><div class='loader'></div></center>");
		$("#coutnyRegimenOutcomes").html("<center><div class='loader'></div></center>");

        $("#outcomesBycounty").html("<center><div class='loader'></div></center>");
		$("#outcomesBysubcounty").html("<center><div class='loader'></div></center>");
        $("#outcomesBypartner").html("<center><div class='loader'></div></center>");
        
        if(filter_value && filter_value != 'null'){
            $("#first").hide();
            $("#second").show();
            $("#outcomesRegimen").load("{{ url('regimen/testing_trends') }}"); 
            $("#coutnyRegimenOutcomes").load("{{ url('regimen/get_counties_breakdown') }}"); 
            
            $("#outcomesBycounty").load("{{ url('positivity/regimen_listings/County') }}"); 
            $("#outcomesBysubcounty").load("{{ url('positivity/regimen_listings/Subcounty') }}"); 
            $("#outcomesBypartner").load("{{ url('positivity/regimen_listings/Partner') }}"); 
        }else{
            $("#second").hide();
            $("#first").show();  
            $("#regimen_outcomes").load("{{ url('regimen/regimen_outcomes') }}"); 
        }
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection