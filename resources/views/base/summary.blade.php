@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-md-12" id="nattatdiv">
		<div class="col-md-6 col-md-offset-3">
			<div class="col-md-4 title-name" id="title">
				<center>National TAT <l style="color:red;">(Days)</l></center>
			</div>
			<div class="col-md-8">
				<div id="nattat"></div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="title-name">Key</div>
			<div class="row">
				<div class="col-md-6">
					<div class="key cr"><center>Collection Receipt (C-R)</center></div>
					<div class="key rp"><center>Receipt to Processing (R-P)</center></div>
				</div>
				<div class="col-md-6">
					<div class="key pd"><center>Processing Dispatch (P-D)</center></div>
					<div class="key"><center><div class="cd"></div>Collection Dispatch (C-D)</center></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" style="min-height: 5em;">
		  	<div class="col-sm-4">
			    Testing Trends
			    <div class="display_range"></div>
			</div>

			<div class="col-sm-5">
			    <h3> <div id="samples_heading">(Initial PCR)</div> </h3>
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
</div>
<div class="row">
	<!-- Map of the country -->
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
		  	Actual Infants Tested Positive Validation at Site Outcomes <div class="display_date" ></div>
		  </div>
		  <div class="panel-body" id="hei_outcomes" style="height:660px;">
		  	<center><div class="loader"></div></center>
		  </div>
		  
		</div>
	</div>

	<!-- Map of the country -->
	<div class="col-md-2 col-sm-4 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
			  Status of Actual Confirmed Positives at Site <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="hei_follow_up" style="height:660px;">
		    <center><div class="loader"></div></center>
		  </div>
		  <!-- <div>
		  	<button class="btn btn-default" onclick="justificationModal();">Click here for breakdown</button>
		  </div> -->
		</div>
	</div>

	<div class="col-md-3">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    EID Outcomes by Age  (Initial PCR) <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="ageGroups" style="height:660px;">
		    <center><div class="loader"></div></center>
		  </div>
		  <!-- <div>
		  	<button class="btn btn-default" onclick="ageModal();">Click here for breakdown</button>
		  </div> -->
		</div>
	</div>
</div>

<div class="row">
	<!-- Map of the country -->
	<div class="col-md-4 col-sm-3 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	EID Outcomes by Entry Point (Initial PCR) <div class="display_date" ></div>
		  </div>
		  <div class="panel-body" id="entry_point">
		  	<center><div class="loader"></div></center>
		  </div>
		  
		</div>
	</div>

	<!-- Map of the country -->
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
			  EID Outcomes by Mother PMTCT Regimen (Initial PCR)<div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="mprophilaxis" style="/*height:500px;">
		    <center><div class="loader"></div></center>
		  </div>
		  <!-- <div>
		  	<button class="btn btn-default" onclick="justificationModal();">Click here for breakdown</button>
		  </div> -->
		</div>
	</div>
	<!-- Map of the country -->
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
			  EID Outcomes  by Infant Prophylaxis (Initial PCR) <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="iprophilaxis" style="/*height:500px;">
		    <center><div class="loader"></div></center>
		  </div>
		  <!-- <div>
		  	<button class="btn btn-default" onclick="justificationModal();">Click here for breakdown</button>
		  </div> -->
		</div>
	</div>
</div>
<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	County Outcomes <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="county_outcomes">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="agemodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Age Category Breakdown</h4>
      </div>
      <div class="modal-body" id="CatAge">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="justificationmodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pregnant and Lactating Mothers</h4>
      </div>
      <div class="modal-body" id="CatJust">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')

<script type="text/javascript">

	function reload_page()
	{
    	$("#nattat").html("<center><div class='loader'></div></center>");
    	$("#testing_trends").html("<center><div class='loader'></div></center>");
    	$("#eidOutcomes").html("<center><div class='loader'></div></center>");
        $("#hei_outcomes").html("<center><div class='loader'></div></center>");
        $("#hei_follow_up").html("<center><div class='loader'></div></center>");
		$("#ageGroups").html("<center><div class='loader'></div></center>");
		$("#entry_point").html("<center><div class='loader'></div></center>");
		$("#mprophilaxis").html("<center><div class='loader'></div></center>");
		$("#iprophilaxis").html("<center><div class='loader'></div></center>");
		$("#county_outcomes").html("<center><div class='loader'></div></center>");

		$("#nattat").load("{{ url('summary/turnaroundtime') }}");
		$("#testing_trends").load("{{ url('summary/test_trends') }}");
		$("#eidOutcomes").load("{{ url('summary/eid_outcomes') }}");
		$("#hei_outcomes").load("{{ url('summary/hei_validation') }}");
		$("#hei_follow_up").load("{{ url('summary/hei_follow') }}");
		$("#ageGroups").load("{{ url('summary/age2') }}");

		$("#entry_point").load("{{ url('summary/dynamic_detailed/entry_points') }}");
		$("#mprophilaxis").load("{{ url('summary/dynamic_detailed/mprophylaxis') }}");
		$("#iprophilaxis").load("{{ url('summary/dynamic_detailed/iprophylaxis') }}");
		// $("#feeding").load("{{ url('summary/agegroup') }}");
		
		$("#county_outcomes").load("{{ url('summary/dynamic_outcomes/county') }}");
	}


	$().ready(function(){

		// reload_page();
		date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection
