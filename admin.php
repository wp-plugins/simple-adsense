<?php
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

function sa_admin_menu() {
	$page=array();
	
  	//Add some submenus
  	//parent, title, link, rights, url, function
  	$page[]=add_submenu_page('options-general.php', 'Simple adsense', 'Simple adsense','manage_options','sa-options', 'sa_admin_options');

  	foreach($page as $p)
  		add_action('admin_print_styles-' . $p, 'sa_admin_styles');
}
add_action('admin_menu', 'sa_admin_menu');
add_action('admin_init', 'sa_admin_init');


function sa_admin_init(){
	wp_register_style('saStyleAdmin', WP_CONTENT_URL.'/plugins/simple-adsense/admin.css');

	
}


function sa_admin_styles(){
	wp_enqueue_style('saStyleAdmin');
}

function sa_admin_options(){
	echo "<div id='sa_admin'><h1>Simple adsense</h1>";
	
	$adid=array(1,2,3,4,5,6);
	
	$texts=get_option('simple-adsense');
	/*
	 * handle post
	 */
	if(sa_post("do")=="update"){
		foreach ($adid as $id)
			$texts[$id]=sa_post($id);
		update_option('simple-adsense',$texts);
		echo "<div class='success'>Saved!</div>";
	}
	
	
	/*
	 * Print html
	 */
	?>
	<form action="" method="POST">
	<input type="hidden" name="do" value="update" />
	<table>
		<tr><td>[adsense_id="1"]</td><td>[adsense_id="2"]</td></tr>
		<tr><td><textarea name="1"><?php echo sa_getValue($texts,'1');?></textarea></td>
		<td><textarea name="2"><?php echo sa_getValue($texts,'2');?></textarea></td></tr>
		<tr><td>[adsense_id="3"]</td><td>[adsense_id="4"]</td></tr>
		<tr><td><textarea name="3"><?php echo sa_getValue($texts,'3');?></textarea></td>
		<td><textarea name="4"><?php echo sa_getValue($texts,'4');?></textarea></td></tr>
		<tr><td>[adsense_id="5"]</td><td>[adsense_id="6"]</td></tr>
		<tr><td><textarea name="5"><?php echo sa_getValue($texts,'5');?></textarea></td>
		<td><textarea name="6"><?php echo sa_getValue($texts,'6');?></textarea></td></tr>
	</table>
	<input type="submit" value="Save" />
	
	</form>
	
	</div><!-- end sa_admin -->
	<?php 
}


function sa_getValue(array &$arr,$key){
	if(isset($arr[$key]))
		return stripslashes($arr[$key]);
	return "";
}

/**
 * Use sa_post() as value in a input-tag. This will return "" instead of the error you get with $_POST
 * @param String $str
 */
function sa_post($str){
	if(isset($_POST[$str]))
		return $_POST[$str];
	return "";
}

