<?php

/**
 retorna un llistat amb les sessions disponibles
 */

function va_get_sessions(){

	global $wpdb; 
  	$table_name = $wpdb->prefix . "videoanalytics";

    $sql="SELECT *,count(*) as t FROM $table_name group by rndk order by ti desc";

    $posts = $wpdb->get_results($sql);
    return $posts;


}

/**
retorna les dades d'una sessió en concret
*/


function va_session_get($session_id,$track=""){
  global $wpdb; 
  $table_name = $wpdb->prefix . "videoanalytics";

    $sql="SELECT * FROM $table_name where rndk=".$session_id;
    if($track!="") $sql.=" and video='$track' ";
    $sql.=" order by ta asc";

    $posts = $wpdb->get_results($sql);
    return $posts;
}


/**
 retorna un llistat amb tots els videos disponibles
 */

function va_get_videos(){

  global $wpdb; 
  $table_name = $wpdb->prefix . "videoanalytics";

  $sql="SELECT video,count(*) as t FROM $table_name group by video order by t desc";
  $posts = $wpdb->get_results($sql);
  return $posts;
}

/**
 retorna totes les sessions d'un video concret
 */

function va_get_sessions_video($video){

  global $wpdb; 
    $table_name = $wpdb->prefix . "videoanalytics";

    $sql="SELECT * FROM $table_name WHERE video='$video' order by rndk, ta asc";

    $posts = $wpdb->get_results($sql);
    $res=array();
    foreach($posts as $p){
      //ta és el de l'acció      
      $p->ts= (new DateTime($p->ta))->getTimestamp();


      



      unset($p->video);
      unset($p->ti);
      unset($p->ta);

      $res[]=$p;
    }
    return $res;


}

//todo en un periode de temps


/**
 crides a la api, retornen json
 */

function api($method,$params){
  switch($method){
      case "sessions_videos":
      $posts=va_get_sessions_video($params['video']);
      print json_encode($posts);
      break;


  }



}



/**
demanar durada video a vimeo 
*/


//test 
//$duration=videoanalytics_get_duration('06507_m2_exercici6');
//print "duration : ".$duration;

function videoanalytics_get_duration($video_name){
  global $wpdb;
  //primer mirar si existeix a la base de dades
  $table_name = $wpdb->prefix . "videoanalytics_video";
  $query="SELECT * FROM $table_name WHERE video='".$video_name."'";
  $results=$wpdb->get_results($query);

  if(count($results)>0){

    return $results[0]->duration;

  }
  

  //en cas contrari demanar a vimeo i guardar resultat
  //com sembla que no es pot demanar a vimeo s'estima amb aquesta query
  $table_name_main = $wpdb->prefix . "videoanalytics";
   $query="SELECT * FROM $table_name_main WHERE video='".$video_name."' and act='pausa' order by params desc";
   $results=$wpdb->get_results($query);
  
   if(count($results)>0){
    $timestamp = date('Y-m-d G:i:s');
    $duration=$results[0]->params;
    $query="INSERT INTO $table_name (video,duration,updated) values ('$video_name',$duration, '$timestamp')";
    
    $wpdb->query($query);

   }

}