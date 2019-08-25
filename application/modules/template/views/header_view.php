<?php ?>
<!DOCTYPE html>
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
		<?php      	
			$this->load->view('utils/dynamicLoads');
		?>
		<link rel=icon href="<?php echo base_url('assets/img/kenya-coat-of-arms.png');?>" type="image/png">
		<title>
			Dashboard
		</title>
		<style type="text/css">
			@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700");
			@import url("https://fonts.googleapis.com/css?family=Roboto:400,300,500,700");
			
			h1,
			h2,
			h3,
			h4,
			h5,
			h6 {
			  font-weight: 100;
			}
			h1 {
			  font-size: 30px;
			}
			h2 {
			  font-size: 24px;
			}
			h3 {
			  font-size: 16px;
			}
			h4 {
			  font-size: 14px;
			}
			h5 {
			  font-size: 12px;
			}
			h6 {
			  font-size: 10px;
			}
			h3,
			h4,
			h5 {
			  margin-top: 5px;
			  font-weight: 600;
			}
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
			.nav {
				color: black;
			}
		</style>
		<!-- <script src='https://www.google.com/recaptcha/api.js'></script> -->
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
	<?php //echo "<pre>";print_r($_SERVER['REQUEST_URI']);die();?>
		<!-- Begining of Navigation Bar -->
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
						<!-- <li><a href="<?php echo base_url();?>county">County View</a></li>
						<li><a href="http://eid.nascop.org/vreports.php">Reports</a></li>
						<li><a href="http://eid.nascop.org/login.php">Login</a></li>
						<li><a href="http://eid.nascop.org">EID View</a></li> -->
						<!-- <li><a href="javascript:void(0)">Link</a></li> -->
						<!-- <li class="dropdown"> -->
							<!-- <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown
							<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="javascript:void(0)">Action</a></li>
								<li><a href="javascript:void(0)">Another action</a></li>
								<li><a href="javascript:void(0)">Something else here</a></li>
								<li class="divider"></li>
								<li><a href="javascript:void(0)">Separated link</a></li>
							</ul> -->
						<!-- </li> -->
					</ul>
				</div>
			</div>
		</div>
		<!-- End of Navigation Bar -->
		<!-- Begining of Dashboard area -->
		<div class="container-fluid">
		