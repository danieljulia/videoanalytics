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


function videoanalytics__adding_scripts() {
  wp_register_script('vis', plugin_dir_url( __FILE__ ) . '/vis/vis.min.js');
  wp_enqueue_script('vis');

  wp_enqueue_style(
    'viscss',
    plugin_dir_url( __FILE__ )  . '/vis/vis.css'
);
   wp_enqueue_style(
    'videoanalytics',
    plugin_dir_url( __FILE__ )  . '/css/videoanalytics.css'
);

}

 
add_action( 'admin_enqueue_scripts', 'videoanalytics__adding_scripts' );



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
  <nav>
  <ul class="videoanalytics-menu">
   <li><a href="admin.php?page=videoanalytics&option=main">Main</a></li> 
   <li><a href="admin.php?page=videoanalytics&option=sessions">Sessions</a></li> 
   <li><a href="admin.php?page=videoanalytics&option=videos">Videos</a></li> 
  </ul>
  </nav>
</div>
<div class="wrap">
 <h2>Video analytics</h2>
  <?php

  $option="main";
  if(isset($_GET['option'])){
    $option=$_GET['option'];
  }



  switch($option){
      case "main":
        include "templates/main.php";
        break;
      case "sessions":
        include "templates/sessions.php";
        break;
       case "session":
        include "templates/session.php";
        break;
      case "videos":
        include "templates/videos.php";
        break;
      case "sessions_videos":
        include "templates/sessions_videos.php";
        break;
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
add_action( 'wp_ajax_videoanalytics_api', 'videoanalytics_api' );
add_action( 'wp_ajax_nopriv_videoanalytics_api', 'videoanalytics_api' );

/**
per testejar
http://kiwoo.dev/videoanalytics/wordpress/wp-admin/admin-ajax.php?action=videoanalytics_api&method=sessions_video&video=06507_m2_exercici_11
*/
function videoanalytics_api(){
  if(isset($_GET['method'])){
    $method=$_GET['method'];
  }
  if(isset($_GET['download'])){ //forçar download
    //todo mostrar info dades al json
     header('Content-Disposition: attachment; filename="videoanalytics_data.json"');
     
  }

  switch($method){
      case "sessions_videos":
        api("sessions_videos",array("video"=>$_GET['video']));
        break;

  }

  
  exit(); 


}


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
