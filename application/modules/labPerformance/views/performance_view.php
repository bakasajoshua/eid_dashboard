<div id="graphs">


	
</div>
<div class="row">

  <div id="stacked_graph">

  </div>

  <div id="lineargauge">
  </div>
</div>


<script type="text/javascript">

  $().ready(function() {
    $("#graphs").load("<?php echo base_url();?>charts/LabPerformance/testing_trends");
    $("#stacked_graph").load("<?php echo base_url();?>charts/LabPerformance/lab_outcomes");
    $("#lineargauge").load("<?php echo base_url();?>charts/LabPerformance/lab_turnaround");
  });
  

  function get_graphs(year){
    $.ajax({
           url: "<?php echo base_url('charts/LabPerformance/testing_trends'); ?>/" + year,
           
           error: function(data) {
              $("#test").append(data);
           },
           dataType : "json",
           success: function(data) {
                
                
                $("#graphs").empty().append(data);
           },
           type: 'GET'
        });
  }

	
   
</script>