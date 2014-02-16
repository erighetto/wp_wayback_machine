<?php

/*
Plugin Name: Wayback Machine
Description: Mostra i link ai post vecchi scritti nello stesso giorno negli anni precedenti
Version: 0.3
Author: Emanuel Righetto
Author URI: http://www.consulenzaweb.net
*/

class WaybackMachineWidget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'wpwbm_widget', 

// Widget name will appear in UI
__('Wayback Machine Widget', 'wpwbm_widget_domain'), 

// Widget description
array( 'description' => __( 'Mostra i link ai post vecchi di un blog scritti nello stesso giorno però negli anni precedenti', 'wpwbm_widget_domain' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
echo "<ul>".wb_machine()."</ul>";
echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Wayback Machine Widget', 'wpwbm_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
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
        $sql = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title, DATE_FORMAT($wpdb->posts.post_date, '%Y') as post_year ";
        $sql.= "FROM $wpdb->posts YEAR($wpdb->posts.post_date)<" .date("Y");
		    $sql.= " AND MONTH($wpdb->posts.post_date)=" .date("m");
			  $sql.= " AND DAYOFMONTH($wpdb->posts.post_date)=" .date("d");
			  $sql.= " AND $wpdb->posts.post_status = 'publish' ";
			  $sql.= " AND $wpdb->posts.post_type = 'post' "; 
			  $sql.= " AND $wpdb->posts.post_password = '' ";
			  $sql.= "ORDER BY $wpdb->posts.post_date DESC";

			 

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
