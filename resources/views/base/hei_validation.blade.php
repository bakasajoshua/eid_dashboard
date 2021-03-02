@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 5em;">
                <div class="col-sm-4">
                    HEI Validations
                    <div class="display_date"></div>
                </div>
            </div>
            <div class="panel-body" id="hei_validation_table"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page()
	{
        $("#hei_validation_table").html("<center><div class='loader'></div></center>");
        $("#hei_validation_table").load("{{ url('summary/hei/validation/' . $type) }}");
	}


	$().ready(function(){
        // reload_page();
        date_filter('yearly', "{{ date('Y') }}");
	});

</script>

@endsection