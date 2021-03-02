<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- about this site -->
	<meta name="description" content="A web platform for early infant diagnosis.">
	<meta name="keywords" content="EID, HIV, AIDS, HIV/AIDS, Kenya">
	<meta name="author" content="NASCOP">
	<meta name="Resource-type" content="Document">



	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />


	<link rel='stylesheet' href='//cdn.datatables.net/1.10.12/css/jquery.dataTables.css' type='text/css' />
	<link rel='stylesheet' href='//cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css' type='text/css' />
	<link rel='stylesheet' href='//cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.css' type='text/css' />
	<link rel="stylesheet" href="{{ asset('css/toastr/toastr.min.css') }}" type="text/css">

	@yield('css_scripts')

	<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />

	<link rel=icon href="{{ asset('img/kenya-coat-of-arms.png') }}" type="image/png" />
	<title> Dashboard </title>
</head>

<body>
	<!-- Begining of Navigation Bar -->
	<div class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-responsive-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="javascript:void(0)" style="padding:0px;padding-top:4px;padding-left:4px;">
					<img src="{{ url('img/nascop_pepfar_logo.jpg') }}" style="width:280px;height:52px;" />
				</a>
			</div>
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">

					<li class="dropdown">
						<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle"
							data-toggle="dropdown">Summaries
							<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ url('/') }}">Summary</a></li>
							<li><a href="{{ url('summary/heivalidation') }}">HEI Validation Summary</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle"
							data-toggle="dropdown">County/Sub-County
							<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ url('county') }}">County</a></li>
							<li><a href="{{ url('county/tat') }}">County TAT</a></li>
							<li><a href="{{ url('subcounty') }}">Sub-County</a></li>
							<li><a href="{{ url('subcounty/tat') }}">Sub-County TAT</a></li>
						</ul>
					</li>
					<li><a href="{{ url('facility') }}">Facilities</a></li>
					<li class="dropdown">
						<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle"
							data-toggle="dropdown">Labs
							<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ url('labPerformance') }}">Lab Performance</a></li>
							<li><a href="{{ url('labPerformance/poc') }}">POC</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle"
							data-toggle="dropdown">Partners
							<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ url('partner') }}">Summary</a></li>
							<li><a href="{{ url('partner/trends') }}">Trends</a></li>
							<li><a href="{{ url('partner/sites') }}">Partner Facilities</a></li>
							<li><a href="{{ url('partner/counties') }}">Partner Counties</a></li>
							<li><a href="{{ url('partner/heivalidation') }}">HEI Validation</a></li>
							<li><a href="{{ url('partner/tat') }}">Partner TAT</a></li>
							<li><a href="{{ url('partner/agencies') }}">Funding Agencies</a></li>
						</ul>
					</li>

					<!-- <li><a href="{{ url('rht') }}">RHT Testing</a></li> -->
					<li class="dropdown">
						<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle"
							data-toggle="dropdown">Positivity
							<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{ url('positivity') }}">Positivity</a></li>
							<li><a href="{{ url('age') }}">Age Analysis</a></li>
							<li><a href="{{ url('regimen') }}">Regimen Analysis</a></li>
						</ul>
					</li>
					<li><a href="{{ url('trends') }}">Trends</a></li>
					<li><a href="https://nascop.org">Resources</a></li>
					<li><a href="https://viralload.nascop.org">VL View</a></li>
					<li><a href="https://viralload.nascop.org/contact-us">Contact Us</a></li>
					<li><a href="https://eiddash.nascop.org/">Login</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- End of Navigation Bar -->
	<!-- Begining of Dashboard area -->
	<div class="container-fluid">

		<div class="row" id="filter">
			<div class="col-md-3">
				@if($dropdown_type == 'Facility')
					<select class="custom-select form-control" id="site_filter">
						<option disabled='true'>Select Facility</option>
						<option value='null' selected='true'>All Facilities</option>

					</select>
				@else
				<select class="custom-select form-control" style="width:220px;background-color: #C5EFF7;"
					id="{{ $filter_name }}" name="{{ $filter_name }}">
					<!-- <option></option> -->
					<option disabled="true" selected="true">Select {{ $dropdown_type }}:</option>
					<option value="null"> {{ $default_option }} </option>
					@foreach($divisions as $division)
					<option value="{{ $division->id }}"> {{ $division->labname ?? $division->name }} </option>
					@endforeach
					@if($dropdown_type == 'Lab')
					<option value="11">POC Sites</option>
					@endif
				</select>
				@endif
			</div>
			<div id="breadcrum" class="col-md-2 alert alert-info">

			</div>
			<div class="col-md-4" id="year-month-filter">
				Year:
				@for ($i = 9; $i > -1; $i--)
				<a href="javascript:void(0)" onclick="date_filter('yearly', {{ (date('Y')-$i) }})" class="alert-link">
					{{ (date('Y')-$i) }} </a>|
				@endfor
				<br />
				Month:
				<a href='javascript:void(0)' onclick='date_filter("monthly", 1)' class='alert-link'> Jan </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 2)' class='alert-link'> Feb </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 3)' class='alert-link'> Mar </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 4)' class='alert-link'> Apr </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 5)' class='alert-link'> May </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 6)' class='alert-link'> Jun </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 7)' class='alert-link'> Jul </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 8)' class='alert-link'> Aug </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 9)' class='alert-link'> Sep </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 10)' class='alert-link'> Oct </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 11)' class='alert-link'> Nov </a>|
				<a href='javascript:void(0)' onclick='date_filter("monthly", 12)' class='alert-link'> Dec</a>
			</div>
			<div class="col-md-2" id="date-range-filter">
				<div class="row" id="range">
					<div class="col-md-4">
						<input name="startDate" id="startDate" class="date-picker" placeholder="From:" />
					</div>
					<div class="col-md-4 endDate">
						<input name="endDate" id="endDate" class="date-picker" placeholder="To:" />
					</div>
					<div class="col-md-4">
						<button id="date_button" class="btn btn-primary date-pickerBtn"
							style="color: white;background-color: #1BA39C; margin-top: 0.2em; margin-bottom: 0em; margin-left: 4em;">
							<center>Filter</center>
						</button>
					</div>
				</div>
				<center>
					<div id="errorAlertDateRange">
						<div id="errorAlert" class="alert alert-danger" role="alert">...</div>
					</div>
				</center>
			</div>
		</div>

		@yield('content')
	</div>

	<div id="errorModal">

	</div>
	<!-- End of Dashboard area -->
</body>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-124819698-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-124819698-1');
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>

<script src="{{ asset('js/highcharts/highcharts.js') }}" type='text/javascript'></script>
<script src="{{ asset('js/highcharts/highcharts-more.js') }}" type='text/javascript'></script>

<script src="{{ asset('js/highcharts/exporting.js') }}" type='text/javascript'></script>
<script src="{{ asset('js/highcharts/export-data.js') }}" type='text/javascript'></script>

<script src="{{ asset('js/highcharts/map.js') }}" type='text/javascript'></script>
<script src="{{ asset('js/highcharts/kenya.js') }}" type='text/javascript'></script>
<script src='//cdn.datatables.net/1.10.12/js/jquery.dataTables.js' type='text/javascript'></script>
<script src='//cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.js' type='text/javascript'></script>
<script src='//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js' type='text/javascript'></script>
<script src='//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js' type='text/javascript'></script>
<script src='//cdn.datatables.net/buttons/1.4.2/js/buttons.colVis.min.js' type='text/javascript'></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js' type='text/javascript'></script>

<script src="{{ url('js/customFunctions1.7.js') }}"></script>

<script type="text/javascript">
	$(function() {
		    $('.date-picker').datepicker( {
		        changeMonth: true,
		        changeYear: true,
		        showButtonPanel: true,
		        dateFormat: 'MM yy',
		        onClose: function(dateText, inst) { 
		            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
		            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
		            $(this).datepicker('setDate', new Date(year, month, 1));
		        }
		    });

		    $("#date_button").click(function () {
			    var first, second;
			    first = $(".date-picker[name=startDate]").val();
			    second = $(".date-picker[name=endDate]").val();

			    if(!first) return;
		    
			    from = format_date(first);
			    /* from is an array
			     	[0] => month
			     	[1] => year*/
			    to 	= format_date(second);

			    var error_check = check_error_date_range(from, to);

			    if (!error_check){
			    	var date_range_data = {'year': from[1], 'month' : from[0], 'to_year': to[1], 'to_month' : to[0]};
			    	date_filter('', date_range_data, "{{ $date_url ?? '' }}");
			    }

		    });
	    	
	        $.ajaxSetup({
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

	        @php
	            $toast_message = session()->pull('toast_message');
	            $toast_error = session()->pull('toast_error');
	        @endphp
	        
	        @if($toast_message)
	            setTimeout(function(){
	                toastr.options = {
	                    closeButton: false,
	                    progressBar: false,
	                    showMethod: 'slideDown',
	                    timeOut: 10000
	                };
	                @if($toast_error)
	                    toastr.error("{!! $toast_message !!}", "Warning!");
	                @else
	                    toastr.success("{!! $toast_message !!}");
	                @endif
	            });
	        @endif
		    

	        @if(!isset($dropdown_type))
	        	{{ '' }}
		    @elseif($dropdown_type == 'Facility')
		    	set_select_facility("site_filter", "{{ url('/facility/search') }}", 3, "Search for facility");
		    @else
			    $(".custom-select").select2();
		    @endif	
		    
		    $('#errorAlertDateRange').hide();
		    $("#breadcrum").html("{!! $default_option ?? '' !!}");   	    

			$("select").change(function(){
				em = $(this).val();
				id = $(this).attr('id');

				var posting = $.post( "{{ url('filter/any') }}", { 'session_var': id, 'value': em } );

				posting.done(function( data ) {
					console.log(data);
					reload_page(data);
				});

				posting.fail(function( data ) {
					location.reload(true);
				});
			});	
	    });
</script>


@yield('scripts')

</html>