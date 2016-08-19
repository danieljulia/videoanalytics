<?php
//if(isset($_GET['rndk'])){
    $data=va_session_get($_GET['rndk']);
  ?>

<p><?php print __("Dades per la sessió","videoanalytics")?> <?php print  $_GET['rndk']?></p>
<div id="chart_div"></div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
 google.charts.load('current', {'packages':['line', 'corechart']});
      google.charts.setOnLoadCallback(drawChart);
    var track="";//06507_m2_exercici_32";

    function drawChart() {
      
      if(track==null) track="";
      var jsonData = jQuery.ajax({
          url: "admin-ajax.php?action=videoanalytics_do_ajax_request&rndk=<?php print $_GET['rndk']?>&track="+track,
          dataType: "json",
          async: false
          }).responseText;


      var button = document.getElementById('change-chart');
      var chartDiv = document.getElementById('chart_div');

     

      var data = new google.visualization.DataTable(jsonData);
     
      //data.addColumn('number', "Header");
       //data.addColumn('date', 'Time');
     // data.addColumn('number', "Average Hours of Daylight");

      //data.addRows();

      var materialOptions = {
        chart: {
          title: 'Evolució'
        },
        width: 900,
        height: 500,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'Temps'},
          1: {axis: 'Daylight'}
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            Temps: {label: 'Time'},
            Daylight: {label: 'Daylight'}
          }
        }
      };

      var classicOptions = {
        title: 'Average Temperatures and Daylight in Iceland Throughout the Year',
        width: 900,
        height: 500,
        // Gives each series an axis that matches the vAxes number below.
        series: {
          0: {targetAxisIndex: 0}
         // 1: {targetAxisIndex: 1}
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'Temps (Celsius)'},
          1: {title: 'Daylight'}
        },
       /*
        hAxis: {
          ticks: [new Date(2014, 0), new Date(2014, 1), new Date(2014, 2), new Date(2014, 3),
                  new Date(2014, 4),  new Date(2014, 5), new Date(2014, 6), new Date(2014, 7),
                  new Date(2014, 8), new Date(2014, 9), new Date(2014, 10), new Date(2014, 11)
                 ]
        },*/
        vAxis: {
          viewWindow: {
            max: 30
          }
        }
      };

      

      function drawMaterialChart() {
        var materialChart = new google.charts.Line(chartDiv);
        materialChart.draw(data, materialOptions);
        //button.innerText = 'Change to Classic';
      //  button.onclick = drawClassicChart;
      }

      function drawClassicChart() {
        var classicChart = new google.visualization.LineChart(chartDiv);
        classicChart.draw(data, classicOptions);
       // button.innerText = 'Change to Material';
       // button.onclick = drawMaterialChart;
      }

     
      drawMaterialChart();

    }
     function drawChunk(t){
        track=t;
        drawChart();
        return false;
      }

    </script>

  <?php
  $videos=[];
  $last_video="";
 
foreach ($data as $post)
    {
        if( !in_array($post->video,$videos) ){
          $videos[]=$post->video;
        }
 
    }?>

    <nav class="tracks"><a href='#' onclick='drawChunk("")'>All</a> 
    <?php
foreach($videos as $video){
      print "<a  onclick='drawChunk(\"".$video."\")'>".$video."</a> ";
    }
    ?>
    </nav>
    <?php
    print("<ul>");
    foreach ($data as $post)
    {
        
        print('<li>'.$post->video.'|'.$post->ta.'|'.$post->act.'|'.$post->params.'</a>');
         print('</li>');
    }
    print("</ul>");
    ?>





