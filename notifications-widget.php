<?php
/**
 * Plugin Name: BuddyPress Notifications Widget
 * Author: Brajesh Singh
 * Versions:1.0.9
 * Plugin URI: http://buddydev.com/plugins/buddypress-notifications-widget/
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Description: Allow site admins to show BuddyPress user notification in widget.
 * License: GPL
 */

//localization
function bpdev_bpdnw_load_textdomain(){

        $locale = apply_filters( 'bpdnw_load_textdomain_get_locale', get_locale() );
        
      
	// if load .mo file
	if ( !empty( $locale ) ) {
		$mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path( __FILE__ ), $locale );
              
		$mofile = apply_filters( 'bpdnw_load_textdomain_mofile', $mofile_default );
		
                if ( file_exists( $mofile ) ) {
                    // make sure file exists, and load it
			load_textdomain( 'bpdnw', $mofile );
		}
	}
}
add_action ( 'bp_loaded', 'bpdev_bpdnw_load_textdomain', 2 );

//widget class
class BPDev_BPNotification_Widget extends WP_Widget{
    function __construct(){
       
       $name = __( '(BuddyDev) BP Notifications', 'bpdnw' );
       parent::__construct( false, $name );
    }
    
    function BPDev_BPNotification_Widget(){
        $this->__construct();
    }
    
    //display
    function widget( $args, $instance ){
       if( ! is_user_logged_in() )
           return ;//do not show anything if user is not logged in
       
       extract( $args );
       
        //let us get the notifications for the user
        $notifications = bp_notifications_get_notifications_for_user( get_current_user_id(), $format='simple' );
        
        if( empty( $notifications ) )//will be set to flase if there are no notifications
            $count = 0;
        else
            $count = count( $notifications );
       
        if( $count <= 0 )
           return;//do not show this widget
    
        echo $before_widget;
		echo $before_title;
                    echo $instance['title'];
                    if( $instance['show_count_in_title'] )
                        printf( "<span class='notification-count-in-title'>(%d)</span>", $count );
                echo  $after_title;
                echo "<div class='bpnw-notification-list'>";
               
                if( $instance['show_count'] )
                    printf( __( 'You have %d new Notifications', 'bpdnw' ), $count );
                
                if( $instance['show_list'] )
                    self::print_list( $notifications, $count );
                
                if( $count > 0 )
                      echo '<a class="clear-widget-notifications" href="'. bp_core_get_user_domain( get_current_user_id() ).'?clear-all=true' . '&_wpnonce=' . wp_create_nonce('clear-all-notifications-for-' . bp_loggedin_user_id() ). '">' . __( '[x] Clear All Notifications', 'bpdnw' ) . '</a>';
	
                echo "</div>";
         echo $after_widget;       
                
    }
    //update
    
    function update( $new_instance, $old_instance ){
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['show_count_in_title'] = intval( $new_instance['show_count_in_title'] ) ;
        $instance['show_count'] = intval( $new_instance['show_count'] ) ;
        $instance['show_list'] = intval( $new_instance['show_list'] ) ;
        return $instance;
    }
    //widget option form if any?
    function form( $instance ){
      $instance = wp_parse_args(
                    (array) $instance,
                    array( 
                        'title'                     => __( 'Notifications', 'bpdnw' ),
                        'show_count'                => 1,
                        'show_count_in_title'       => 0,
                        'show_list'                 => 1,
                        'show_clear_notification'   => 1
                        )
              );
      
      $title = strip_tags( $instance['title'] );
      $show_count_in_title = $instance['show_count_in_title'] ;//show notification count
      $show_count = $instance['show_count'] ;//show notification count
      $show_list =  $instance['show_list'] ;//show notification list  
      $show_clear_notification = $instance['show_clear_notification'];
      ?>
       <p>
           <label for="bp-notification-title"><strong><?php _e('Title:', 'bpdnw'); ?> </strong>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
           </label>
       </p>
       <p>
            <label for="bp-show-notification-count-in-title"><?php _e( 'Show Notification count in Title', 'bpdnw' ); ?> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count_in_title' ); ?>" name="<?php echo $this->get_field_name( 'show_count_in_title' ); ?>" type="checkbox" value="1" <?php if($show_count_in_title ) echo 'checked="checked"';?>style="width: 30%" />
            </label>
        </p>
	<p>
            <label for="bp-show-notification-count"><?php _e( 'Show Notification count', 'bpdnw' ); ?> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" value="1" <?php if($show_count ) echo 'checked="checked"';?>style="width: 30%" />
            </label>
        </p>
	<p>
            <label for="bp-show-notification-list"><?php _e( 'Show the list of Notifications', 'bpdnw' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_list' ); ?>" name="<?php echo $this->get_field_name( 'show_list' ); ?>" type="checkbox" value="1" <?php if($show_list) echo 'checked="checked"'; ?> style="width: 30%" />
            </label>
        </p>
        <p>
            <label for="bp-show_clear_notification"><?php _e( 'Show the Clear Notifications button', 'bpdnw' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_clear_notification' ); ?>" name="<?php echo $this->get_field_name( 'show_clear_notification' ); ?>" type="checkbox" value="1" <?php if($show_clear_notification) echo 'checked="checked"'; ?> style="width: 30%" />
            </label>
        </p>
<?php	
    }
 //helper
    
    function print_list( $notifications, $count ){
        echo '<ul class="bp-notification-list">';
        
	if ( $notifications ) {
		$counter = 0;
		for ( $i = 0; $i < $count; $i++ ) {
			$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

			<li<?php echo $alt ?>><?php echo $notifications[$i] ?></li>

			<?php $counter++;
		}
	} else { ?>

		<li><?php _e( 'You don\'t have any new notification.', 'bpdnw' ); ?></li>

	<?php
	}

	echo '</ul>';
    }
}
//register widgets
function bpdev_notification_register_widget() {
	register_widget( 'BPDev_BPNotification_Widget' );
	
	}
add_action( 'bp_widgets_init', 'bpdev_notification_register_widget' );

//load javascript
add_action('bp_enqueue_scripts','bpdev_notification_widget_load_js');
function bpdev_notification_widget_load_js(){
    wp_enqueue_script('notification-clear-js',  plugin_dir_url(__FILE__)."notification.js",array('jquery'));
}

//ajaxed delete
add_action( 'wp_ajax_bpdev_notification_clear_notifications', 'bpdev_notification_clear_notifications' );
function bpdev_notification_clear_notifications(){
        //CHECK VALIDITY OF NONCE
    
        if( !is_user_logged_in() )
            return;
        
        $user_id = bp_loggedin_user_id();
        
        check_ajax_referer( 'clear-all-notifications-for-' . $user_id );
        
        global $bp, $wpdb;
        
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->core->table_name_notifications} WHERE user_id = %d ", $user_id ) );
        
        echo "1";
        exit(0);
    }
    
