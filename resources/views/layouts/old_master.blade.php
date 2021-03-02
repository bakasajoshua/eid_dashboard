<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- about this site -->
		<meta name="description" content="A web platform that for Viral Load">
		<meta name="keywords" content="EID, VL, Early infant diagnosis, Viral Load, HIV, AIDS, HIV/AIDS, adults, pedeatrics, infants">
		<meta name="author" content="Star Sarifi Tours">
		<meta name="Resource-type" content="Document">
		<link rel='stylesheet' href='https://eid.nascop.org/assets/css/custom.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/jquery-ui/jquery-ui.min.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/bootstrap/css/bootstrap.min.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/bootstrap/css/bootstrap-theme.min.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/bootstrap/css/bootstrap-responsive.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/bootstrap/css/bootstrap-material-design.min.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/select2/css/select2.min.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/tablecloth/css/tablecloth.css' type='text/css'></link>
		<link rel='stylesheet' href='https://eid.nascop.org/assets/plugins/tablecloth/css/prettify.css' type='text/css'></link>
		<link rel='stylesheet' href='//cdn.datatables.net/1.10.12/css/jquery.dataTables.css' type='text/css'></link>
		<link rel='stylesheet' href='//cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css' type='text/css'></link>
		<link rel='stylesheet' href='//cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.css' type='text/css'></link>
		<script src='https://eid.nascop.org/assets/plugins/jquery/jquery-2.2.3.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/jquery-ui/jquery-ui.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/bootstrap/js/bootstrap.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/bootstrap/js/material.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/select2/js/select2.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/tablecloth/js/jquery.metadata.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/tablecloth/js/jquery.tablesorter.min.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/plugins/tablecloth/js/jquery.tablecloth.js' type='text/javascript'></script>
		<script src='https://eid.nascop.org/assets/js/customFunctions.js' type='text/javascript'></script>
		<script src='https://code.highcharts.com/highcharts.js' type='text/javascript'></script>
		<script src='https://code.highcharts.com/highcharts-more.js' type='text/javascript'></script>
		<script src='https://code.highcharts.com/modules/exporting.js' type='text/javascript'></script>
		<script src='https://code.highcharts.com/modules/export-data.js' type='text/javascript'></script>
		<script src='https://code.highcharts.com/maps/modules/map.js' type='text/javascript'></script>
		<script src='//cdn.datatables.net/1.10.12/js/jquery.dataTables.js' type='text/javascript'></script>
		<script src='//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js' type='text/javascript'></script>
		<script src='//cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.js' type='text/javascript'></script>
		<script src='//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js' type='text/javascript'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js' type='text/javascript'></script>
		<link rel=icon href="https://eid.nascop.org/assets/img/kenya-coat-of-arms.png" type="image/png">
		<title>
			Dashboard
		</title>
		<!-- <script src='https://www.google.com/recaptcha/api.js'></script> -->
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
		<div class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="javascript:void(0)" style="padding:0px;padding-top:4px;padding-left:4px;"><img src="<?php echo base_url();?>assets/img/nascop_pepfar_logo.jpg" style="width:280px;height:52px;"/></a>
				</div>
				<div class="navbar-collapse collapse navbar-responsive-collapse">
					<ul class="nav navbar-nav">
						
					</ul>
					<!-- <form class="navbar-form navbar-left" id="1267192336">
						<div class="form-group">
							<input type="text" class="form-control col-md-8" placeholder="Search">
						</div>
					</form> -->
					<ul class="nav navbar-nav navbar-right">
						<!-- <li><a href="<?php echo base_url();?>">Summary</a></li> -->
						<li class="dropdown">
							<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Summaries
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url();?>">Summary</a></li>
								<li><a href="<?php echo base_url();?>summary/heivalidation">HEI Validation Summary</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">County/Sub-County
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url();?>county">County</a></li>
								<li><a href="<?php echo base_url();?>county/tat">County TAT</a></li>
								<li><a href="<?php echo base_url();?>county/subCounty">Sub-County</a></li>
								<li><a href="<?php echo base_url();?>county/subCountytat">Sub-County TAT</a></li>
							</ul>
						</li>
						<li><a href="<?php echo base_url();?>sites">Facilities</a></li>
						<li class="dropdown">
							<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Labs
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url();?>labPerformance">Lab Performance</a></li>
								<li><a href="<?php echo base_url();?>labPerformance/poc">POC</a></li>
							</ul>
						</li>
						<!-- <li><a href="<?php echo base_url();?>age">Age</a></li> -->
						<li class="dropdown">
							<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Partners
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url();?>partner">Summary</a></li>
								<li><a href="<?php echo base_url();?>partner/trends">Trends</a></li>
								<li><a href="<?php echo base_url();?>partner/sites">Partner Facilities</a></li>
								<li><a href="<?php echo base_url();?>partner/counties">Partner Counties</a></li>
								<li><a href="<?php echo base_url();?>partner/heivalidation">HEI Validation</a></li>
								<li><a href="<?php echo base_url();?>partner/tat">Partner TAT</a></li>
								<li><a href="<?php echo base_url();?>partner/agencies">Funding Agencies</a></li>
							</ul>
						</li>
						
						<!-- <li><a href="<?php echo base_url();?>rht">RHT Testing</a></li> -->
						<li class="dropdown">
							<a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Positivity
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo base_url();?>positivity">Positivity</a></li>
								<li><a href="<?php echo base_url();?>age">Age Analysis</a></li>
								<li><a href="<?php echo base_url();?>regimen">Regimen Analysis</a></li>
							</ul>
						</li>
						<li><a href="<?php echo base_url();?>trends">Trends</a></li>
						<li><a href="https://nascop.org">Resources</a></li>
						<li><a href="https://viralload.nascop.org">VL View</a></li>
						<li><a href="<?php echo base_url();?>contacts">Contact Us</a></li>
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
			        <form action="<?php echo base_url();?>template/filter_county_data" method="post" id="filter_form">
			            <select class="btn btn-primary js-example-basic-single" style="background-color: #C5EFF7;" name="county">
			                <option disabled="true" selected="true">Select a Sub County:</option>
			                <option value="0">National</option>
			                <?php echo $subCounty; ?>

			            </select>
			        </form>
			    </div>
			    <div class="col-md-2">
			        <div id="breadcrum" class="alert" style="background-color: #1BA39C;/*display:none;"></div>
			    </div>
			    <div class="col-md-4" id="year-month-filter">
			        <div class="filter">
			            Year: 
			              <?php
			                for ($i=9; $i > -1; $i--) { 
			                  $year = gmdate('Y');
			                  $year -= $i;
			              ?>
			              <a href="javascript:void(0)" onclick="date_filter('yearly', <?= @$year; ?> )" class="alert-link"> <?= @$year; ?> </a>|
			              <?php } ?>
			        </div>
			        <div class="filter">
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
			    </div>
			    <div class="col-md-2">
			        <div class="row" id="range">
			            <div class="col-md-4">
			                <input name="startDate" id="startDate" class="date-picker" placeholder="From:" />
			            </div>
			            <div class="col-md-4 endDate">
			                <input name="endDate" id="endDate" class="date-picker" placeholder="To:" />
			            </div>
			            <div class="col-md-4">
			                <button id="filter" class="btn btn-primary date-pickerBtn" style="color: white;background-color: #1BA39C; margin-top: 0.2em; margin-bottom: 0em; margin-left: 4em;"><center>Filter</center></button>
			            </div>
			        </div>
		            <center>
		            	<div id="errorAlertDateRange"><div id="errorAlert" class="alert alert-danger" role="alert">...</div></div>
		            </center>
			    </div>
			</div>
		

			@yield()






		</div>
		<!-- End of Dashboard area -->
	</body>
</html>