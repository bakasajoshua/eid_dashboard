<div id="graphs">


	
</div>


<div id="stacked_graph">

</div>

  


<script type="text/javascript">

  $().ready(function() {
    $("#graphs").load("<?php echo base_url();?>charts/positivity/positive_trends");
    $("#stacked_graph").load("<?php echo base_url();?>charts/positivity/summary");
  });
  

  function get_graphs(year){
    $.ajax({
           url: "<?php echo base_url('charts/positivity/positive_trends'); ?>/" + year,
           
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