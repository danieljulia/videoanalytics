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
<ul class="log">
</ul>
</div>
<script type="text/javascript">

//variables globals
  var compta_desfase=true;
  var data_vis=[];
  var pausa=[];
  var ff=[];
  var rw=[];
 var punts=[];
  var ctx;
  var duration=<?php print intval($duration) ?>;

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
    var data_g=[];
   


    for(var i=0;i<l;i++){
      var d=data[i];
      if(d.rndk!=last){
        desfase=0;
        dt=d.ts;
        last=d.rndk;
        group++;
        data_vis.push(data_g);
        data_g={label:group,data:[]}; //color?
        //todo cal afegir l'ultim correctament!
        txt+="<li class='legend'>Session "+group+"</li>";
      }
      
     // items.push({y:d.ts-dt-desfase,x:parseFloat(d.params),group:group});
     data_g.data.push([parseFloat(d.params),d.ts-dt-desfase]);

      txt+="<li>"+d.act+" "+(d.ts-dt)+" "+d.params;
      if(desfase>0) txt+=" pausa acumulada: "+desfase;
      txt+="</li>";

      
     
          if(i<l-1){ //si te següent
            if(data[i+1].rndk==d.rndk){ //si el següent és de la mateixa sessió

 
                var next_status=data[i+1].act;

                var s=d.act+next_status;
                if(punts[s]==undefined){
                  punts[s]=[];
                  for(var i=0;i<=duration;i++) punts[s][i]=0;
                }
              /*
                if(punts[s][parseInt(d.params)]==undefined){
                  punts[s][parseInt(d.params)]=0;
                }*/
                punts[s][parseInt(d.params)]++;
                console.log(punts);
                /** eliminem el temps després d'una pausa perquè
                el
                gràfic es vegi més bé
                */

                if( d.act=="pausa"  ){
                  if(next_status="buscado" || next_status=="pausa" ){
                        //items.push({y:d.ts-dt,x:0,group:group});
                        data_g.data.push([d.params,d.ts-dt-desfase]);
                        if(compta_desfase){
                          desfase+=data[i+1].ts-d.ts;
                        }
                      
                  }
                  if(next_status=="play" ){ //una pausa molt gran que no volem que es visualitzi
                        //alert("desfase");
                         data_g.data.push([d.params,d.ts-dt-desfase]);
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
  
          }else{

            
              data_vis.push(data_g-desfase);
              console.log("he afegit lutil");
             //afegit ultim tros
            
          
          }
          
       
      }
               

    console.log("rewinds i fastforwards",rw,ff);

    jQuery("ul.log").html(txt);
    jQuery(".more-info").html(group+" sessions");
   
    doVis();
          
   
}




function doVis(){



    //veure exemple view-source:http://www.flotcharts.org/flot/examples/canvas/

    var options = {
      canvas: true
      //xaxes: [ { mode: "time" } ],
      /*yaxes: [ { min: 0 }, {
        position: "right",
        alignTicksWithAxis: 1,
        tickFormatter: function(value, axis) {
          return value.toFixed(axis.tickDecimals) + "€";
        }
      } ]*/,
      lines:{show:true},
      points:{show:true},
      legend: { position: "sw" }
    }


   var plot=jQuery.plot(".vis-placeholder",
      data_vis,
      options);

    /*
    var plot=jQuery.plot(".vis-placeholder",
      [{data:data_vis,lines:{show:true},canvas:true}],
      options);
  */
    ctx = plot.getCanvas();

    // Add the Flot version string to the footer

    jQuery("#footer").prepend("Flot " + jQuery.plot.version + " &ndash; ");
}


//todo canviar de localitzacio



function dlCanvas() {
  var dt = ctx.toDataURL('image/png');
  /* Change MIME type to trick the browser to downlaod the file instead of displaying it */
  dt = dt.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');

  var id="<?php print $video?>";


 jQuery('#dl').attr('download','videoanalytics_'+id+'.png');
   /* In addition to <a>'s "download" attribute, you can define HTTP-style headers */
  dt = dt.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=videoanalytics'+id+'.png');
  this.href = dt;
};
document.getElementById("dl").addEventListener('click', dlCanvas, false);

</script>