

  <h3>Videos</h3>

<h3>video id / [sessions]</h3>
  <?php
  $sessions=va_get_videos();

    print("<ul>");
    foreach ($sessions as $post)
    {
        print('<li><a href="?page=videoanalytics&option=sessions_videos&video='.$post->video.'">'.$post->video.'</a>'.' ['.$post->t.']');
    
         print('</li>');
    }
    print("</ul>");
    ?>
