<script type="text/javascript">
	$().ready(function(){
		var site = <?php echo json_encode($this->session->userdata("site_filter")); ?>;
		//$("#siteOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>");
		
		if (!site) {
    		$("#siteOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>");
			$("#first").show();
			$("#second").hide();
		} else {
			$("#first").hide();
			$("#second").show();

			$("#tsttrends").html("<center><div class='loader'></div></center>");
			$("#stoutcomes").html("<center><div class='loader'></div></center>");
			$("#vlOutcomes").html("<center><div class='loader'></div></center>");
			$("#ageGroups").html("<center><div class='loader'></div></center>");

			$("#tsttrends").load("<?php echo base_url('charts/sites/site_trends');?>/"+site);
			$("#stoutcomes").load("<?php echo base_url('charts/sites/site_positivity');?>/"+site);
			$("#vlOutcomes").load("<?php echo base_url('charts/sites/site_eid');?>/"+site);
			$("#ageGroups").load("<?php echo base_url('charts/sites/site_hei');?>/"+site);
			
		}

		
		$("select").change(function() {
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_site_data", { site: em } );

	        // Put the results in a div
	        posting.done(function( data ) {
	        	$.get("<?php echo base_url();?>template/dates", function(data){
	        		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
	        	});

	        	$.get("<?php echo base_url();?>template/breadcrum", function(data){
	        		$("#breadcrum").html(data);
	        	});

	        	if (em=="NA") {
	        		$("#siteOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>");
					$("#first").show();
					$("#second").hide();
				} else {
					$("#first").hide();
					$("#second").show();

					$("#tsttrends").html("<center><div class='loader'></div></center>");
					$("#stoutcomes").html("<center><div class='loader'></div></center>");
					$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#ageGroups").html("<center><div class='loader'></div></center>");

					$("#tsttrends").load("<?php echo base_url('charts/sites/site_trends');?>/"+data);
					$("#stoutcomes").load("<?php echo base_url('charts/sites/site_positivity');?>/"+data);
					$("#vlOutcomes").load("<?php echo base_url('charts/sites/site_eid');?>/"+data);
					$("#ageGroups").load("<?php echo base_url('charts/sites/site_hei');?>/"+data);
					
				}
	        });
		});
	});

	function date_filter(criteria, id)
 	{
 		$("#partner").html("<div>Loading...</div>");

 		if (criteria === "monthly") {
 			year = null;
 			month = id;
 		}else {
 			year = id;
 			month = null;
 		}

 		var posting = $.post( '<?php echo base_url();?>summary/set_filter_date', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {
			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
			
		});
		var site = <?php echo json_encode($this->session->userdata('site_filter')); ?>;
		//Checking if site was previously selected and calling the relevant views
		if (!site) {
			$("#siteOutcomes").html("<center><div class='loader'></div></center>");
			$("#siteOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>/"+year+"/"+month+"/"+null);
		} else {
			$("#tsttrends").html("<center><div class='loader'></div></center>");
			$("#stoutcomes").html("<center><div class='loader'></div></center>");
			$("#vlOutcomes").html("<center><div class='loader'></div></center>");
			$("#ageGroups").html("<center><div class='loader'></div></center>");

			$("#tsttrends").load("<?php echo base_url('charts/sites/site_trends');?>/"+null+"/"+year);
			$("#stoutcomes").load("<?php echo base_url('charts/sites/site_positivity');?>/"+null+"/"+year);
			$("#vlOutcomes").load("<?php echo base_url('charts/sites/site_eid');?>/"+null+"/"+year);
			$("#ageGroups").load("<?php echo base_url('charts/sites/site_hei');?>/"+null+"/"+year);
			
		}
		///console.log(county);

	 	
 	}

	function ageModal()
	{
		$('#agemodal').modal('show');
		// $('#CatAge').load('<?php echo base_url();?>charts/summaries/agebreakdown');
	}
</script>