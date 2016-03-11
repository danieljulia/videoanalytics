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

  var compta_desfase=true;


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
    
    var pausa=[];
    var ff=[];
    var rw=[];

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

      
     
          if(i<l-1){ //si te següent
            if(data[i+1].rndk==d.rndk){ //si el següent és de la mateixa sessió

 
                var next_status=data[i+1].act;

                if(d.act=="pausa"){
                  if(next_status="buscado" || next_status=="pausa"){
                        items.push({y:d.ts-dt,x:0,group:group});
                        if(compta_desfase){
                          desfase+=data[i+1].ts-d.ts;
                        }
                      
                  }
                  if(next_status=="play" ){ //una pausa molt gran que no volem que es visualitzi
                        //alert("desfase");
                        if(compta_desfase){
                        desfase+=data[i+1].ts-d.ts;
                      }
                      //prova http://kiwoo.dev/videoanalytics/wordpress/wp-admin/admin.php?page=videoanalytics&option=sessions_videos&video=06507_m4_exercici_3
                  }
                }

                //detectar rewindows i forward
                if(d.act=="buscado"){
                  if(next_status="buscado"){
                         

                          if( data[i+1].params  >d.params){
                            ff.push(d.params);
                          }
                          if( data[i+1].params<d.params){
                            rw.push(d.params);
                          }

                          
                        
                      
                  }
                  
                }


            }
        
          }
           

        

      
      
    }

    console.log("rewinds i fastforwards",rw,ff);

    jQuery("ul.log").html(txt);
    jQuery(".more-info").html(group+" sessions");
    console.log(items);

    var has_legend=true;
    if(group>10) has_legend=false;
   
    var dataset = new vis.DataSet(items);
    var options = {
        legend: has_legend,
        sort: false,
        defaultGroup: 'session',
        interpolation:false,
        zoomable:false,
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



//todo canviar de localitzacio

function exportImage(){
  //find all svg elements in $container
  //$container is the jQuery object of the div that you need to convert to image. This div may contain highcharts along with other child divs, etc




  container=jQuery('#visualization');
  //container=jQuery('.vis-content');

  var svgElements= container.find('svg');
var canvas, xml;

console.log("svg elements ",svgElements);

  //replace all svgs with a temp canvas
  var c=0;
  svgElements.each(function () {
      if(c==0) {
      

      canvas = document.createElement("canvas");
      canvas.className = "screenShotTempCanvas";
      //convert SVG into a XML string
      xml = (new XMLSerializer()).serializeToString(this);

      // Removing the name space as IE throws an error
      xml = xml.replace(/xmlns=\"http:\/\/www\.w3\.org\/2000\/svg\"/, '');


      //draw the SVG onto a canvas
      canvg(canvas, xml);
      

   
     

      jQuery(canvas).insertAfter(this);
    return;
      console.log("ja he inserit el canvas",canvas);
      //hide the SVG element
      this.className = "tempHide";
      jQuery(this).hide();
    }
      c++;

  });


  //...
  // HTML2CANVAS SCREENSHOT
  //...
html2canvas(container, {
  onrendered: function(canvas) {
    document.body.appendChild(canvas);
  }
});


  //After your image is generated revert the temporary changes
  container.find('.screenShotTempCanvas').remove();
 container.find('.tempHide').show().removeClass('tempHide');

}
</script>