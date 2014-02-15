<?php

/*
Plugin Name: Wayback Machine
Description: Mostra i link ai post vecchi scritti nello stesso giorno negli anni precedenti
Version: 0.3
Author: Emanuel Righetto
Author URI: http://www.consulenzaweb.net
*/

class WaybackMachineWidget extends WP_Widget {

	function WaybackMachineWidget() {
		// Istanzia l'oggetto genitore
		parent::__construct( false, 'Titolo del widget' );
	}

	function widget( $args, $instance ) {
		// Output del widget
		$instance = "<ul>". wb_machine() ."</ul>";
	}

	function update( $new_instance, $old_instance ) {
		// Salva le opzioni del widget
	}

	function form( $instance ) {
		// Stampa il modulo di amministrazione con le opzioni del widget
	}
}

function wp_wayback_machine_register_widgets() {
	register_widget( 'WaybackMachineWidget' );
}

add_action( 'widgets_init', 'wp_wayback_machine_register_widgets' );



/* Cerco se ci sono dei post in questo stesso giorno per gli anni passati */
function wb_machine() {
global $before_title, $after_title, $sql, $results, $result, $wpdb, $title, $permalink;



$before_title = '<li>';
$after_title = '</li>';





        // Primary SQL query    
        $sql = "SELECT ID, post_title, DATE_FORMAT(post_date, '%Y') as post_year ";
        $sql.= "FROM wp_posts YEAR(post_date)<" .date("Y");
		    $sql.= " AND MONTH(post_date)=" .date("m");
			  $sql.= " AND DAYOFMONTH(post_date)=" .date("d");
			  $sql.= " AND post_status = 'publish' ";
			  $sql.= " AND post_type = 'post' "; 
			  $sql.= " AND post_password = '' ";
			  $sql.= "ORDER BY post_date DESC";

			 

		//interrogo il database	 
        $results = $wpdb->get_results($sql);

		

      

        if ($results) {
            foreach ($results as $result) {

                $title = stripslashes($result->post_title);
				        $permalink = get_permalink($result->ID);

                echo $before_title .$result->post_year.' - <a href="'. $permalink .'" rel="bookmark" title="Link permanente a: ' . $title . '" class="snap_preview">' . $title . '</a>' . $after_title;

            }

        } else {

           echo $before_title.'<small>Non ci sono in archivio post pubblicati  in questo giorno</small>'.$after_title;

        }

}



?>
