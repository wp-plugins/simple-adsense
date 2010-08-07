<?php
/**
 * Plugin Name: Simple Adsense
 * Plugin URI: http://www.webfish.se/wp/plugins/simple-adsense
 * Version: 1.0.3
 * Description: Adds user defined texts. Write [adsense_id=1], call the getSimpleAdsense($id) function or use the widget.
 * Author: Tobias Nyholm
 * Author URI: http://www.tnyholm.se
 * License: GPLv3
 * Copyright: Tobias Nyholm 2010
 */

/*
    This file is part of Simple Adsense.

    Simple Adsense is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Simple Adsense is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Simple Adsense.  If not, see <http://www.gnu.org/licenses/>.

 */

include_once dirname(__FILE__)."/admin.php";

add_filter('the_content', 'saFilter');
/**
 * Verifies if a table of contents should be here. If so, then we add one.
 * @param String $content
 */
function saFilter($content){


	//reges to match if the "[toc=2]" exists
	$regex = '|(?:<p>)?\[adsense_id=(?:["'."'".'])?([1-9]+)(?:["'."'".'])? ?\](?:<br \/>)?(?:<\/p>)?|s';
	$match = preg_match_all($regex, $content, $matches);
	//echo ("<pre>".print_r($matches,true));

	//if [adsense_id] exists
	if ($match){
		$ads=get_option("simple-adsense");
		foreach($matches[1] as $key=>$adsenseid){

			if(isset($ads[$adsenseid])){
				$content = preg_replace($regex, stripslashes($ads[$adsenseid]), $content,1);
			}
				
		}
	}
	return $content;
}

/**
 * returns the adsense code you want
 * @param unknown_type $adsenseid
 */
function getSimpleAdsense($adsenseid){
	$ads=get_option("simple-adsense");
	
	if(isset($ads[$adsenseid]) && strlen($ads[$adsenseid])>2){
		return stripslashes($ads[$adsenseid]);
	}
		
	return false;

}


/**
 *
 * @since 2.8.0
 */
class simple_adsense_widget extends WP_Widget {

	/**
	 * inti the widget.
	 */
	function simple_adsense_widget() {
		$widget_ops = array('classname' => 'simple_adsense_widget', 'description' =>  'Put your adsese to the sidebar' );
		$this->WP_Widget('simple_adsense_widget', 'Simple Adsense widget', $widget_ops);
	}

	/**
	 * Echo the widget
	 * @param unknown_type $args
	 * @param unknown_type $instance
	 */
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
		
		$ad=getSimpleAdsense($instance['adsenseid']);
		
		if($ad!==false){
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo "<div id='simple_adsense_widget'>$ad</div>";
		echo $after_widget;
		}
	}

	/**
	 * ADMIN: update the widget
	 * @param unknown_type $new_instance
	 * @param unknown_type $old_instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['adsenseid'] = strip_tags($new_instance['adsenseid']);
		return $instance;
	}

	/**
	 * ADMIN: This is the widget form
	 * @param unknown_type $instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','adsenseid' => '' ) );
		$title = strip_tags($instance['title']);
		$adsenseid= strip_tags($instance['adsenseid']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: (optional)</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('adsenseid'); ?>">Adsense id:</label>
		<select class="widefat" id="<?php echo $this->get_field_id('adsenseid'); ?>" name="<?php echo $this->get_field_name('adsenseid'); ?>">
		<?php for($i=1;$i<=6;$i++):?>
			<option value="<?php echo $i?>" <?php if(esc_attr($adsenseid)==$i) echo "selected"?>><?php echo $i;?></option>
		<?php endfor;?>
		</select>
		</p>
<?php
	}
}
//register widget
add_action('widgets_init', create_function('','return register_widget("simple_adsense_widget");'));


function sa_install(){
	add_option("simple-adsense", array(),'','yes');
}

register_activation_hook(__FILE__,'sa_install');