<?php
$dates=va_get_dates();
$from="";
$to="";
if(isset($_POST['from'])){
  $from=$_POST['from'];
}
if(isset($_POST['to'])){
  $to=$_POST['to'];
}
?>


  <h3>Date window</h3>

  <form action="?page=videoanalytics&option=dates">
  From:<input type="text" name="from" placeholder="2016-10-10">
  To:<input type="text" name="to" placeholder="2016-10-10">
  <input type="submit" value="Filter">
  </form>


  <?php
  $sessions=va_get_videos($from,$to);

    print("<ul>");
    foreach ($sessions as $post)
    {
        print('<li><a href="?page=videoanalytics&option=sessions_videos&video='.$post->video.'">'.$post->video.'</a>'.' ['.$post->t.']');
    
         print('</li>');
    }
    print("</ul>");
    ?>
