
//var max_y=5; //todo config
//variables globals
var compta_desfase=true;
var data_vis;
var punts=[];
var ctx;
var data_g=[];
var data;
var current_group="";
/** carregar dades */

function ajax_load(url){
    jQuery.getJSON(url,function(indata){
        data=indata;
        init();
    });
}


/** crear gràfic */

function init(){
  var items=[];
  var last="";
  var group=0;
  var dt=0;
  var desfase=0;
  var l=data.length;
  var txt="";
  var c=0;

  //data_g=[];
  data_g={label:group,data:[]}; //co
  data_vis=[];

  for(var i=0;i<l;i++){

    var d=data[i];


    if(d[group_separator]!=last){ //add a new group in visualization

        if(i!=0) txt+="</ul>";

        desfase=0;
        dt=d.ts;
        last=d[group_separator];
        group++;

        if(current_group=="" || current_group==group){
          console.log("afegint el grup",group);
          data_vis.push(data_g);

        }
            data_g={label:group,data:[]}; //color?
        //todo cal afegir l'ultim correctament!
        txt+="<p class='legend'>Group "+group+" "+group_separator+": "+last;
        txt+=" <span class='view'>view</span>  <span class='graph'>graph</span> "+"</p>";

        txt+="<ul data-group="+group+" class='legend'>";

        c=0;

    }

   // items.push({y:d.ts-dt-desfase,x:parseFloat(d.params),group:group});
   if(c>0){
     //si es una pausa
     d2=data[i-1];

     //si temps va enrrera x i hi ha increment y
     if(d.params==d2.params){

       var dy=d.ts-d2.ts;

       if(dy>max_y) desfase=desfase-max_y+dy;
     }


     if(d.params<(d2.params-1)){

       //limitar increment y
       //el desfase ja esta corregit abans
       //var dy=d.ts-d2.ts;
       //if(dy>max_y) desfase=desfase-max_y+dy;
       //afegir tram
       console.log("correccio a grup",group,d.params,d2.params);

       //var dy=d.ts-d2.ts+dt+desfase;
       //if(dy>max_y) desfase=desfase-max_y+dy;

       data_g.data.push([d.params,d2.ts-dt-desfase]);
       //ara cal corregir desfase y per la següent

       var dy=d.ts-d2.ts;
       if(dy>max_y) desfase=desfase-max_y+dy;
     }


   }
   data_g.data.push([parseFloat(d.params),d.ts-dt-desfase]);

  txt+="<li>"+d.act+" "+(d.ts-dt)+" "+d.params;
//  if(desfase>0) txt+=" pausa acumulada: "+desfase;
  txt+="</li>";


    if(i<l-1){ //if not last
        if( data[i+1][group_separator]==d[group_separator] ){ //if next in same group

          //register borders
          if(group_separator=="rndk"){

            var next_status=data[i+1].act;
            var s=d.act+next_status;
            if(punts[s]==undefined){
              punts[s]=[];
              for(var j=0;j<=duration;j++) punts[s][j]=0;
            }
            punts[s][parseInt(d.params)]++;
          }
        }

    }else{ //l'ultim
      data_vis.push(data_g);

    }
    //fi si te següent
if(i==l-1) txt+="</ul>";
    c++;
  }//end for


  jQuery("div.log-list").html(txt);
  addLogEvents();
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

    //jQuery.plot(".vis-placeholder", {}, options);
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


function addLogEvents(){
  jQuery('.legend .view').on('click',function(){
    console.log("clicat",jQuery(this).parent().next());
    jQuery(this).parent().next().toggle('fast');
  });
  jQuery('.legend .graph').on('click',function(){
    console.log("clicat",jQuery(this).parent().next());
    var group=jQuery(this).parent().next().data('group');
    current_group=group;
    init();
  });
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
