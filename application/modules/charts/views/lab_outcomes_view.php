<div id="outcomes" class="col-md-6">

</div>

<script type="text/javascript">

  
    $("#outcomes").highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: "Outcomes",
            x: -20 //center
        },
        xAxis: {
            categories: <?php echo json_encode($trends['categories']);?>
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Tests'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        series: <?php echo json_encode($trends['outcomes']);?>
            
    });
  

 
</script>