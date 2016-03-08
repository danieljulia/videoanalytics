<?php
$video=$_GET['video'];
$url=get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=videoanalytics_api&method=sessions_videos&video='.$video;
?>


<h2>
Video: <?php print $video ?></h2> 
<p class="info">
<?php _e("Duration","va")?>: <?php print videoanalytics_get_duration($video)?> s.
<span class="more-info"></span></p>

<div id="visualization"></div>

<p><a href="<?php print $url."&download"?>"><?php _e("Download data")?></a>
</p>
<div class="log">
Log:
<ul class="log">
</ul>
</div>
<script type="text/javascript">
/**

tema de grups

*/
  var container = document.getElementById('visualization');
  
 
  var video="<?php print $_GET['video']?>";



 jQuery(document).ready(function(){
    jQuery.getJSON('<?php print $url?>',function(data){
     
        init(data);
    });
  });

  function init(data){
    var items=[];
    var last="";
    var group=0;
    var dt=0;
    var desfase=0;
    var l=data.length;
    var txt="";
    for(var i=0;i<l;i++){
      var d=data[i];
      if(d.rndk!=last){
        desfase=0;
        dt=d.ts;
        last=d.rndk;
        group++;
        txt+="<li class='legend'>Session "+group+"</li>";
      }
      
      items.push({y:d.ts-dt-desfase,x:parseFloat(d.params),group:group});

      txt+="<li>"+d.act+" "+(d.ts-dt)+" "+d.params;
      if(desfase>0) txt+=" pausa acumulada: "+desfase;
      txt+="</li>";

      
      if(d.act=="pausa"){
          if(i<l-1){
          if(data[i+1].rndk==d.rndk){
            if(data[i+1].act=="buscado" || data[i+1].act=="pausa"){
                  items.push({y:d.ts-dt,x:0,group:group});
                
            }
            if(data[i+1].act=="play"){ //una pausa molt gran que no volem que es visualitzi
                  //alert("desfase");
                  desfase+=data[i+1].ts-d.ts;
                //prova http://kiwoo.dev/videoanalytics/wordpress/wp-admin/admin.php?page=videoanalytics&option=sessions_videos&video=06507_m4_exercici_3
            }
          }
        }
        

      }
      
    }
    jQuery("ul.log").html(txt);
    jQuery(".more-info").html(group+" sessions");
    console.log(items);
    /*
    var items = [
        {x: '104343', y: 10,group:0},
        {x: '104345', y: 25,group:0},
        {x: '104348', y: 30,group:0},
         {x: '104543', y: 10,group:1},
        {x: '104545', y: 25,group:1},
        {x: '104548', y: 30,group:1},
         {x: '106343', y: 10,group:2},
        {x: '104645', y: 25,group:2},
        {x: '104648', y: 30,group:2},

   
        
    ];*/

    var has_legend=true;
    if(group>10) has_legend=false;
   
    var dataset = new vis.DataSet(items);
    var options = {
        legend: has_legend,
        sort: false,
        defaultGroup: 'session',
        interpolation:false,
        groups:{
          1:{color:{background:'red'}},
          2:{color:{background:'red'}},
          3:{color:{background:'red'}}

        }
        //graphHeight: '1500px',
       // height: '500px',
       // start: '2014-06-10',
       // end: '2014-06-18'
    };
    var graph2d = new vis.Graph2d(container, dataset, options);
}
</script>