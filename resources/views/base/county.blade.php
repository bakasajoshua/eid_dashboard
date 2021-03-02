@extends('layouts.master')

@section('content')
<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    County Outcomes (Initial PCR) <div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_outcomes"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    County Details <div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_details"></div>
            </div>
        </div>
    </div>
</div>
<div id="second">
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    Sub-County Outcomes <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_outcomes">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    Sub-County Positivity <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_positivity">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    County Sub-County Details <div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_sub_county_details">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    County Partners Details <div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_partners_details">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    County Facilities Details <div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_facilities_details">
                    <center>
                        <div class="loader"></div>
                    </center>
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

        let county;
        if (countyFilter !== null) { county = countyFilter.county_filter; }
        if (typeof county === 'undefined') {
            $("#second").hide();
            $("#first").show();   
        } else {
            $("#first").hide();
            $("#second").show();
        }
        // first
		$("#county_outcomes").html("<center><div class='loader'></div></center>");
		$("#county_details").html("<center><div class='loader'></div></center>");

        // second
        $("#subcounty_outcomes").html("<center><div class='loader'></div></center>");
        $("#subcounty_positivity").html("<center><div class'loader></div></center>");
        $("#county_sub_county_details").html("<center><div class='loader'></div></center>");
        $("#county_partners_details").html("<center><div class='loader'></div></center>");
        $("#county_facilities_details").html("<center><div class='loader'></div></center>");
        
        if(filter_value && filter_value != 'null'){
            $("#subcounty_outcomes").load("{{ url('county/subcounty/outcomes') }}");
            $("#subcounty_positivity").load("{{ url('county/subcounty/positivity') }}");
            $("#county_sub_county_details").load("{{ url('county/details/county/sub_county') }}");
            $("#county_partners_details").load("{{ url('county/details/county/partner') }}");
            $("#county_facilities_details").load("{{ url('county/details/county/facilities') }}");
        }else{
            $("#county_outcomes").load("{{ url('county/county_outcomes') }}");
            $("#county_details").load("{{ url('county/details/county/county') }}");

        }
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection