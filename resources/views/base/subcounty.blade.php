@extends('layouts.master')

@section('content')
<div id="first">
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    Sub-County Positivity (Actual Infants) <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_positivity">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
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
    </div>
</div>
<div id="second">
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-4 col-sm-3 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    EID Outcomes <div class="display_date"></div>
                </div>
                <div class="panel-body" id="eid_outcomes">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>

            </div>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Actual Infants Tested Positive Validation at <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_hei_outcomes">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>

            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Status of Actual Confirmed Positives at Site <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_hei_follow_up" style="/*height:500px;">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    EID Outcomes by Age (Initial PCR) <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_age" style="height:560px;">
                    <center>
                        <div class="loader"></div>
                    </center>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading">
                    Facilities <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_facilities">
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
    function reload_page(subcountyFilter = null)
	{
        var filter_value = $("#{{ $filter_name }}").val();
        let subcounty;
        if (subcountyFilter !== null) { subcounty = subcountyFilter.sub_county_filter; }
        if (typeof subcounty === 'undefined') {
            $("#second").hide();
            $("#first").show();
        } else {
            $("#first").hide();
            $("#second").show();
        }

        // first
        $("#subcounty_positivity").html("<center><div class='loader'></div></center>");
		$("#subcounty_outcomes").html("<center><div class='loader'></div></center>");

        $("#subcounty_positivity").load("{{ url('county/county_outcomes/2') }}");
		$("#subcounty_outcomes").load("{{ url('county/counties_details/2') }}");
		
        // second
        $("#eid_outcomes").html("<center><div class='loader'></div></center>");
        $("#subcounty_hei_outcomes").html("<center><div class='loader'></div></center>");
        $("#subcounty_hei").html("<center><div class='loader'></div></center>");
        $("#subcounty_age").html("<center><div class='loader'></div></center>");
        $("#subcounty_facilities").html("<center><div class='loader'></div></center>");

        if(filter_value && filter_value != 'null'){
            $("#eid_outcomes").load("{{ url('summary/eid_outcomes') }}");
            $("#subcounty_hei_outcomes").load("{{ url('summary/hei_validation') }}");
            $("#subcounty_hei_follow_up").load("{{ url('summary/hei_follow') }}");
            $("#subcounty_age").load("{{ url('summary/age') }}");
            $("#subcounty_facilities").load("{{ url('county/details/sub_county/facilities') }}");
        }
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection