<?php

$video=$_GET['video'];
$url=get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=videoanalytics_api&method=sessions_videos&video='.$video;

?>



<h2>
Video: <?php print $video ?></h2>
<p class="info">
<?php _e("Duration","va")?>: <?php
$duration=videoanalytics_get_duration($video);
 print $duration ?> s.
<span class="more-info"></span></p>


<div class="vis-container">
      <div class="vis-placeholder"></div>
    </div>

<a id="dl" download="Canvas.png" href="#">Download Image</a>

<p><a href="<?php print $url."&download"?>"><?php _e("Download data")?></a>
</p>
<div class="log">
Log:
<div class="log-list">
</div>
</div>
<script type="text/javascript">
var duration=<?php print $duration?>;
var group_separator="rndk";
var image_id="<?php print $video?>";
jQuery(document).ready(function(){
  ajax_load('<?php print $url?>');
});

</script>
