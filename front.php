<?php
 /**
  * Ajout du short code
  * Format du shortcode : [contact-lite]
  * @return le formulaire
  *
  * @see https://developer.wordpress.org/reference/functions/add_shortcode/
  */
add_shortcode('contact-lite', 'cl_add_front');
function cl_add_front() {
		wp_enqueue_style( 'cl_style', plugin_dir_url(dirname( __FILE__) ) . 'contact-lite/assets/css/contact-lite.css' );
		wp_enqueue_script( 'cl_script', plugin_dir_url(dirname( __FILE__) ) . 'contact-lite/assets/js/contact-lite.js');
		wp_localize_script('cl_script', 'cl_ajaxUrl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script('cl_script', 'cl_home_url', home_url() );

		//wp_enqueue_script( 'googleCaptcha', 'https://www.google.com/recaptcha/api.js');
		?>
		<div id="contact-lite-container">
			<form id="form-contact" name="form-contact" action="<?php the_permalink();?>" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="formContact">
				<?php wp_nonce_field('nonceformContact', 'nonceformContact'); ?>
				<div class="content-form">
					
					<label for="name">Votre nom :</label>
					<input type="text" name="name" id="name" required="required" placeholder="nom*">

					<label for="firstname">Votre prénom :</label>
					<input type="text" name="firstname" id="firstname" required="required" placeholder="prénom*">

					<label for="email">Votre email :</label>
					<input type="email" name="email" id="email" required="required" placeholder="email*">

					<label for="phone">Votre numéro de téléphone :</label>
					<input type="tel" name="phone" id="phone" required="required" placeholder="téléphone*" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$">

					<!--<label for="file">Envoyer un fichier (image ou pdf) :</label>
					<input type="file" name="file" id="file" placeholder="votre fichier" accept="image/*, application/pdf">-->

					<label for="comment">Votre message :</label>
					<textarea name="comment" id="comment" placeholder="message" required="required"></textarea>

					<div class="container-button">
						<!--<div class="g-recaptcha" data-callback="cl_recaptchaCallback" data-sitekey=""></div>-->
						<!--<button type="submit" class="button" disabled id="submitBtn">Envoyer</button>-->
						<button type="submit" class="button" id="cl_submitBtn">Envoyer</button>
					</div>
				</div>
			</form>
		</div>
		<div class="modal" id="cl_modal">
			<div class="modal-header">
				<h2>Formulaire de contact</h2>
			</div>
			<div class="modal-content">
			</div>
			<div class="modal-footer">
				<p>
					<a href="<?php echo home_url();?>"><button class="button" onclick="cl_closeModal()">
						&times; Fermer
					</button></a>
				</p>
			</div>
		</div>
	<?php
}


/*
* traitement du post du form de Contact
* enregistrement des values dans le custom post type
*/
add_action( 'wp_ajax_formContact', 'cl_formContact' );
add_action( 'wp_ajax_nopriv_formContact', 'cl_formContact' );
function cl_formContact(){
	if (wp_verify_nonce($_POST['nonceformContact'], 'nonceformContact')) {
		global $wpdb;

		$upload_file_text = "";
		if(isset($_FILES['file'])){
			$attachments = array();
			$maintenant = date("d-m-Y_H:i:s");
			$upload_dir   = wp_upload_dir();
			$uploaddirimg = $upload_dir['basedir'].'/img-form/';
			$uploadfile = $uploaddirimg . $maintenant . '-'.basename($_FILES['file']['name']);
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
				array_push($attachments, $uploadfile);
				$upload_file_text = "Fichier : ".$upload_dir['baseurl'].'/img-form/' . $maintenant . '-'.basename($_FILES['file']['name']);
			}
		}

		$to = sanitize_email( $_POST['showroom'] );
		$subject = CL_SUBJECT;
		$body = 'Nom : '.sanitize_text_field($_POST['name'])."\r\n";
		$body .= 'Prénom : '.sanitize_text_field($_POST['firstname'])."\r\n";
		$body .= 'email : '.sanitize_text_field($_POST['email'])."\r\n";
		$body .= 'Téléphone : '.sanitize_text_field($_POST['phone'])."\r\n";
		$body .= 'Commentaire : '.sanitize_textarea_field($_POST['comment'])."\r\n";
		$body .= $upload_file_text;
		$headers[] = 'From: '.get_bloginfo('name').' <'. CL_EMAIL_FROM .'>';
		wp_mail( $to, $subject, $body, $headers, $attachments);

        $post['post_type']   = 'contact-lite';
        $post['post_status'] = 'publish';
		$post['post_title'] = sanitize_text_field($_POST['firstname']).' '.sanitize_text_field($_POST['name']);
		$post['post_content'] = $body;
		wp_insert_post( $post, true );


		$body = 'Bonjour,
Nous avons bien reçu votre demande d’informations.
Votre requête est en cours d’acheminement vers le ou la chargé(e) de projet qui a travaillé avec vous sur votre précédent projet .Il ou elle reviendra vers vous dès que possible dans les 48H.

Bien cordialement,

La Compagnie des Ateliers';
$headers[] = 'From: '.get_bloginfo('name').' <'. CL_EMAIL_FROM .'>';
wp_mail( sanitize_text_field($_POST['email']), "Votre demande d'informations", $body, $headers);
	}
	die();
}
