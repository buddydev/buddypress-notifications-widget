<?php
/**
 * Plugin Name: BuddyPress Notifications Widget
 * Author: Brajesh Singh
 * Versions:1.0
 * Plugin URI: http://buddydev.com/plugins/buddypress-notifications-widget/
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Version:1.0
 * Description: Allow site admins to show BuddyPress user notification in widget.
 * License: GPL
 */

//localization
function bpdev_bpdnw_load_textdomain(){

        $locale = apply_filters( 'bpdnw_load_textdomain_get_locale', get_locale() );
        
      
	// if load .mo file
	if ( !empty( $locale ) ) {
		$mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path(__FILE__), $locale );
              
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
       
            $name=__( 'BP Notifications', 'bpdnw' );
        parent::__construct( false, $name );
    }
    
    function BPDev_BPNotification_Widget(){
        $this->__construct();
    }
    
    //display
    function widget($args,$instance){
       if(!is_user_logged_in())
           return ;//do not show anything if user is not logged in
       extract( $args );
       
        //let us get the notifications for the user
        $notifications = bp_core_get_notifications_for_user( bp_loggedin_user_id() );
        if(empty($notifications))//will be set to flase if there are no notifications
            $count=0;
        else
        $count=count($notifications);
       
    
        echo $before_widget;
		echo $before_title;
                    echo $instance['title'];
                    if($instance['show_count_in_title'])
                        printf("<span class='notification-count-in-title'>(%d)</span>",$count);
                echo  $after_title;
                if($instance['show_count'])
                    printf(__('You have %d new Notifications','bpdnw'),$count);
                if($instance['show_list'])
                    self::print_list($notifications,$count);
                
         echo $after_widget;       
                
    }
    //update
    
    function update($new_instance,$old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['show_count_in_title'] = intval($new_instance['show_count_in_title'] ) ;
        $instance['show_count'] = intval($new_instance['show_count'] ) ;
        $instance['show_list'] = intval($new_instance['show_list'] ) ;
        return $instance;
    }
    //widget option form if any?
    function form($instance){
      $instance = wp_parse_args( (array) $instance, array( 'title' => __('Notifications','bpdnw'), 'show_count' =>1,'show_count_in_title'=>0,'show_list'=>1 ) );
      $title = strip_tags( $instance['title'] );
      $show_count_in_title = $instance['show_count_in_title'] ;//show notification count
      $show_count = $instance['show_count'] ;//show notification count
      $show_list =  $instance['show_list'] ;//show notification list  
      ?>
       <p>
           <label for="bp-notification-title"><strong><?php _e('Title:', 'bpdnw'); ?> </strong>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" style="width: 100%" />
           </label>
       </p>
       <p>
            <label for="bp-show-notification-count-in-title"><?php _e('Show Notification count in Title', 'bpdnw'); ?> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count_in_title' ); ?>" name="<?php echo $this->get_field_name( 'show_count_in_title' ); ?>" type="checkbox" value="1" <?php if($show_count_in_title ) echo 'checked="checked"';?>style="width: 30%" />
            </label>
        </p>
	<p>
            <label for="bp-show-notification-count"><?php _e('Show Notification count', 'bpdnw'); ?> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" value="1" <?php if($show_count ) echo 'checked="checked"';?>style="width: 30%" />
            </label>
        </p>
	<p>
            <label for="bp-show-notification-list"><?php _e('Show the list of Notifications', 'bpdnw'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_list' ); ?>" name="<?php echo $this->get_field_name( 'show_list' ); ?>" type="checkbox" value="1" <?php if($show_list) echo 'checked="checked"'; ?> style="width: 30%" />
            </label>
        </p>
<?php	
    }
 //helper
    
    function print_list($notifications,$count){
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
	add_action('widgets_init', create_function('', 'return register_widget("BPDev_BPNotification_Widget");') );
	
	}
add_action( 'bp_init', 'bpdev_notification_register_widget' );

?>