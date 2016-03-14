<?php
$data=va_session_get($_GET['rndk']);
$rndk=$_GET['rndk'];
$url=get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=videoanalytics_api&method=session&rndk='.$rndk;

?>



<h2>
Session: <?php print $rndk ?></h2> 
<p class="info">

<span class="more-info"></span></p>


<div class="vis-container">
      <div class="vis-placeholder"></div>
    </div>
  
<a id="dl" download="Canvas.png" href="#">Download Image</a>

<p><a href="<?php print $url."&download"?>"><?php _e("Download data")?></a>
</p>
<div class="log">
Log:
<ul class="log">
</ul>
</div>
<script type="text/javascript">
var group_separator="video";
var image_id="<?php print $rndk?>";
jQuery(document).ready(function(){
  ajax_load('<?php print $url?>');
});

</script>