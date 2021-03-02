<div id="{{ $div }}" style="height: 700px">

</div>


<script type="text/javascript">
	$(function () {
        // Initiate the chart
        $('#{{ $div }}').highcharts('Map', {
            title: {
                text: "{{ $title ?? '' }}"
            },
            legend: {
                enabled: true
            },
            
            series: [
                {
                    "name" : "data",
                    "type": "map",
                    "mapData": kenya_map,
                    "data" : {!! json_encode($outcomes) !!},
                    "joinBy": ['id', 'id'],
                    "dataLabels": {
                            enabled: true,
                            color: '#FFFFFF',
                            format: '{point.name}'
                        },
                    "point":{
                        "events":{
                            click: function(){
                                // set_table(this.id, this.name);
                            }
                        }
                    },
                    "tooltip": {
                        "valueSuffix": ""
                    }
                }
            ],

            /*colorAxis: {
                minColor : "#f2bae",
                maxColor : "#ff2015"
            },*/
            colorAxis: {
                min: 0,
                minColor: '#E6E7E8',
                maxColor: '#005645'
            },

            mapNavigation: {
                enabled: false,
                enableButtons: true
            },
       
        
        });

    });
</script>