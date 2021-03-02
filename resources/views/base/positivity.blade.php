@extends('layouts.master')

@section('content')

<style type="text/css">
	.navbar-inverse {
		border-radius: 0px;
	}
	.navbar .container-fluid .navbar-header .navbar-collapse .collapse .navbar-responsive-collapse .nav .navbar-nav {
		border-radius: 0px;
	}
	.panel {
		border-radius: 0px;
	}
	.panel-primary {
		border-radius: 0px;
	}
	.panel-heading {
		border-radius: 0px;
	}
	.list-group-item{
		margin-bottom: 0.1em;
	}
	.btn {
		margin: 0px;
	}
	.alert {
		margin-bottom: 0px;
		padding: 8px;
	}
	.filter {
		margin: 2px 20px;
	}
	.display_date {
		width: 130px;
		display: inline;
	}
	#filter {
		background-color: white;
		margin-bottom: 1.2em;
		margin-right: 0.1em;
		margin-left: 0.1em;
		padding-top: 0.5em;
		padding-bottom: 0.5em;
	}
	#year-month-filter {
		font-size: 12px;
	}

</style>

<div style="margin-bottom: 1em;background-color:#E4F1FE;">
	<div class="alert" style="background-color: #DADFE1;color:black;">
		<center id="notification">
			Positivity: XXXX
		</center>
	</div>  	
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Age Category <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="ageCat">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Infant Prophylaxis <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="iprophylaxis">
		  		<div>Loading...</div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Mother Prophylaxis <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="mprophylaxis">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Entry Point <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="epoint">
		  		<div>Loading...</div>
		  	</div>
		  </div>
		</div>
	</div>
</div>
<div class="row">
	<center><h3>Positivity rates</h3></center>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Counties <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="countys">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Sub counties <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="subcounty">
		  		<div>Loading...</div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Facilities <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="facilities">
		  		<div>Loading...</div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Partners <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="partners">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			  EID County Positivity <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="county_outcomes">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			  EID County Positivity <div class="display_date"></div>
			</div>
		  	<div class="panel-body">
		  	<div id="county_mixed">
		  		<div>Loading...</div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>


@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(countyFilter = null)
	{

		$("#notification").load("{{ url('positivity/notification_bar') }}");

		$("#ageCat").load("{{ url('summary/age2/1') }}");
		$("#iprophylaxis").load("{{ url('summary/dynamic_detailed/iprophylaxis/1') }}");
		$("#mprophylaxis").load("{{ url('summary/dynamic_detailed/mprophylaxis/1') }}");
		$("#epoint").load("{{ url('summary/dynamic_detailed/entry_points/1') }}");

		$("#countys").load("{{ url('positivity/listings/County') }}");
		$("#subcounty").load("{{ url('positivity/listings/Subcounty') }}");
		$("#facilities").load("{{ url('positivity/listings/Facility') }}");
		$("#partners").load("{{ url('positivity/listings/Partner') }}");
		// $("#feeding").load("{{ url('charts/summaries/agegroup') }}");
		
		$("#county_outcomes").load("{{ url('summary/dynamic_outcomes/county/facility/1') }}");
		$("#county_mixed").load("{{ url('summary/dynamic_outcomes/county') }}");


	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection