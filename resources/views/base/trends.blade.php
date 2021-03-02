@extends('layouts.master')

@section('content')
<div class="row">
  <div class="col-md-4 col-md-offset-4">
    <div id="breadcrum" class="alert" style="background-color: #1BA39C;text-align: center;vertical-align: middle;" onclick="switch_source()">
          <span id="current_source">Click to toggle between quarterly and yearly</span>   
      </div>
         
  </div>
</div>

<div class="row">
  <div style="color:red;"><center>Click on Year(s)/Quarter(s) on legend to view only for the year(s)/quarter(s) selected</center></div>
  <div id="first">
    <div id="stacked_graph" class="col-md-12"></div>
    <div id="repeat_q" class="col-md-12"></div>
    <div id="alltests_q" class="col-md-12"></div>
    <div id="infants_q" class="col-md-12"></div>
    <div id="less2m_q" class="col-md-12"> </div>
    <div id="graphs"></div>

  </div>

  <div id="second">    
    <div id="q_outcomes" class="col-md-12"></div>
    <div id="q_graphs"></div>
  </div>
</div>

  
@endsection

@section('scripts')

<script type="text/javascript">
    function reload_page(countyFilter = null)
  {
      var a = localStorage.getItem("my_var");
      if(a == 0){
        $("#first").show();
        $("#second").hide();

        $("#graphs").load("{{ url('trends/positive_trends') }}");
        $("#stacked_graph").load("{{ url('trends/summary') }}");
        $("#alltests_q").load("{{ url('trends/alltests_q') }}");
        $("#repeat_q").load("{{ url('trends/repeat_q') }}");
        $("#infants_q").load("{{ url('trends/infants_q') }}");
        $("#less2m_q").load("{{ url('trends/less2m_q') }}");
      }
      else{
        $("#first").hide();
        $("#second").show();
        $("#q_outcomes").load("{{ url('trends/quarterly_outcomes') }}");
        $("#q_graphs").load("{{ url('trends/quarterly') }}");
      }
  }

  function switch_source(){
    var a = localStorage.getItem("my_var");

    if(a == 0){
      localStorage.setItem("my_var", 1);
    }else{
      localStorage.setItem("my_var", 0);
    }
        reload_page();
  }


  $().ready(function(){
        localStorage.setItem("my_var", 0);
        reload_page();
        // date_filter('yearly', "{{ date('Y') }}");
  });

</script>

@endsection
