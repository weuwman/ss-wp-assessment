<?php
/**
 * @package My_Contact_Form
 * @version 1.0
 */
/*
Plugin Name: My Contact Form
Plugin URI: https://softwareseni.com
Description: For SS WP Assessment
Author: Muhammad Arif
Version: 1.0
Author URI: https://softwareseni.com
*/

//Import classes
require_once (dirname(__FILE__)."/inc/wp_list_table.php");

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//table prefix
$table_prefix = "mycf_";

/**
 * create tables when plugin activate
 */
function mycf_activate()
{
	global $wpdb;

	// create/update database
	require_once (dirname(__FILE__)."/database.php");
}
register_activation_hook(__FILE__, 'bd_ppt_activate');


//Menu and admin pages
function mycf_add_options_page(){
	add_menu_page('My Contact Form', 'Contact Form', 8, 'mycf-page', 'mycf_get_all_data');
	// add_submenu_page('mycf-page', 'Add New Form', 'Add New Package', 'manage_options', 'mycf-add-page' , 'mycf_add_package');
	// add_submenu_page('mycf-page', 'Settings', 'Settings', 'manage_options', 'mycf-settings', 'mycf_settings');
}
add_action('admin_menu', 'mycf_add_options_page');

//Show All Packages
function mycf_get_all_data()
{
    global $wpdb;
    $table_data 	= new MyCF_List_Table();
    $table_data->prepare_items();

    $message = '';
    if ('delete' === $table_data->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
	<div class="wrap">

	    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	    <h2>Contacts
	    </h2>
	    <?php echo $message; ?>
	    <form id="mycf-contact-table" method="GET">
	        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	        <?php $table_data->display() ?>
	    </form>

	</div>
	<?php
}


function mycf_process_form(){
		if(isset($_GET['process_form']))
	{

		$name 		= $_GET['name'];
		$email 		= $_GET['email'];
		$message	= $_GET['message'];

		if($name && $email)
		{
			$data = array(
				"name"				=> $name,
				"email"				=> $email,
				"created"			=> time()
			);

			$save_data = $wpdb->insert(
				$table_prefix.'data',
				$data
			);
			$issave = true;
			if($issave)
			{
				$to = get_option( 'admin_email' );
				$fromname 	= $name;
				$fromemail 	= $email;
				$notes 		= $message;
				$date       = date("d-m-Y");
				$subject = "New Contact Form Submission";
				$message = "New contact form submission. Details below:";

				$message .= "<br><br> {$date} <br><br>
				Name  : ".$fromname."<br>
				Email : ".$fromemail."<br>
				Phone : ".$fromphone."<br>
				Comments : ".$notes;

				// a random hash will be necessary to send mixed content
				$separator = md5(time());

				// carriage return type (we use a PHP end of line constant)
				$eol = PHP_EOL;

				// main header
				$headers  = "From: ".$fromname." <".$fromemail.">".$eol;
				$headers .= "MIME-Version: 1.0".$eol;
				$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

				// no more headers after this, we start the body! //

				$body = "--".$separator.$eol;
				$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
				// $body .= "This is a MIME encoded message.".$eol;

				// message
				$body .= "--".$separator.$eol;
				$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
				$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
				$body .= $message.$eol;

				//send message
				mail($to, $subject, $body, $headers);
			}
		}
		$issave = false;
	}
}
add_action('init','mycf_process_form');


function mycf_form_display( $atts )
{
	global $wp, $wpdb, $table_prefix;
	$form_title = $atts['title'] ? $atts['title'] : "My Contact Form";
	?>
	<div class="mycf_form_display">
		<div class="mycf_form_header">
			<?php echo $form_title; ?>
		</div>
		<hr>
		<div class="mycf_form_content">
			<?php 
			if($issave){
				echo '<div class="alert">Thanks for contacting us.</div>';
			} 
			?>
			<form action="<?php echo home_url( $wp->request ); ?>/?process_form" method="get">
				<label>Name:</label>
				<input type="text" name="name" placeholder="Name" required="true"><br/>
				<label>Email:</label> 
				<input type="email" name="email" placeholder="Email" required="true"><br/>
				<label>Message:</label>
				<textarea name="message" placeholder="Please enter your message here" required="true"></textarea><br/>
				<button type="submit">Submit</button>
			</form>
		</div>
	</div>
	<?php
}
add_shortcode( 'mycf_display_form', 'mycf_form_display' );