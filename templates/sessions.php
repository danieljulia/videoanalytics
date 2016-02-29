
  <h2><?php echo __("Sessions","videoanalytics")?> </h2>
  <h3>rndk [data] / canvis</h3>

  <?php
  $sessions=va_get_sessions();

    print("<ul>");
    foreach ($sessions as $post)
    {
        print('<li><a href="?page=videoanalytics&option=session&rndk='.$post->rndk.'">'.$post->rndk.' ['.$post->ti.']  / '.$post->t.'</a>');
        //'|'.$post->video.'
         print('</li>');
    }
    print("</ul>");
    ?>
