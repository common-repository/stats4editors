<?php
/*
	Plugin Name: Starts for editors
	Plugin URI: http://scott-herbert.com
	Description: A simple and secure starts page that can be viewed by
editor and above
	Author: Scott Herbert
	Author URI: http://scott-herbert.com
	Version: 0.3
 */

 /*  Copyright 2013 Scott Herbert  (email : scott.a.herbert@googlemail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301
USA
*/

global $SH_SWS_data;
global $wpdb;
class  SH_SWS_data_store{
   public $tableName = "";
   public $version = 1;

  function __construct() {
	global $wpdb;
	$this->tableName = $wpdb->prefix . "SWS_Visitor_count";
  }
}
$SH_SWS_data = new SH_SWS_data_store();


function SH_SWS_install(){
	global $SH_SWS_data;
	global $wpdb;

   $sql = "CREATE TABLE ".$SH_SWS_data->tableName." (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  nonce text NOT NULL,
  ip VARCHAR(16),
  UA text DEFAULT '' NOT NULL,
  dt text DEFAULT '' NOT NULL,
  width INTEGER DEFAULT 0 NOT NULL,
  height INTEGER DEFAULT 0 NOT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );

}

register_activation_hook( __FILE__, 'SH_SWS_install' );


function add_footer_JS() {
    /*
		Add the JavaScript to detect the screen height/width and colour
depth
		push the nonce, user agent and ip address into the db
(sanitising all first)
	*/
	global $wpdb;
	global $SH_SWS_data;
	$nounce = wp_create_nonce('SH_SWS_stats');
	$ip = $_SERVER["REMOTE_ADDR"];
	$userAgent = $_SERVER['HTTP_USER_AGENT'];

	$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM " .
$SH_SWS_data->tableName . " WHERE nounce = ".$nounce );
	if($user_count == 0){

		$wpdb->insert(
			$SH_SWS_data->tableName,
			array( 'nonce' => $nounce, 'ip' => $ip, 'UA' =>
$userAgent, 'dt' => date('m/d/Y h:i:s a', time()) ),
			array( '%s', '%s', '%s', '%s' )
		);
	} else {
		$wpdb->update(
			$SH_SWS_data->tableName,
			array( 'ip' => $ip, 'UA' => $userAgent ),
			array( 'nonce' => $nounce),
			array( '%s', '%s')
			);
	}
		?>
		<script  type='text/javascript'>
		<!--
		// When the document loads do everything inside here ...
		jQuery(document).ready(function(){
			jQuery.ajax({
				type: "post",url: "admin-ajax.php",data: {
action: 		'SHSWSprams',

		_ajax_nonce: 	'<?php echo $nonce; ?>'

		width:			$(window).width(),

		height:			$(window).height(),

		depth:			screen.colorDepth

		},
			}); //close jQuery.ajax(
		})
		</script>
<?php
}
add_action('wp_footer', 'add_footer_JS');


function SH_SWS_prams() {
	/**
		This function is triggered when the by the Ajax code above
			TODO :-
				Retrieve (unbundle) the screen size
				Post the data to the database (
	*/
$nonce = $_REQUEST['_ajax_nonce'];
if ( ! wp_verify_nonce( $nonce, 'SH_SWS_stats' ) ) {

     die( 'Il peut être interdit d\'interdire. mais il vous est interdit de
poster avec un nonce cassé' );

} else {

	/*** the nonce isn't corrupted */

	/*** So add the data DB  N.B. It's sanatized by wp, and if we only
list ip addresses it won't
			Show unless their is a corrosponding half */
	global $wpdb;
	global $SH_SWS_data;
	$width = $_REQUEST['width'];
	$height = $_REQUEST['height'];
	$depth = $_REQUEST['depth'];

	$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM " .
$SH_SWS_data->tableName . " WHERE nounce = ".$nonce );
	if($user_count == 0){
		$wpdb->insert(
			$SH_SWS_data->tableName,
			array( 'nonce' => $nonce, 'width' => $width , 'height' =>
$height, 'depth' => $depth ),
			array( '%s', '%d', '%d', '%d' )
		);
	} else {
		$wpdb->update(
			$SH_SWS_data->tableName,
			array( 'width' => $width , 'height' => $height, 'depth'
=> $depth ),
			array( 'nonce' => $nonce),
			array( '%d', '%d', '%d')
			);
	}

}


}

add_action( 'wp_ajax_SHSWSprams', 'SH_SWS_prams' );


/***********************************************************************
 *
 * OK lots data in the DB so below is the admin (editor and above) panel
showing the stats
 */

add_action( 'admin_bar_menu', 'toolbar_link_to_mypage', 999 );

function toolbar_link_to_mypage( $wp_admin_bar ) {

	global $wpdb;
	global $SH_SWS_data;
	$page_views = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName);
	$visitor_count = $wpdb->get_var( "SELECT COUNT(DISTINCT ip) FROM
".$SH_SWS_data->tableName);
	$IE_Users = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName." WHERE UA LIKE %MSIE%");
	$FF_Users = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName." WHERE UA LIKE %Firefox%");
	$Chrome_Users = $wpdb->get_var( "SELECT COUNT(*) FROM
".$SH_SWS_data->tableName." WHERE UA LIKE %Chrome%");

	$IE_Users = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName." WHERE UA LIKE '%MSIE%'");
	$FF_Users = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName." WHERE UA LIKE '%Firefox%'");
	$Chrome_Users = $wpdb->get_var( "SELECT COUNT(*) FROM
".$SH_SWS_data->tableName." WHERE UA LIKE '%Chrome%'");
	$Bot_Users = $wpdb->get_var( "SELECT COUNT(*) FROM ".$SH_SWS_data->
tableName." WHERE UA LIKE '%bot%'");
	$Other_Users = $page_views - ($IE_Users + $FF_Users + $Chrome_Users +
$Bot_Users);

	$args = array(
		'id'    => 'counter',
		'title'  => $visitor_count . ' Unique visitors and ' .
$page_views .' Page views',
		'href' => '#',
		'aria-haspopup' => 'true',
		'meta' => array( 'html' => '<div id="SH_SWS_message_box"
style="overflow:visible;box-shadow: 10px 10px 5px
#888888;border-style:outset;border-width:5px;background:white;display:none;min-height:
 500px;" width="300" height="500" >
		<div id="SH_SWS_Chart">

		<iframe src="' . plugins_url( "chart.php" , __FILE__ ) .
'?IE=' . $IE_Users . '&Chrome=' . $Chrome_Users . '&FF=' . $FF_Users .
'&Other='.$Other_Users.'&bots='.$Bot_Users.'" height="450" width="250"
style="min-height: 500px;display: block;position: relative"></iframe>

		</div>
		</div>',
                'onclick' => 'if(document.getElementById
("SH_SWS_message_box").style.display == "block") {document.getElementById
("SH_SWS_message_box").style.display="none";} else {document.getElementById
("SH_SWS_message_box").style.display="block";}')
	);
	$wp_admin_bar->add_node( $args );
}
