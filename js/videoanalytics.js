
//global variables
var compta_desfase=true;
var data_vis;
var punts=[];
var ctx;
var data_g;
var data;
var current_group="";
var group;
var first_time=true;
/** load data */

function ajax_load(url){
    jQuery.getJSON(url,function(indata){
        data=indata;
        init();
    });
}


/** create graph */

function init(){
  var items=[];
  var last="";
  data_g=[];

  var dt=0;
  var desfase=0;
  var l=data.length;
  var txt="";
  var c=0;

  data_g={label:group,data:[]};
  data_vis=[];
  group=0;

  for(var i=0;i<l;i++){

    var d=data[i];

    if(d[group_separator]!=last){ //add a new group in visualization

        if(i!=0) txt+="</ul>";

        desfase=0;
        dt=d.ts;
        last=d[group_separator];
        group++;

        if( (current_group=="" || current_group==group-1) && last!="" ){
          if(data_g.data.length>0)
            data_vis.push(data_g);
        }
        data_g={label:group,data:[]}; //color?

        txt+="<p class='legend'>Group "+group+" "+group_separator+": "+last;
        txt+="| <span class='view'>view</span> "+"</p>";
        txt+="<ul data-group="+group+" class='legend'>";

        c=0;

    }

   if(c>0){

     d2=data[i-1];

     //if time goes back correct max increment
     if(d.params==d2.params){
       var dy=d.ts-d2.ts;
       if(dy>max_y) desfase=desfase-max_y+dy;
     }

     if(d.params<(d2.params-1)){

       data_g.data.push([d.params,d2.ts-dt-desfase]);

       var dy=d.ts-d2.ts;
       if(dy>max_y) desfase=desfase-max_y+dy;
     }


   }
   data_g.data.push([parseFloat(d.params),d.ts-dt-desfase]);

  txt+="<li>"+d.act+" "+(d.ts-dt)+" "+d.params;
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
      if(current_group=="" || current_group==group)
        data_vis.push(data_g);

    }

if(i==l-1) txt+="</ul>";
    c++;
  }//end for

  if(first_time){

      jQuery("div.log-list").html(txt);
      addLogEvents();
      jQuery(".more-info").html(group+" groups");

  }
  first_time=false;
  doVis();
}

/** show visualization */
function doVis(){

    // http://www.flotcharts.org/flot/examples/canvas/

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
    jQuery('.log-list ul').hide();
    var group=jQuery(this).parent().next().data('group');
    current_group=group;
    init();
    jQuery(this).parent().next().toggle('fast');
  });

  jQuery('#view-all').on('click',function(ev){
    jQuery('.log-list ul').hide();
    ev.preventDefault();
    current_group="";
    init();

  });
}


/** download graph */

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
