<?php 
/*
Plugin Name: OB Contact Form
Plugin URI: http://owebest.com
Description: OB Contact form is a simple contact form which works out of the box. Use shortcode on posts or pages to generate OB Contact Form.
Version: 1.0
Author: Owebest
Author URI: http://Owebest.com
*/

$db_version = '1.0';
	
	add_action('wp_enqueue_scripts', 'ob_style_front_data');
	
	function ob_style_front_data(){
		wp_enqueue_style( 'style_css', trailingslashit(plugin_dir_url(__FILE__)).'/style.css'); 
	}
	
	// active and deactive hooks with function
		
	add_action( 'wp_ajax_obfc_form', 'obfc_form' );
	add_action( 'wp_ajax_nopriv_obfc_form', 'obfc_form' );
	
	function obfc_form()
	{				
			if(isset($_POST['ob_squeeze_form']) && trim($_POST['ob_squeeze_form']) == 'k4342'){
				$data = array();
				if(isset($_POST['name'])){
					$name = sanitize_text_field(trim($_POST['name']));
				}
				if(isset($_POST['last_name'])){
					$lastname = sanitize_text_field(trim($_POST['last_name']));
				}
				if(isset($_POST['email'])){
						$email = sanitize_email(trim($_POST['email']));
				}
				if(isset($_POST['phone'])){
				
					$phone = intval(trim($_POST['phone']));
				}
				if(isset($_POST['ip'])){
				
					if($_POST['ip'] === filter_var($_POST['ip'], FILTER_VALIDATE_IP))
					{
						$ip = $_POST['ip'];
					}
				}
				if(isset($_POST['comment'])){
					$comment = esc_attr($_POST['comment']);
				}
				
				$data['name'] = $name;
				$data['last_name'] = $lastname;
				$data['email'] = $email;
				$data['phone'] = $phone;
				$data['comment'] = $comment;
				$data['ip'] = $ip;
				
				/* If anyone wants the data they can add their functions to this hook*/
				do_action('save_obcf',$data);
				$default_email = get_option('admin_email');
				$to = get_option('obcf_to_email',$default_email);
				$from = get_option('obcf_from_email',$default_email);
				$from_name = get_option('obcf_from_name','Wordpress');
				$subject = get_option('obcf_form_subject','New contact form submitted on your website.');
				$headers = 'From: '. $from_name .' <'. $from.'>';
				$message ="Name: " .$name. "\n";
				$message .="Last Name: ".$lastname." \n";
				$message .="Email: ".$email." \n";
				$message .="Phone: ".$phone." \n";
				$message .="Ip: ".$ip." \n";
				$message .="Message: ".esc_html($comment)." \n\n\n\n";	
				$message .="This email was sent from ".site_url();
				
					if(wp_mail($to, $subject, $message, $headers )){
							echo 1;
							wp_die();
					}
					else{
						echo 0;
						wp_die();
					}
			}
			
			
	}	
		function OB_general_info() 
	{		
		?>
		<div class="au_wrapper">
			<h2>OB Contact Form</h2>
			<br />
			<br />
			<p>
				Welcome to OB Contact Form.<br />
				OB Contact Form work out of the box. Just use the shortcode mentioned below to generate simple contact form on any post/page.<br /><br />
			</P>
			<p>
				Shortcode to generate the squeeze form</br>
				<code>
					[obcf_contact_form]
				</code>
			</p>
			<p>
				To view more of our plugins please <a href="#">click here</a>
				</br></br>
				Enjoy!!!
			</p>
		</div>
		<?php
	}
		add_shortcode( 'obcf_contact_form', 'obcf_shortcode' );
		function obcf_shortcode() 
		{
			?>
			<div id="mail-status" style="color:#f05227; font-size:18px; font-weight:400;"></div>
			<form action="" method="post" accept-charset="utf-8" class="subform" id="subscription" name="lp" action="<?php $_SERVER['PHP_SELF']; ?>">
				<fieldset>
					<div style="padding-top: 52px;">
						<input type="text"  name="obcf_name" id="obcf_name" style="color:#000;" placeholder="* Name" class="obcf_first_name_input">
						<input type="text"  name="obcf_lastname" id="obcf_lastname" style="color:#000;" placeholder="* Surname" class="obcf_last_name_input">
						<input type="text"  style="color:#000;" name="obcf_phone" id="obcf_phone" placeholder="Telephone/Skype" class="obcf_tel_input"><br>
						<input type="email" style="color:#000;" name="obcf_email" id="obcf_email" placeholder="* E-mail address" class="obcf_email_input">	<br><br>	
						<textarea name="obcf_comment" class="obcf_email_input" id="obcf_comment"></textarea>
						
						<?php do_action('save_obcf_captcha'); ?>
						
						<input type="hidden" name="ob_squeeze_form" id="ob_squeeze_form" value="k4342">
						<input type="hidden" id="ip" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"><br><br>
						
						<div class="submit-container">
							<input id="obcf_submit" class="submit-button obcf_submit_input" type="submit" name="obcf_submit" value="SUBMIT">
						</div>
						<div id="progress_submit"></div><br>
					</div>
				</fieldset>
					
			</form>
			<?php 
			echo '
			<script>
				jQuery("#subscription").submit(function() 
				{
					var x=document.forms["lp"]["obcf_name"].value;
					var y=document.forms["lp"]["obcf_lastname"].value;
					var z=document.forms["lp"]["obcf_email"].value;

					if (x==null || x=="") { alert("Please enter your first name."); document.forms["lp"]["obcf_name"].focus(); return false; }
					if (y==null || y=="") { alert("Please enter your surname."); document.forms["lp"]["obcf_lastname"].focus(); return false; }
					if (z==null || z=="") { alert("Please enter an e-mail address."); document.forms["lp"]["obcf_email"].focus(); return false; }

					var $name  = jQuery("#obcf_name").val();
					var $last_name  = jQuery("#obcf_lastname").val();
					var $email  = jQuery("#obcf_email").val();
					var $phone  = jQuery("#obcf_phone").val();
					var $comment  = jQuery("#obcf_comment").val();
					var $captcha  = true;
					
					var $ip  = jQuery("#ip").val();
					var $ob_squeeze_form  = jQuery("#ob_squeeze_form").val();
					var ajaxurl = "'. admin_url("admin-ajax.php").'";
					var success_message = "'.get_option("obcf_form_success","Thank you for contacting us.").'";
					var error_message = "'. get_option("obcf_form_error","Sorry there was a problem submitting your form. Please try again later").'";
					';
					$content = 'jQuery.ajax(
						{
						   contentType: "application/x-www-form-urlencoded;charset=utf-8",
						   type: "POST",
						   cache: false,
						   url: ajaxurl,
						   beforeSend:function(){
								jQuery("#progress_submit").html("loading...");
								jQuery("#obcf_submit").prop("disable",true);
						   },
						   
						   data: { name: $name, last_name: $last_name, email: $email, phone: $phone, ip: $ip, comment : $comment, ob_squeeze_form: $ob_squeeze_form , action: "obfc_form"},
						   success: function(resp)
							{
								if(resp == 1 || resp == "1" )
								{
									
									jQuery("#mail-status").html(success_message);
									jQuery("#subscription").hide();
									//alert("You will soon receive an e-mail with the confirmation link");
									jQuery("#progress_submit").html("");
								}
								else
								{
									alert(error_message);
								}
							}
						});
					';
					
					$content =  apply_filters('obcf_captcha_change',$content);
					
					echo $content;
					echo '
					return false;
					});
					</script>
			';
		}
		
	
	function obcf_settings(){
		if(isset($_POST['obcf_option_submit'])){
			if (!isset( $_POST['obcf_save_options_nonce'] ) || ! wp_verify_nonce( $_POST['obcf_save_options_nonce'], 'obcf_save_options_contact' )) {
				print 'Sorry, This is not a valid request.';
				exit;
			}
			else{
				if(isset($_POST['obcf_from_email'])){
					$chk_content = get_option('obcf_from_email');
					update_option('obcf_from_email',sanitize_email($_POST['obcf_from_email']));
				}
				if(isset($_POST['obcf_from_name'])){
					$chk_content = get_option('obcf_from_name');
					update_option('obcf_from_name',sanitize_text_field($_POST['obcf_from_name']));
				}
				if(isset($_POST['obcf_form_subject'])){
					update_option('obcf_form_subject',sanitize_text_field($_POST['obcf_form_subject']));
				}
				if(isset($_POST['obcf_to_email'])){
					update_option('obcf_to_email',sanitize_email($_POST['obcf_to_email']));
				}
				if(isset($_POST['obcf_form_error'])){
					update_option('obcf_form_error',sanitize_text_field($_POST['obcf_form_error']));
				}
				if(isset($_POST['obcf_form_success'])){
					update_option('obcf_form_success',sanitize_text_field($_POST['obcf_form_success']));
				}	
				do_action('obcf_save_options',$_POST);
			
				$message = '<div class="updated notice notice-success is-dismissible below-h2" id="message"><p> Options Saved.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';	
			}
		}
			
			?>
		
		<div class="option-wrapper">
			<h2>OB Contact Form Options </h2><br />
			<?php
				
					if(!empty($message))
						echo $message;
			?>
			<form action="" method="post">
				<table>
					<tr>
						<td>To Email:</td>
						<td><input type="email" name="obcf_to_email" value="<?php echo get_option('obcf_to_email','demo@yourdomain.com'); ?>"/></td>
					</tr>
					<tr>
						<td>From Email:</td>
						<td><input type="email" name="obcf_from_email" value="<?php echo get_option('obcf_from_email','demo@yourdomain.com'); ?>"/></td>
					</tr>
					<tr>
						<td>From Name</td>
						<td><input type="text" name="obcf_from_name" value="<?php echo get_option('obcf_from_name','Wordpress'); ?>" /></td>
					</tr>
					<tr>
						<td>Subject </td>
						<td><input type="text" name="obcf_form_subject" value="<?php echo get_option('obcf_form_subject','New contact form submitted on your website'); ?>" /></td>
					</tr>
					<tr>
						<td>
							Success Message
						</td>
						<td><input type="text" name="obcf_form_success" value="<?php echo get_option('obcf_form_success','Thank you for contacting us. We will be in touch'); ?>" /></td>
					</tr>
					<tr>
						<td>
							Error Message
						</td>
						<td><input type="text" name="obcf_form_error" value="<?php echo get_option('obcf_form_error','Sorry there was an error submitting your form. Please try again later.'); ?>" /></td>
					</tr>
					<?php 
						echo apply_filters('obcf_add_options','');
					?>
					<tr>
						<td>Save Changes</td>
						<td>
							<?php
								wp_nonce_field( 'obcf_save_options_contact', 'obcf_save_options_nonce' );
							?>
							<input id="obcf_option_submit" type="submit" name="obcf_option_submit" class="btn" value="Save Settings" />
						</td>
					</tr>
				</table>
			</form>
		</div>

	<?php		
	}
		
add_action( 'admin_menu', 'OB_submenu_page' );
	
	function OB_submenu_page() {
		add_menu_page('OB Contact Form', 'OB Contact Form', 'administrator', 'OB-contact-us-form', 'OB_general_info', 'dashicons-admin-generic');
		add_submenu_page('OB-contact-us-form', 'Settings - OB Contact Form', 'Settings', 'administrator', 'settings', 'obcf_settings'); 
	}
?>
