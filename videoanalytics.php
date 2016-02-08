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


add_action( 'admin_init', 'pimpampum_newsletter_options_init' );
add_action( 'admin_menu', 'pimpampum_newsletter_options_add_page' ); 


function pimpampum_newsletter_options_init(){
 register_setting( 'pimpampum_newsletter_options', 'pimpampum_newsletter_options');
} 

function pimpampum_newsletter_options_add_page() {
add_options_page( "Pimpampum newsletter", "Video analytics setup", "activate_plugins", "ppp_newsletter_options", "ppp_newsletter_options_do_page");

 /*add_theme_page(
  __( 'Opcions de Newsletter', 'pimpampum_newsletter' ),
  __( 'Opcions de Newsletter', 'pimpampum_newsletter' ),
   'edit_theme_options', 'pimpampum_newsletter_options', 'ppp_newsletter_options_do_page' );*/
} 

function ppp_newsletter_options_do_page() {

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
<?php settings_fields( 'pimpampum_newsletter_options' ); ?>  

<?php $options = get_option( 'pimpampum_newsletter_options' ); 

?>

<table>


<?php
 

if(!isset($options['api_key'])){
  $options['api_key']="";
}
if(!isset($options['test_list_id'])){
  $options['test_list_id']="";
}
if(!isset($options['ok_list_id'])){
  $options['ok_list_id']="";
}
if(!isset($options['from_email'])){
  $options['from_email']="";
}
if(!isset($options['from_name'])){
  $options['from_name']="";
}

?>



<p>
<!--Name of database -->

</p>
<tr valign="top"><th scope="row">
<?php print __("Database name","mt")?></th>
<td>
<input id="pimpampum_newsletter_options[api_key]" type="text" name="pimpampum_newsletter_options[api_key]" value="<?php esc_attr_e( $options['api_key'] ); ?>" />
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
  add_menu_page('Video analytics setup','Video analytics','edit_posts','newsletter','my_plugin_options','dashicons-email');
}

function my_plugin_options(){

  ?>
  <div class="wrap">
  <h1>Video analytics</h1>
  <p>List available stats</p>
  <a target="newsletter" href="<?php print bloginfo("wpurl")?>/mailchimp-template"><?php print __("View","mt")?></a>
  </div>
  <?php

}

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


