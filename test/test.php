<?php

require "../videoanalytics_api.php";

$videos=va_get_videos();

print_r($videos);