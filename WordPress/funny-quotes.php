<?php
/**
 * Plugin Name:         Exammple Funny Quotes Plugin
 * Plugin URI:        	https://example.com
 * Description:       	Assume this is a WordPress plugin file. Improve the code as much as you possibly can. *Make sure to update the readme.txt with a detailed changelog*.
 * Version:          	1.0
 * Author:            	Not Really Funny
 * Author URI:        	https://example.com
 * License:           	GPLv2 or later
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
 */

add_action('init', 'quotes_SetUp');

function quotes_SetUp()
{
	register_post_type( 'funny-quote', array( 'labels' => array( 'name_admin_bar' => 'Quotes' ), 'public'  => true ));
}


function quotes_init()
{
	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_quotes_control() {
		$options = $newoptions = get_option('quotes' );
		if ( !is_array($newoptions) )
			$newoptions = array(
				'title' => 'Funny & Motivational Quotes',
				'num' => 1,
			);
		if ( $_POST['quotes-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['quotes-title']));
			$newoptions['num'] = stripslashes($_POST['quotes-num']);
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('quotes', $options);
		}
		$title = $options['title'];
		$num = $options['num'];

		echo '
		<div>
			<p><label for="quotes-title">Title of Widget:<br />
					<input style="width:100%;" id="quotes-title" name="quotes-title" type="text" value="'.$title.'" />
				</label>
			</p>
			
			<p>
				<label for="quotes-num">Show # of Quotes:
					<select name="quotes-num" id="quotes-num">
						<option value="1"';
		if($num == 1) { echo ' selected="selected"';}
		echo '>1</option>
						<option value="2"';
		if($num == 2) { echo ' selected="selected"';}
		echo '>2</option>
						<option value="3"';
		if($num == 3) { echo ' selected="selected"';}
		echo '>3</option>
						<option value="4"';
		if($num == 4) { echo ' selected="selected"';}
		echo '>4</option>
						<option value="5"';
		if($num == 5) { echo ' selected="selected"';}
		echo '>5</option>
						<option value="6"';
		if($num == 6) { echo ' selected="selected"';}
		echo '>6</option>
						<option value="7"';
		if($num == 7) { echo ' selected="selected"';}
		echo '>7</option>
					</select>
				</label>
			</p>
		
			<input type="hidden" id="quotes-submit" name="quotes-submit" value="1" />
		</div>
			';
	}

	function quotes_widget($args) {
		extract($args);
		$options = get_option('quotes');
		$title = $options['title'];
		$num = isset($_GET['numberofquotes']) ? $_GET['numberofquotes'] : $options['num'];

		echo $before_widget;
		if(!empty($title)) { echo  $before_title . $title . $after_title;}
		echo quotes($num);
		echo $after_widget;
	}

	register_widget_control('Funny & Motivational Quotes', 'widget_quotes_control', 300, 450);
	register_sidebar_widget(array('Funny & Motivational Quotes', 'funny-motivational-quotes'), 'quotes_widget');
}

add_action('widgets_init', 'quotes_init');

// Show quotes that are stored as "funny-quote" post type on the site
function quotes( $number_to_show ) {
	global $wpdb;
	$sql = "SELECT * FROM wp_posts WHERE post_type = 'funny-quote'";
	$quotes = $wpdb->get_results( $sql, ARRAY_A );
	
	$output = 'Showing '.$number_to_show.' quotes:';

	foreach ( $quotes as $quote ) {
		$output .= wpautop( $quote['post_content'] );
	}

	return $output;
}