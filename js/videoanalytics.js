
//variables globals
var compta_desfase=true;
var correccions=true;

var data_vis=[];
 
var punts=[];
var ctx;

/** carregar dades */  

function ajax_load(url){
    jQuery.getJSON(url,function(data){
      console.log(data);
        init(data);
    });
}
 

/** crear gràfic */

function init(data){
  var items=[];
  var last="";
  var group=0;
  var dt=0;
  var desfase=0;
  var l=data.length;
  var txt="";
  var data_g=[];
 

  console.log("en total hi ha ",l,data);
  for(var i=0;i<l;i++){
    console.log("pas ",i);
    var d=data[i];
    if(d[group_separator]!=last){
      desfase=0;
      dt=d.ts;
      last=d[group_separator];
      group++;
      data_vis.push(data_g);
      data_g={label:group,data:[]}; //color?
      //todo cal afegir l'ultim correctament!
      txt+="<li class='legend'>Group "+group+" "+group_separator+": "+last+"</li>";
      console.log("afegeixo un");
    }
    
   // items.push({y:d.ts-dt-desfase,x:parseFloat(d.params),group:group});
   data_g.data.push([parseFloat(d.params),d.ts-dt-desfase]);

  txt+="<li>"+d.act+" "+(d.ts-dt)+" "+d.params;
  if(desfase>0) txt+=" pausa acumulada: "+desfase;
  txt+="</li>";

    if(i<l-1){ //si te següent
        if( data[i+1][group_separator]==d[group_separator] ){ //si el següent és de la mateixa sessió

          //registrar punts de canvi
          if(group_separator=="rndk"){

            var next_status=data[i+1].act;
            var s=d.act+next_status;
            if(punts[s]==undefined){
              punts[s]=[];
              for(var j=0;j<=duration;j++) punts[s][j]=0;
            }
            punts[s][parseInt(d.params)]++;
          }

          //correccions del grafic
          /** eliminem el temps després d'una pausa perquè
                el
                gràfic es vegi més bé
                */
              if(correccions){ 
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

              }
            }
          }//end if correccions


        }

    }else{ //l'ultim
      data_vis.push(data_g);
      console.log("he afgit lultim",data_vis);
    }
    //fi si te següent


  }//end for
             

  jQuery("ul.log").html(txt);
  jQuery(".more-info").html(group+" groups");
 
  doVis();
        
 
}

/** mostra visualització*/
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

}



/**descarregar imatge*/

function dlCanvas() {
  var dt = ctx.toDataURL('image/png');
  /* Change MIME type to trick the browser to downlaod the file instead of displaying it */
  dt = dt.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');


 jQuery('#dl').attr('download','videoanalytics_'+image_id+'.png');
   /* In addition to <a>'s "download" attribute, you can define HTTP-style headers */
  dt = dt.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=videoanalytics'+image_id+'.png');
  this.href = dt;
};

document.getElementById("dl").addEventListener('click', dlCanvas, false);