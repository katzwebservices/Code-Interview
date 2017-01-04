<?php
/**
 * Plugin Name:         Exammple Funny Quotes Plugin
 * Plugin URI:        	https://example.com
 * Description:       	Assume this is a WordPress plugin file. Improve the code as much as you possibly can. *Make sure to update the readme.txt with a detailed changelog*.
 * Version:          	1.1.0
 * Author:            	Not Really Funny
 * Text Domain :		funny-quotes
 * Domain Path :		/languages
 * Author URI:        	https://example.com
 * License:           	GPLv2 or later
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Add up all this plugins actions
 */
add_action( 'init', 'funny_quotes_setup');
add_action( 'widgets_init', 'funny_quotes_widget_register' );
add_action( 'plugins_loaded', 'funny_quotes_plugin_textdomain' );


/**
 * Funny quote plugin init actions. 
 *
 * Register the `funny-quote` custom post type.
 *
 * @since 1.1.0
 */
function funny_quotes_setup()
{
	register_post_type( 
		'funny-quote', 
		array( 
				'labels' => array( 
					'name_admin_bar' => 'Quotes' 
				), 
			'public'  => true 
		)
	);
}
/**
 * Register the actual funny quotes widget
 *
 * @since 1.1.0
 */
function funny_quotes_widget_register() {
	register_widget( 'funny_quotes_widget' );
}

/**
 * Loads up the files needed for localization (required for WordPress versions pre 4.6).
 *
 * @since 1.1.0
 */
function funny_quotes_plugin_textdomain() {
    load_plugin_textdomain( 'funny-quotes', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Builds,Renders and modifies the funny quote WordPress widget.
 * 
 * @since 1.1.0
 */
class funny_quotes_widget extends WP_Widget {

	/**
	 * Stores default widget values
	 * 
	 * @since 1.1.0
	 * @access private
	 */
	private $defaults;

	function __construct() {
		/**
		 * Set default values for the widget fields.
		 */
		$this->defaults['numberofquotes'] = 3;
		$this->defaults['title'] = __('Funny & Motivational Quotes', 'funny-quotes');
		// Instantiate the parent object
		parent::__construct( false, __('Funny & Motivational Quotes', 'funny-quotes'));
	}

	/**
	 * Render the actual widget.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function widget( $args, $instance ) {
		$D = &$this->defaults;
		extract($args);
		$title   = ( isset( $instance['title'] ) ) ? $instance['title'] : $D['title'];
		$num   = ( isset( $instance['numberofquotes'] ) ) ? $instance['numberofquotes'] : $D['numberofquotes'];
		echo $before_widget;
		if(!empty($title)) { echo  $before_title . $title . $after_title;}
		echo funny_quotes_get_quotes($num);
		echo $after_widget;
	}

	/**
	 * Save widget options.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['numberofquotes']      = absint( strip_tags( $new_instance['numberofquotes'] ) );
		return $instance;
	}

	/**
	 * Output the widget options form in wp-admin.
	 * 
	 * @since 1.1.0
	 * @access public
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/ 
	 */
	function form( $instance ) {
		$defaults = array(
			'title'          => $this->defaults['title'],
			'numberofquotes' => $this->defaults['numberofquotes'],
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'funny-quotes' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'numberofquotes' ) ); ?>"><?php _e( 'Show # of Quotes', 'funny-quotes' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'numberofquotes' ) ); ?>">
				<?php for ($i=0; $i < 10; $i++) { ?>
					<option value="<?php echo $i ?>" <?php selected( $instance['numberofquotes'], $i ); ?>>
						<?php echo $i ?>
					</option>
				<?php } ?>
			</select>
		</p>

		<?php
	}
}



/**
 * Shows quotes that are stored as "funny-quote" post type on the site.
 *
 * @since 1.1.0
 * @param int $number_to_show number of quotes to show.
 * @return string containing requested number of quotes.
 * 
 */
function funny_quotes_get_quotes( $number_to_show ) {
	$quotes = get_posts(
		array(
			'numberposts' => $number_to_show,
			'post_type' => 'funny-quote'
		)
	);

	$output = sprintf( __('Showing %d quotes:' , 'funny-quotes') , $number_to_show ) ;

	// 'Showing '.$number_to_show.' quotes:';

	foreach ( $quotes as $quote ) {

		$output .= wpautop( $quote->post_content );

	}

	return $output;
}