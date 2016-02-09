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
<p>Dades per la sessió <?php print  $_GET['rndk']?></p>

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

 
  <p>List available stats</p>

  <?php
  $sessions=va_get_sessions();

    print("<ul>");
    foreach ($sessions as $post)
    {
        print('<li><a href="?page=videoanalytics&rndk='.$post->rndk.'">'.$post->rndk.'|'.$post->video.'</a>');
         print('</li>');
    }
    print("</ul>");
    ?>

  <a target="newsletter" href="<?php print bloginfo("wpurl")?>/mailchimp-template"><?php print __("View","mt")?></a>

  <?php
}
?>
  </div>
  <?php
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

