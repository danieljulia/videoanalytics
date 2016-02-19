<?php
/*
Plugin Name: Custom Video analytics UOC
Plugin URI: http://www.uoc.edu
Description: Analytics for custom video stats
Version: 0.1
Author: Daniel Julià
Author URI: http://www.pimpampum.net
License: License: GPLv2
*/



require "videoanalytics_api.php";

add_action( 'admin_init', 'videoanalytics_options_init' );
add_action( 'admin_menu', 'videoanalytics_options_add_page' ); 



/* registrar les opcions del plugin */
function videoanalytics_options_init(){
 register_setting( 'videoanalytics_options', 'videoanalytics_options');
} 

/* afegir pàgina a l'escriptori*/

function videoanalytics_options_add_page() {
  add_options_page( "Video analytics", "Video analytics setup", "activate_plugins", "videoanalytics_options", "videoanalytics_options_do_page");

 /*add_theme_page(
  __( 'Opcions de Newsletter', 'videoanalytics_' ),
  __( 'Opcions de Newsletter', 'videoanalytics_' ),
   'edit_theme_options', 'videoanalytics_options', 'ppp_newsletter_options_do_page' );*/
} 

function videoanalytics_options_do_page() {

  global $options;

  if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false; 
  ?>
<div>
<?php screen_icon(); echo "<h2>". __( 'Video analytics configuration', 'mt' ) . "</h2>"; ?>
<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
<div>
<p><strong><?php _e( 'Options saved', 'mt' ); ?></strong></p></div>
<?php endif; ?> 
<form method="post" action="options.php">
<?php settings_fields( 'videoanalytics_options' ); ?>  

<?php $options = get_option( 'videoanalytics_options' ); 

?>

<table>


<?php
 

if(!isset($options['db_name'])){
  $options['db_name']="";
}

?>



<p>
<!--Name of database -->

</p>
<tr valign="top"><th scope="row">
<?php print __("Database name","mt")?></th>
<td>
<input id="videoanalytics_options[db_name]" type="text" name="videoanalytics_options[db_name]" value="<?php esc_attr_e( $options['db_name'] ); ?>" />
</td>
</tr> 


</table> 
<p>
<input type="submit" value="<?php print __("Save the options","mt");?>" />
</p>
</form>

</div>
<?php 
} 


/** add option to wp menu*/


add_action('admin_menu','my_plugin_menu');

function my_plugin_menu(){
  add_menu_page('Video analytics setup','Video analytics','edit_posts','videoanalytics','my_plugin_options','dashicons-welcome-view-site');
}

function my_plugin_options(){



?>
  <div class="wrap">
   <h1>Video analytics</h1>
  <?php

  if(isset($_GET['rndk'])){
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
    
    

  }else{

  ?>

 
  <h2><?php echo __("Sessions","videoanalytics")?> </h2>
  <h3>rndk [data] / canvis</h3>

  <?php
  $sessions=va_get_sessions();

    print("<ul>");
    foreach ($sessions as $post)
    {
        print('<li><a href="?page=videoanalytics&rndk='.$post->rndk.'">'.$post->rndk.' ['.$post->ti.']  / '.$post->t.'</a>');
        //'|'.$post->video.'
         print('</li>');
    }
    print("</ul>");
    ?>



  <?php
}
?>
  </div>
  <?php
}


/** crides ajax 

totes es fan a la url
http://192.168.1.200/projects/2016_a/videoanalytics/wordpress/wp-admin/admin-ajax.php?action=videoanalytics_do_ajax_request


 admin-ajax.php?action=videoanalytics_do_ajax_request&rndk=0.6015316601842642
*/

add_action( 'wp_ajax_videoanalytics_do_ajax_request', 'videoanalytics_do_ajax_request' );
add_action( 'wp_ajax_nopriv_videoanalytics_do_ajax_request', 'videoanalytics_do_ajax_request' );

function videoanalytics_do_ajax_request(){
 $track="";
  if(isset($_GET['track'])){
    $track=$_GET['track'];

  }

  $data=va_session_get($_GET['rndk'],$track);

?>
  {"cols":[

{"id":"","label":"Header","pattern":"","type":"number"},
{"id":"","label":"Time","pattern":"","type":"date"}

],"rows":[

<?php
$c=0;
$total=count($data);

foreach($data as $d):

  $ts=(new DateTime($d->ta))->getTimestamp();

$date=date_create();
date_timestamp_set($date, $ts);
 //echo date_format($date, 'U = Y-m-d H:i:s') . "\n";

?>
{"c":[{"v":<?php print $d->params?>},{"v":"Date( <?php print date_format($date,"Y,m,d,H,i,s") ?>)"}]},



<?php
  //todo si es el final afegir
  if($d->act=="pausa"){

    //si el seguent torna a ser un play del mateix video...
    if($c<$total-1){
      if($data[$c+1]->act=="play" && $data[$c+1]->video==$data[$c]->video){
            
            
            $ts2=$data[$c+1]->params;
         
            $date2=date_create();
            date_timestamp_set($date2, $ts);
             
            ?>
            {"i":"blah","c":[{"v":<?php print $d->params?>},{"v":"Date( <?php print date_format($date2,"Y,m,d,H,i,s") ?>)"}]},


            <?php


      }else{

            //print "Ole".intVal($d->params);
            //$date2=date_add($date, date_interval_create_from_date_string('+'.intVal($d->params).' seconds'));
            $ts2=$ts+intVal($d->params);
            //print "ts2: ".$ts2." ts: ".$ts." dif: ".($ts2-$ts);
            $date2=date_create();
            date_timestamp_set($date2, $ts);
              // $date2=$date->add(new DateInterval('PT'.$d->params.'S'));
            //todo no suma be els segons
            ?>
            {"i":"blah","color":"#ff0000","c":[{"v":0},{"v":"Date( <?php print date_format($date2,"Y,m,d,H,i,s") ?>)"}]},


            <?php


      }
    }
   

  }

$c++;
endforeach;
?>

]}
<?php
 
  exit();
}

/*
add_action('init', function() {

  

$url = $_SERVER['REQUEST_URI'];
$tokens = explode('/', $url);
$url_path=$tokens[sizeof($tokens)-1];
$url2=explode('?', $url_path);
if(count($url2)>1){
  $url_path=$url2[0];
}



  if ( $url_path === 'mailchimp-template' ) {
    
    //$file_name='/mailchimp-templates/mailchimp-main-template.php';
    $file_name='/send-newsletter.php';
    //load_template( 'send-newsletter.php' );
    //load_template( 'mailchimp-templates/mailchimp-main-template.php' );

    
     // load the file if exists
     if ( $overridden_template = locate_template( $file_name ) ) {
   // locate_template() returns path to file
   // if either the child theme or the parent theme have overridden the template
   load_template( $overridden_template );
    exit();
 } else {
   // If neither the child nor parent theme have overridden the template,
   // we load the template from the 'templates' sub-directory of the directory this file is in
   load_template( dirname( __FILE__ ) . $file_name );
   exit();
 }

     
  }
});
*/


/**
crear taula al activar el plugin
*/

function videoanalytics_video_install(){
  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();
     $table_name = $wpdb->prefix . "videoanalytics_video"; 


  $sql="CREATE TABLE IF NOT EXISTS $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    video varchar(256) NOT NULL,
    duration float NOT NULL,
    updated timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (id)
  ) $charset_collate ;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  //marcar versió
  add_option( "videoanalytics_video", "1.0" );
}


register_activation_hook( __FILE__, 'videoanalytics_video_install' );
