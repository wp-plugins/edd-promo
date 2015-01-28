<div class="wrap">
	<h2><?php _e('EDD Promo Settings', 'edd-promo'); ?></h2>

	<form method="post" action="options.php" id="edd_promo_options_form">
		<?php settings_fields('edd_promo_full_options'); ?>
<input type="hidden" name="subject_email" value="<?php echo esc_attr( $this->options['subject_email']);?>">
<input type="hidden" name="template" value="<?php echo esc_attr( $this->options['template']);?>">
		<!-- Sender options -->
		<h3 class="edd_promo_title"><?php _e('Email Options', 'edd-promo'); ?></h3>
		<p style="margin-bottom: 0;"><?php _e('Set your own email subject.', 'edd-promo'); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="edd_promo_subject_email"><?php _e('Subject', 'edd-promo'); ?></label></th>
				<td><input type="text" id="edd_promo_subject_email" class="regular-text" name="edd_promo_options[subject_email]" value="<?php esc_attr_e($this->options['subject_email']); ?>" /></td>
		</table>

		<!-- Template -->
		<h3 class="edd_promo_title"><?php _e('HTML Template', 'edd-promo'); ?></h3>
		<p><?php _e('Edit the HTML template if you want to customize it.');?></p>
		<div id="edd_promo_template_container">
			<?php $this->template_editor() ?>
		</div>
		<!-- Preview -->
		<h3 class="edd_promo_title"><?php _e('Preview', 'edd-promo'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="edd_promo_preview_field"><?php _e('Send an email preview to', 'edd-promo'); ?></label>
				</th>
				<td>
					<input type="text" id="edd_promo_preview_field" name="admin" class="regular-text" value="<?php esc_attr_e(get_option('admin_email')); ?>" />
					<input type="submit" class="button" name="edd_preview" value="<?php _e('Send Preview', 'edd-promo'); ?>"/>
					<br /><span class="description"><?php _e('You must save your template before sending an email preview and/or promotion.', 'edd-promo'); ?></span>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="empty" class="button-primary" value="<?php _e('Save Changes', 'edd-promo') ?>" />
			<input type="submit" name="edd_promo" class="button-primary" value="<?php _e('Send Promo', 'edd-promo') ?>" />
		</p>
	</form>
	<br>
	<!-- Support -->
	<div id="edd_promo_support">
		<h3><?php _e('Support & bug report', 'edd-promo'); ?></h3>
		<p><?php printf(__('If you have any idea to improve this plugin or any bug to report, please email me at : <a href="%1$s">%2$s</a>', 'edd-promo'), 'mailto:eddpromo@cbfreeman.com?subject=[edd-promo-plugin]', 'eddpromo@cbfreeman.com'); ?></p>
			<?php $donation_link = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BAZNKCE6Q78PJ'; ?>
		<p><?php printf(__('You like this plugin ? You use it in a business context ? Please, consider a <a href="%s" target="_blank" rel="external">donation</a>.', 'eddpromo'), $donation_link ); ?></p>
	</div>
</div>