<?php

/* retorna un llistat amb les sessions disponibles */

function va_get_sessions(){

	global $wpdb; 

    $sql="SELECT * FROM videoanalytics group by rndk order by ti desc";

    $posts = $wpdb->get_results($sql);
    return $posts;


}

/* retorna les dades d'una sessió en concret */


function va_session_get($session_id){
	global $wpdb; 

    $sql="SELECT * FROM videoanalytics where rndk=".$session_id." order by ta asc";

    $posts = $wpdb->get_results($sql);
    return $posts;
}