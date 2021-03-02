@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        TAT calculation is based on working days excluding weekends and public holidays
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" id="heading">
                Partner TAT Outcomes <div class="display_date"></div>
            </div>
            <div class="panel-body" id="county_tat_outcomes"></div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" id="heading">
                Partner TAT Details <div class="display_date"></div>
            </div>
            <div class="panel-body" id="county_tat_details"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page()
	{
        $("#county_tat_outcomes").html("<center><div class='loader'></div></center>");
		$("#county_tat_details").html("<center><div class='loader'></div></center>");
        
        $("#county_tat_outcomes").load("{{ url('tat/tat_outcomes/1') }}");
		$("#county_tat_details").load("{{ url('tat/tat_details/1') }}");
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection