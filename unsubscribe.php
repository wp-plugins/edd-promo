<?php

global $wpdb;
global $current_user;
$id = $current_user->ID;
$useremail = $current_user->user_email;
  
if(isset($_POST['eddpromo-unsub'])) {
  if(isset($_POST['sender']))
  $sender = sanitize_text_field($_POST['sender']);
  $wpdb->query(
	"UPDATE {$wpdb->prefix}edd_customers SET subscriber ='Z' WHERE email LIKE '%$sender%' LIMIT 1 ");
	 
	 echo "Your email address at $sender has been unsubscribed!<br>";
}
?>
<h3><?php _e('Enter your email address below to unsubscribe.', 'edd-promo');?></h3>
<form method="post" action="" id="edd-promo-form">
		<!-- Sender options -->
		<table class="form-table">
<tr valign="top">
				<th scope="row">
				<label for="subject"><?php _e('Email:', 'edd-promo'); ?></label></th>
				<td>
				<input type="text" name="sender" value='<?php echo esc_attr($useremail);?>'>

			
			<input type="submit" name="eddpromo-unsub" class="button-primary" value="<?php _e('Submit', 'edd-promo') ?>" />
		</td>
		</tr>
		</table>
	</form>

