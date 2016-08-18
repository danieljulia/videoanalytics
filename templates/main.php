<div class="wrap">


<a href="<?php bloginfo("wpurl")?>/wp-admin/options-general.php?page=videoanalytics_options">Configuration</a>


<h1>Stats for current table</h1>
<?php

$sessions=va_get_sessions();
$videos=va_get_videos();

 ?>
<ul>
<li>- total sessions : <?php print count($sessions);?>
</li>

<li>- total videos : <?php print count($videos);?>
</li>
</ul>


</div>
