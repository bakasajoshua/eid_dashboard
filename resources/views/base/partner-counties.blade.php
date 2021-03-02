@extends('layouts.master')

@section('content')
<style type="text/css">
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
	#excels {
		padding-top: 0.5em;
		padding-bottom: 2em;
	}
</style>

<div class="row" id="first">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Outcomes <div class="display_date"></div>
			</div>
		  	<div class="panel-body" id="siteOutcomes">
		  		<center><div class="loader"></div></center>
		  	</div>
		</div>
	</div>
</div>

<div class="row" id="second">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			  Partner Counties <div class="display_date"></div>
			</div>
		  	<div class="panel-body" id="partnerSites">
		  		<center><div class="loader"></div></center>
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

		$("#siteOutcomes").html("<center><div class='loader'></div></center>");
		$("#partnerSites").html("<center><div class='loader'></div></center>");

        $("#siteOutcomes").load("{{ url('summary/dynamic_outcomes/partner/county') }}"); 
        
        if(filter_value && filter_value != 'null'){
            // $("#first").hide();
            $("#second").show();
            $("#partnerSites").load("{{ url('county/details/partner/county') }}"); 
        }else{
            $("#second").hide();
            // $("#first").show();  
        }
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection