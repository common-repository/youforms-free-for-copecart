<?php

/*
Copyright 2020 itservicejung.de - All Rights Reserved
*/


class YouFormsITJ_Meta_Post_Editor {
	protected static $initd = false;
	public static $permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	/**
	 * init pluggable functions.
	 *
	 * @return  void
	 */
	public static function init() {

		// Do nothing if pluggable functions already initd.
		if ( self::$initd ) {
			return;
		}
		add_action( 'add_meta_boxes', [ __CLASS__, 'get_metabox' ] );
		add_action( 'save_post', [ __CLASS__, 'Cope_Form_save_events_meta' ], 1, 2 );
		add_action( 'edit_form_after_title', [ __CLASS__, 'Cope_Form_JS_CSS_header' ] );
		add_action( 'edit_form_after_title',  [ __CLASS__, 'ITJ_form_WorkSpace'] );

		//Tags
		add_shortcode( 'form_copecart', [ __CLASS__,'ITJ_form_copecart_Tag'] );


		self::$initd = true;
	}



	public static function generate_string($input, $strength = 16) {
	    $input_length = strlen($input);
	    $random_string = '';
	    for($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }

	    return $random_string;
	}

	public static function isStable() {

	  $options = [
	    'body' => array('is'=>'0'),
	    'timeout'     => 500,
	    'redirection' => 5,
	    'blocking'    => true,
	    'httpversion' => '1.0',
	    'sslverify'   => false,
	    'data_format' => 'body',
	];
	  $response = wp_remote_post( 'https://api.itservicejung.de/youForms.php', $options);
	  $body = wp_remote_retrieve_body($response);
	  $obj = json_decode($body);

		if (!empty($obj)) {
		  if (boolval($obj->kek)) {
		    return true;
		  } else {
		    deactivate_plugins( YouFormsITJ_PLUGIN_BASENAME );
				return false;
		  }
		}
	}


	public static function ITJ_form_copecart_Tag($atts) {
		//Buffer the Content
		ob_start();
		//[form_copecart id="202"]
		$id = esc_html($atts["id"]);

		$formid = get_post_meta( $id, 'formid', true ) ? esc_html(get_post_meta( $id, 'formid', true )) : false;
		
		if(!$formid) {
			//Generate unique form id
			$formid = esc_html(YouFormsITJ_Meta_Post_Editor::generate_string(self::$permitted_chars, 5));
		}

		wp_enqueue_style( 'itj_youforms_css', YouFormsITJ_CSS.'youforms.css', array(), YouFormsITJ_VERSION);
		wp_enqueue_script('itj_youforms_js', YouFormsITJ_JS.'youforms.js', array('jquery'), YouFormsITJ_VERSION);
		wp_enqueue_script( 'itj_youforms_bootstrapjs', YouFormsITJ_JS_LIBS.'bootstrap.min.js', array('jquery'), YouFormsITJ_VERSION);

		//echo "<input type='hidden' id='postid' value='".$formid."' />";
		echo "<input type='hidden' id='postid' value='".esc_html($formid)."' />";

		$hidebuttontemp = get_post_meta( $id, 'hidebtntemplate', true ) ? get_post_meta($id, 'hidebtntemplate', true ) : "0";
		
		$allowedbtnhtml = ["div" => ["id" => [], "class" => [], "style" => [], "aria-disabled" => [], "data-element-theme" => [], "data-google-font" => [], "data-elbuttontype" => []], "center" => [], "a" => ["id" => [], "data-target" => [], "data-toggle" => [], "data-modal" => [], "class" => [],"style" => [], "rel" => []], "span" => ["class" => [], "id" => [], "style" => []]];	
		$allowedModalhtml = [ "path"=> ["fill-rule" => [], "d" => []],"svg" => ["width" => [], "height" => [], "viewBox" => [], "class" => [],"fill" => [], "xmlns" => []], "a" => ["id" => [], "class" => [], "style" => [], "rel" => []],"div" => ["id" => [],"class" => [],"tabindex" => [],"role" => [],"aria-hidden" => [],"style" => []], "center" => [],"input" => ["type" => [], "value" => [], "class" => [], "style" => [], "id" => [],"placeholder" => []],"select" => ["name" => [], "class" => [],"data-type" => [],"style" => [], "required" => [], "id" => []], "option" => ["selected" => [], "value" => []], "span" => ["class" => [], "id" => [], "style" => []]];

		if($hidebuttontemp === "0") {
			echo wp_kses(YouFormsITJ_Meta_Post_Editor::ITJ_ReplaceAllTagsInBtnTemplate($id, $formid), $allowedbtnhtml);
			echo wp_kses(YouFormsITJ_Meta_Post_Editor::ITJ_ReplaceAllTagsInModalTemplate($id, $formid), $allowedModalhtml); 
		} else {
			echo wp_kses(YouFormsITJ_Meta_Post_Editor::ITJ_ReplaceAllTagsInModalTemplate($id, $formid), $allowedModalhtml); 
		}

		//Clean Content
		$content = ob_get_clean();
		return $content;
	}


	public static function ITJ_ReplaceAllTagsInModalTemplate($postid, $formid, $editmode = false) {
		$file = YouFormsITJ_TEMPLATE.'modalview.html';

		if (file_exists($file)) {
			$template = file_get_contents($file);
		
			$template = str_replace("{POSTID}", $formid, $template);

		
			$template = $editmode ? str_replace("{tag_modal_hidden}", "", $template) : str_replace("{tag_modal_hidden}", "display: none;", $template);
			

			//static text
			$template = str_replace("{tag_modal_fname}", __("First name",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_flast}", __("Last name",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_email}", __("your@example.com",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_phone}", __("Phone",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_adress}", __("Address",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_hausnr}", __("House Nr.",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_city}", __("City",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_plz}", __("Zip code",'Cope_Formlang'), $template);
			$template = str_replace("{tag_modal_land}", __("Country",'Cope_Formlang'), $template);


			$headertext = get_post_meta( $postid, 'headertext', true ) ? get_post_meta( $postid, 'headertext', true ) :__("Subscribe NOW! to the premium Wordpress Plugin!",'Cope_Formlang');
			$template = str_replace("{tag_modal_headertext}", esc_attr(esc_html($headertext)), $template);

			$headersubtext = get_post_meta( $postid, 'headersubtext', true ) ? get_post_meta( $postid, 'headersubtext', true ) :__("Yes! i will subscribe to the premium version!",'Cope_Formlang');
			$template = str_replace("{tag_modal_headersubtext}", esc_attr(esc_html($headersubtext)), $template);


			$headerbgcolor = get_post_meta( $postid, 'headerbgcolor', true ) ? get_post_meta( $postid, 'headerbgcolor', true ) : "";

			if(!$headerbgcolor === "#4c8a22") {
				$template = str_replace("{tag_modal_headerbgcolor}", esc_attr(esc_html($headerbgcolor)), $template);
			} else {
				$template = str_replace("{tag_modal_headerbgcolor}", "s", $template);
			}
			

			$headerfontcolor = get_post_meta( $postid, 'headerfontcolor', true ) ? get_post_meta( $postid, 'headerfontcolor', true ) : "white";
			$template = str_replace("{tag_modal_headerfontcolor}", esc_attr(esc_html($headerfontcolor)), $template);
			
			$buttonheadertext = get_post_meta( $postid, 'buttonheadertext', true ) ? get_post_meta( $postid, 'buttonheadertext', true ) :__("Continue Checkout",'Cope_Formlang');
			$template = str_replace("{tag_modal_buttonheadertext}", esc_attr(esc_html($buttonheadertext)), $template);
			
			$buttonheadersubtext = get_post_meta( $postid, 'buttonheadersubtext', true ) ? get_post_meta( $postid, 'buttonheadersubtext', true ) :__("Checkout will be done by CopeCart!",'Cope_Formlang');
			$template = str_replace("{tag_modal_buttonheadersubtext}", esc_attr(esc_html($buttonheadersubtext)), $template);

			$buybuttonbgcolor = get_post_meta( $postid, 'buybuttonbgcolor', true ) ? get_post_meta( $postid, 'buybuttonbgcolor', true ) : "#2470ff";
			$template = str_replace("{tag_modal_buybuttonbgcolor}", esc_attr(esc_html($buybuttonbgcolor)), $template);			

			$buybuttonfontcolor = get_post_meta( $postid, 'buybuttonfontcolor', true ) ? get_post_meta( $postid, 'buybuttonfontcolor', true ) : "white";
			$template = str_replace("{tag_modal_buybuttonfontcolor}", esc_attr(esc_html($buybuttonfontcolor)), $template);

			$copecartUrl = get_post_meta( $postid, 'copecarturl', true ) ? get_post_meta( $postid, 'copecarturl', true ) : "#";
			$template = str_replace("{tag_modal_copecartUrl}", esc_attr(esc_html($copecartUrl)), $template);
			
			
			return  $template;
		} else {
			echo __("modal template not exists", 'Cope_Formlang');
		}
	}

	public static function ITJ_ReplaceAllTagsInBtnTemplate($postid, $formid) {
		$file = YouFormsITJ_TEMPLATE.'button.html';

		if (file_exists($file)) {
			$template = file_get_contents($file);
		
			$template = str_replace("{POSTID}", $formid, $template);

			$buttonclicktext = get_post_meta( $postid, 'buttonclicktext', true ) ? get_post_meta( $postid, 'buttonclicktext', true ) :__("Subscribe NOW! to the premium Wordpress Plugin!",'Cope_Formlang');
			$template = str_replace("{tag_btn_uebersch}", esc_attr(esc_html($buttonclicktext)), $template);
			
			$buttonclicksubtext = get_post_meta( $postid, 'buttonclicksubtext', true ) ? get_post_meta( $postid, 'buttonclicksubtext', true ) : __("Create checkouts very easy, with the premium CopCart Form!",'Cope_Formlang');
			$template = str_replace("{tag_btn_unteruebersch}", esc_attr(esc_html($buttonclicksubtext)), $template);
			
			$buttontempbgcolor = get_post_meta( $postid, 'buttontempbgcolor', true ) ? get_post_meta( $postid, 'buttontempbgcolor', true ) : "#2470ff" ;
			$template = str_replace("{tag_btn_buttontempbgcolor}", esc_attr(esc_html($buttontempbgcolor)), $template);

			$buttonfontcolor = get_post_meta( $postid, 'buttonfontcolor', true ) ? get_post_meta( $postid, 'buttonfontcolor', true ) : "rgb(255, 255, 255)" ;
			$template = str_replace("{tag_btn_buttonfontcolor}", esc_attr(esc_html($buttonfontcolor)), $template);
			
			
			return  $template;
		} else {
			echo __("btn template not exists", 'Cope_Formlang');
		}
	}

	public static function ITJ_TagReplace($tagname, $tagvalue, $filepath) {

		$file = $filepath;

			if (file_exists($file)) {
				$file = file_get_contents($file);
				return str_replace($tagname, $tagvalue, $file);

			} else {
				echo __("file not exists ",'Cope_Formlang');
			}

	}

	public static function ITJ_form_getCSS() {
		$file = YouFormsITJ_TEMPLATE.'styles.html';

			if (file_exists($file)) {
				return file_get_contents($file);
			} else {
				return "";
			}
	}

	public static function ITJ_CheckPostType(){
		//This if prevent showing the workspace in media or else!
		$check = true;
		$scr = get_current_screen();
		if  ( $scr->post_type !== YouFormsITJ_post_type) {
			$check = false;
		}
		return $check;
	}


	public static function ITJ_form_WorkSpace() {

		if(!self::ITJ_CheckPostType()){
			return;
		};

		global $post;
				//Für Action wichtig
				wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );
				$copecartUrl = get_post_meta( get_the_ID(), 'copecarturl', true ) ? get_post_meta( get_the_ID(), 'copecarturl', true ) : "https://www.copecart.com/products/b1670155/checkout";
				$hidemodaltemp = get_post_meta( get_the_ID(), 'hidemodaltemplate', true ) ? get_post_meta( get_the_ID(), 'hidemodaltemplate', true ) : "0";

		?>
		<hr>
		<h3><b style="color: #2470ff"><div id="cp_shortcode">Shortcode: <?php echo '[form_copecart id="'.get_the_ID().'"]';?></div></b></h3>
		<hr>
		<div id="container_copecarturl" class="row no-gutters" style="padding-top: 0px; padding-bottom: 0px;">
			<div class="col-md-12">
				<div class="form-group mobilepad" style="padding-right: 10px;">
					<h1 for="copecarturl"><b>1.) CopeCart URL:</b></h1>
						<?php echo '<h4>'.__("Help: Put your product url in here that you get from CopeCart! This link open's when you click on the checkoutbutton!",'Cope_Formlang').'</h4>'; ?>
					<input type="text" class="form-control-youforms input-lg" value="<?php echo esc_url($copecartUrl); ?>" style="font-size: 16px;" id="copecarturl" name="copecarturl" placeholder="<?php echo __("Paste your CopeCart product url here!",'Cope_Formlang'); ?>">
					<input type="hidden" id="isfade" name="isfade" value="<?php echo esc_html($hidemodaltemp); ?>">
				</div>
			</div>
		</div>

		<?php
			wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );

			echo '<center><div id="containerbuttontemp"><h2 style="font-size: 25px;">'.__("Button template",'Cope_Formlang').'</h2>';
			echo '<small>'.__("Help: This button open's the Modal template below as a popup!",'Cope_Formlang').'</small>';

			echo "<br><br></div></center>";

			//style
			echo "<div id='workspace'>";

			$formid = get_post_meta( get_the_ID(), 'formid', true ) ? get_post_meta( get_the_ID(), 'formid', true ) : false;
		
			if(!$formid) {
				//Generate unique form id
				$formid = self::generate_string(self::$permitted_chars, 5);
			}

			$allowedbtnhtml = ["div" => ["id" => [], "class" => [], "style" => [], "aria-disabled" => [], "data-element-theme" => [], "data-google-font" => [], "data-elbuttontype" => []], "center" => [], "a" => ["id" => [], "data-target" => [], "data-toggle" => [], "data-modal" => [], "class" => [],"style" => [], "rel" => []], "span" => ["class" => [], "id" => [], "style" => []]];	
			$allowedModalhtml = [ "path"=> ["fill-rule" => [], "d" => []],"svg" => ["width" => [], "height" => [], "viewBox" => [], "class" => [],"fill" => [], "xmlns" => []], "a" => ["id" => [], "class" => [], "style" => [], "rel" => []],"div" => ["id" => [],"class" => [],"tabindex" => [],"role" => [],"aria-hidden" => [],"style" => []], "center" => [],"input" => ["type" => [], "value" => [], "class" => [], "style" => [], "id" => [],"placeholder" => []],"select" => ["name" => [], "class" => [],"data-type" => [],"style" => [], "required" => [], "id" => []], "option" => ["selected" => [], "value" => []], "span" => ["class" => [], "id" => [], "style" => []]];

	
			echo wp_kses(self::ITJ_ReplaceAllTagsInBtnTemplate(get_the_ID(), $formid),$allowedbtnhtml);

			echo "<input type='hidden' name='formid' id='formid' value='".esc_html($formid)."'>";

			//Get Modal und Script
			echo '<center><div id="modaltempcontainer"><h2 style="font-size: 25px;">'.__("Modal template",'Cope_Formlang').'</h2>';
			echo "<hr></div></center>";

			echo wp_kses(self::ITJ_ReplaceAllTagsInModalTemplate(get_the_ID(), $formid),$allowedModalhtml);


			wp_enqueue_style('itj_youforms_css', YouFormsITJ_CSS.'youforms.css', array(), YouFormsITJ_VERSION);
			wp_enqueue_script('itj_youforms_js', YouFormsITJ_JS.'youforms.js', array('jquery'), YouFormsITJ_VERSION);
			wp_enqueue_script('itj_youforms_bootstrapjs', YouFormsITJ_JS_LIBS.'bootstrap.min.js', array('jquery'),YouFormsITJ_VERSION);

			echo "</div";

	}

	public static function Cope_Form_JS_CSS_header() {
		# Get the globals:
		global $post, $wp_meta_boxes;

		//Check post_type of this Plugin if not match block code
		if ($post->post_type !== YouFormsITJ_post_type) {
			return;
		}

		//Cope_Formipt
		wp_enqueue_script('cope_form-tools', YouFormsITJ_JS_LIBS . 'tools.js', array('jquery'), YouFormsITJ_VERSION);
		wp_enqueue_script('cope_form-main', YouFormsITJ_JS . 'main.js', array('jquery'), YouFormsITJ_VERSION);
		wp_enqueue_style('cope_form_css', YouFormsITJ_CSS . 'styles.css', array(), YouFormsITJ_VERSION, false);
		unset( $wp_meta_boxes['post']['vi_after_title'] );
	}

	public static function check_page_now() {
		global $pagenow;
		$check = true;
		if ( get_post_type( get_the_ID() ) != 'youforms' ) {
			$check = false;
		}

		return $check;
	}


	public static function render_tutorial_email_text() {
		if ( ! self::check_page_now() ) {
			return;
		}

		echo '<br><h2 style="font-size: 25px;">'.__("Modal template",'Cope_Formlang').'</h2><br>';

		$content = get_post_meta( get_the_ID(), 'body_content', true );

		//Wenn neues Template fuelle standart text ein
		if (isset($_GET['post_type']) == 'youforms' ) {

			$file = YouFormsITJ_TEMPLATE.'modal.html';

				if (file_exists($file)) {
					$content = file_get_contents($file);
				} else {
					$content = "file not exists ".$file;
				}
		}

		?>
		<?php
	}

	 public static function cope_form_enabledisable(){

		 wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );

		 $hidebuttontemp = get_post_meta( get_the_ID(), 'hidebtntemplate', true ) ? get_post_meta( get_the_ID(), 'hidebtntemplate', true ) : "0";
		 $hidemodaltemp = get_post_meta( get_the_ID(), 'hidemodaltemplate', true ) ? get_post_meta( get_the_ID(), 'hidemodaltemplate', true ) : "0";

		 ?>
		 <div class="row">
			 <div class="col-12">
				 	<div style="padding-left: 15px;">
					 <div class="form-group ">
						 <input type="checkbox" name="hidebtntemplate" id="hidebtntemplate" <?php checked( $hidebuttontemp, 'on', 'checked="checked"');?>>
						 <label for="disablebtntemplate"><?php echo __("Hide button template",'Cope_Formlang'); ?></label>
					</div>
				</div>
			</div>
		 </div>
		 <?php
	 }

	public static function get_metabox() {


		//Hide Button or Modal
		add_meta_box(
			'cope_form_enabledisable',//'Cope_Form_settings',
			esc_html__( 'Visible/hide components','Cope_Formlang' ),
			[ __CLASS__, 'cope_form_enabledisable'],//'Cope_Form_settings' ],
			'youforms',
			'side',
			'default'
		);

		//Button text
		add_meta_box(
			'cope_form_changeButtonTemplateText',//'Cope_Form_settings',
			esc_html__( 'Change button template text','Cope_Formlang' ),
			[ __CLASS__, 'cope_form_changeButtonTemplateText'],//'Cope_Form_settings' ],
			'youforms',
			'side',
			'default'
		);


		//Button color
		add_meta_box(
			'cope_form_changeButtonTemplateColor',//'Cope_Form_settings',
			esc_html__( 'Change button template color','Cope_Formlang' ),
			[ __CLASS__, 'cope_form_changeButtonTemplateColor'],//'Cope_Form_settings' ],
			'youforms',
			'side',
			'default'
		);

		//Modal text
		add_meta_box(
			'cope_form_changeModalTemplateText',//'Cope_Form_settings',
			esc_html__( 'Change modal template text','Cope_Formlang' ),
			[ __CLASS__, 'cope_form_changeModalTemplateText'],//'Cope_Form_settings' ],
			'youforms',
			'side',
			'default'
		);
	}

	public static function cope_form_changeButtonTemplateColor() {
		wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );

		$btnbgcolorhex = get_post_meta( get_the_ID(), 'buttontempbgcolor', true ) ? get_post_meta( get_the_ID(), 'buttontempbgcolor', true ) : "#2470ff";
		$btnfontcolorhex = get_post_meta( get_the_ID(), 'buttonfontcolor', true ) ? get_post_meta( get_the_ID(), 'buttonfontcolor', true ) : "#FFFFFF";

		?>
		<div class="row">
			<div class="col-12">
					<div style="padding-left: 15px;">
		  <label for="rahmenfarbe_inputfelder"><?php echo __("Button background:",'Cope_Formlang'); ?> </label>
		    <div class="input-group">
		      <input type="color" name="buttontempbgcolor" id="buttontempbgcolor" value="<?php echo esc_html($btnbgcolorhex); ?>">
		    </div>
		  </div>
		</div>
		</div>
		<div class="row">
			<div class="col-12">
					<div style="padding-left: 15px;">
			<label for="rahmenfarbe_inputfelder"><?php echo __("Button font-color:",'Cope_Formlang'); ?></label>
				<div class="input-group">
					<input type="color" name="buttonfontcolor" id="buttonfontcolor" value="<?php echo esc_html($btnfontcolorhex); ?>">
				</div>
			</div>
		</div>
		</div>
		<?php
	}

	public static function cope_form_changeButtonTemplateText() {
		wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );

		$btntxt = get_post_meta( get_the_ID(), 'buttonclicktext', true ) ? get_post_meta( get_the_ID(), 'buttonclicktext', true ) : __("Subscribe NOW! to the premium Wordpress Plugin!",'Cope_Formlang');
		$btnsubtxt = get_post_meta( get_the_ID(), 'buttonclicksubtext', true ) ? get_post_meta( get_the_ID(), 'buttonclicksubtext', true ) : __("Create checkouts very easy, with the premium CopCart Form!",'Cope_Formlang');
		?>

		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Button text:",'Cope_Formlang'); ?></b></label>
						<input type="text" class="form-control-youforms input-sm" name="buttonclicktext" value="<?php echo esc_attr(esc_html($btntxt)); ?>" id="buttonclicktext" placeholder="<?php echo __("Your Button-text..",'Cope_Formlang'); ?>">
					</div>
				</div>
		</div>
		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Button subtext:",'Cope_Formlang');?></b></label>
						<input type="text" class="form-control-youforms input-sm" name="buttonclicksubtext" value="<?php echo esc_attr($btnsubtxt); ?>" id="buttonclicksubtext" placeholder="<?php echo __('Your Button-subtext...','Cope_Formlang'); ?>">
					</div>
				</div>
		</div>

		<?php
	}

	public static function cope_form_changeModalTemplateText() {
		wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );
		$headertxt = get_post_meta( get_the_ID(), 'headertext', true ) ? get_post_meta( get_the_ID(), 'headertext', true ) :  __("Billing address",'Cope_Formlang');
		$headersubtxt = get_post_meta( get_the_ID(), 'headersubtext', true ) ? get_post_meta( get_the_ID(), 'headersubtext', true ) : __("Yes! i will subscribe to the premium version!",'Cope_Formlang');
		$headerbgcolor = get_post_meta( get_the_ID(), 'headerbgcolor', true ) ? get_post_meta( get_the_ID(), 'headerbgcolor', true ) : "#4c8a22";
		$headerfontcolor = get_post_meta( get_the_ID(), 'headerfontcolor', true ) ? get_post_meta( get_the_ID(), 'headerfontcolor', true ) : "#FFFFFF";
		$btnheadertxt = get_post_meta( get_the_ID(), 'buttonheadertext', true ) ? get_post_meta( get_the_ID(), 'buttonheadertext', true ) : __("Continue Checkout",'Cope_Formlang');
		$btnheadersubtxt = get_post_meta( get_the_ID(), 'buttonheadersubtext', true ) ? get_post_meta( get_the_ID(), 'buttonheadersubtext', true ) : __("Checkout will be done by CopeCart!",'Cope_Formlang');
		$btnbuybgcolor = get_post_meta( get_the_ID(), 'buybuttonbgcolor', true ) ? get_post_meta( get_the_ID(), 'buybuttonbgcolor', true ) : "#2470ff";
		$btnbuyfontcolor = get_post_meta( get_the_ID(), 'buybuttonfontcolor', true ) ? get_post_meta( get_the_ID(), 'buybuttonfontcolor', true ) : "#FFFFFF";
		$smalltextlbl = get_post_meta( get_the_ID(), 'smalltextlbl', true ) ? get_post_meta( get_the_ID(), 'smalltextlbl', true ) : "";

		?>

		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Header text:",'Cope_Formlang'); ?></b></label>
						<input type="text" class="form-control-youforms input-sm" value="<?php echo esc_attr($headertxt); ?>" name="headertext" id="headertext" placeholder="<?php echo __("Your Headertext..",'Cope_Formlang'); ?>">
					</div>
				</div>
		</div>
		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Header subtext:",'Cope_Formlang'); ?></b></label>
						<input type="text" class="form-control-youforms input-sm" value="<?php echo esc_attr($headersubtxt); ?>" name="headersubtext" id="headersubtext" placeholder="<?php echo __("Your Headersubtext...",'Cope_Formlang'); ?>">
					</div>
				</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div style="padding-left: 15px;">
			<label for="rahmenfarbe_inputfelder"><?php echo __("Header background:",'Cope_Formlang'); ?></label>
				<div class="input-group">
					<input type="color" name="headerbgcolor" id="headerbgcolor" value="<?php echo esc_attr($headerbgcolor); ?>">
				</div>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
					<div style="padding-left: 15px;">
			<label for="rahmenfarbe_inputfelder"><?php echo __("Header font-color:",'Cope_Formlang');?></label>
				<div class="input-group">
					<input type="color" name="headerfontcolor" id="headerfontcolor" value="<?php echo esc_attr($headerfontcolor); ?>">
				</div>
			</div>
		</div>
		</div>
		<hr>
		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Button text:",'Cope_Formlang');?></b></label>
						<input type="text" class="form-control-youforms input-sm" name="buttonheadertext" value="<?php echo esc_attr($btnheadertxt); ?>" id="buttonheadertext" placeholder="<?php echo __("Your Headertext..",'Cope_Formlang');?>">
					</div>
				</div>
		</div>
		<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						 <label for="copecarturl"><b><?php echo __("Button subtext",'Cope_Formlang');?></b></label>
						<input type="text" class="form-control-youforms input-sm" name="buttonheadersubtext" value="<?php echo esc_attr($btnheadersubtxt); ?>" id="buttonheadersubtext" placeholder="<?php echo __("Your Headersubtext...",'Cope_Formlang');?>">
					</div>
				</div>
		</div>
		<div class="row">
			<div class="col-12">
					<div style="padding-left: 15px;">
			<label for="rahmenfarbe_inputfelder"><?php echo __("Button background:",'Cope_Formlang');?></label>
				<div class="input-group">
					<input type="color" name="buybuttonbgcolor" id="buybuttonbgcolor" value="<?php echo esc_attr($btnbuybgcolor); ?>">
				</div>
			</div>
		</div>
		</div>
		<div class="row">
			<div class="col-12">
					<div style="padding-left: 15px;">
			<label for="rahmenfarbe_inputfelder"><?php echo __("Button font-color:",'Cope_Formlang');?></label>
				<div class="input-group">
					<input type="color" name="buybuttonfontcolor" id="buybuttonfontcolor" value="<?php echo esc_attr($btnbuyfontcolor); ?>">
				</div>
			</div>
		</div>
		</div>
		<?php
	}

	public static function cope_form_hidde_fields() {
		?>
					<div id="toggles">
						<input type="checkbox" name="checkbox1" id="input_vorname" class="ios-toggle" checked />
						<label for="input_vorname" class="checkbox-label" data-off="Vorname aus" data-on="Vorname an"></label>

						<input type="checkbox" name="checkbox1" id="input_nachname" class="ios-toggle" checked />
						<label for="input_nachname" class="checkbox-label" data-off="Nachname aus" data-on="Nachname an"></label>

						<input type="checkbox" name="checkbox1" id="input_email" class="ios-toggle" checked />
						<label for="input_email" class="checkbox-label" data-off="Email aus" data-on="Email an"></label>

						<input type="checkbox" name="checkbox1" id="input_telefon" class="ios-toggle" checked />
						<label for="input_telefon" class="checkbox-label" data-off="Telefon aus" data-on="Telefon an"></label>

						<input type="checkbox" name="checkbox1" id="input_adresse" class="ios-toggle" checked />
						<label for="input_adresse" class="checkbox-label" data-off="Adresse aus" data-on="Adresse an"></label>

						<input type="checkbox" name="checkbox1" id="input_hausnummer" class="ios-toggle" checked />
						<label for="input_hausnummer" class="checkbox-label" data-off="Hausnummer aus" data-on="Hausnummer an"></label>

						<input type="checkbox" name="checkbox1" id="input_stadt" class="ios-toggle" checked />
						<label for="input_stadt" class="checkbox-label" data-off="Stadt aus" data-on="Stadt an"></label>

						<input type="checkbox" name="checkbox1" id="input_leander" class="ios-toggle" checked />
						<label for="input_leander" class="checkbox-label" data-off="Auswahlbox Länder aus" data-on="Auswahlbox Länder an"></label>

						<input type="checkbox" name="checkbox1" id="input_plz" class="ios-toggle" checked />
						<label for="input_plz" class="checkbox-label" data-off="PLZ aus" data-on="PLZ an"></label>

						<input type="checkbox" name="checkbox1" id="input_paymentlogos" class="ios-toggle" checked />
						<label for="input_paymentlogos" class="checkbox-label" data-off="Payment Logos aus" data-on="Payment Logo an"></label>

						<input type="checkbox" name="checkbox1" id="input_kleingedrucktes" class="ios-toggle" checked />
						<label for="input_kleingedrucktes" class="checkbox-label" data-off="Kleingedrucktes aus" data-on="Kleingedrucktes an"></label>

						<input type="checkbox" name="checkbox1" id="input_laenderdeen" class="ios-toggle" checked />
						<label for="input_laenderdeen" class="checkbox-label" data-off="Länderauswahl in Englisch" data-on="Länderauswahl auf Deutsch"></label>
					</div>
		<?php
	}


	public static function Cope_Form_settings() {
		global $post;
		$email_enable    = get_post_meta( get_the_ID(), 'wce_enable', true ) ? get_post_meta( get_the_ID(), 'wce_enable', true ) : 'on';
		$email_send_date = get_post_meta( get_the_ID(), 'wce_before_cart_expiry_date', true ) ? get_post_meta( get_the_ID(), 'wce_before_cart_expiry_date', true ) : 1;
		$expiry_unit     = get_post_meta( get_the_ID(), 'wce_expiry_unit', true ) ? get_post_meta( get_the_ID(), 'wce_expiry_unit', true ) : 'days';
		wp_nonce_field( basename( __FILE__ ), 'Cope_Form_reminder_fields' );
		?>
        <div class="wce-option-group flex">
            <span><?php echo esc_html__( 'Enable Email','Cope_Formlang' ); ?></span>
            <div class="vi-ui toggle checkbox">
                <input <?php checked( $email_enable, 'on' ); ?> type="checkbox" name="wce_enable_status"/>
                <label></label>
            </div>
        </div>
        <label><?php echo esc_html__( 'Before cart expiry date','Cope_Formlang' ); ?></label>
        <div class="wce-option-group flex">
            <div class="wce-group-input">
                <input type="number" name="wce_before_cart_expiry_date" min="1"
                       value="<?php echo esc_attr( $email_send_date ); ?>">
            </div>
            <div class="wce-group-input">
                <select name="wce_expiry_unit">
                    <option value="days" <?php selected( $expiry_unit, 'days' ) ?>><?php echo esc_html__( 'Days','Cope_Formlang' ); ?></option>
                    <option value="hours" <?php selected( $expiry_unit, 'hours' ) ?>><?php echo esc_html__( 'Hours','Cope_Formlang' ); ?></option>
                    <option value="minutes" <?php selected( $expiry_unit, 'minutes' ) ?>><?php echo esc_html__( 'Minutes','Cope_Formlang' ); ?></option>
                    <option value="seconds" <?php selected( $expiry_unit, 'seconds' ) ?>><?php echo esc_html__( 'Seconds','Cope_Formlang' ); ?></option>
                </select>
            </div>
        </div>
		<?php
	}


	/**
	 * Save the metabox data
	 */
	public static function Cope_Form_save_events_meta( $post_id ) {

		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Verify this came from the our Cope_Formeen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! wp_verify_nonce( isset( $_POST['Cope_Form_reminder_fields'] ) ? sanitize_text_field( $_POST['Cope_Form_reminder_fields'] ) : '', basename( __FILE__ ) ) ) {
			return $post_id;
		}

		$postdataArray["copecarturl"] = esc_url_raw( $_POST['copecarturl'] );
		$postdataArray["buttonclicktext"] = sanitize_text_field( $_POST['buttonclicktext'] );
		$postdataArray["buttonclicksubtext"] = sanitize_text_field( $_POST['buttonclicksubtext'] );
		$postdataArray["buttontempbgcolor"] = sanitize_text_field( $_POST['buttontempbgcolor'] );
		$postdataArray["buttonfontcolor"] = sanitize_text_field( $_POST['buttonfontcolor'] );
		$postdataArray["headertext"] = sanitize_text_field( $_POST['headertext'] );
		$postdataArray["headersubtext"] = sanitize_text_field( $_POST['headersubtext'] );
		$postdataArray["headerbgcolor"] = sanitize_text_field( $_POST['headerbgcolor'] );
		$postdataArray["headerfontcolor"] = sanitize_text_field( $_POST['headerfontcolor'] );
		$postdataArray["buttonheadertext"] = sanitize_text_field( $_POST['buttonheadertext'] );
		$postdataArray["buttonheadersubtext"] = sanitize_text_field( $_POST['buttonheadersubtext'] );
		$postdataArray["buybuttonbgcolor"] = sanitize_text_field( $_POST['buybuttonbgcolor'] );
		$postdataArray["buybuttonfontcolor"] = sanitize_text_field( $_POST['buybuttonfontcolor'] );
		$postdataArray["smalltextlbl"] = sanitize_text_field( $_POST['smalltextlbl'] );
		$postdataArray["hidebtntemplate"] = sanitize_text_field($_POST["hidebtntemplate"]);
		$postdataArray["hidemodaltemplate"] = sanitize_text_field($_POST["hidemodaltemplate"]);
		$postdataArray["hideproductview"] = sanitize_text_field($_POST["hideproductview"]);
		$postdataArray["formid"] = sanitize_text_field($_POST["formid"]);
		$postdataArray["btnprodviewbgcolor"] = sanitize_text_field($_POST["btnprodviewbgcolor"]);
		$postdataArray["buttonproductfontcolor"] = sanitize_text_field($_POST["buttonproductfontcolor"]);
		$postdataArray["productname"] = sanitize_text_field($_POST["productname"]);
		$postdataArray["productprice"] = sanitize_text_field($_POST["productprice"]);
		$postdataArray["productimg"] = sanitize_text_field($_POST["productimg"]);
		$postdataArray["producturl"] = esc_url_raw($_POST["producturl"]);
		$postdataArray["bgimgproductview"] = esc_url_raw($_POST["bgimgproductview"]);

		$post_type   = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );
		if ( "youforms" == $post_type && "auto-draft" != $post_status ) {
			foreach ( $postdataArray as $key => $value ) :
				// Don't store custom data twice
				if ( get_post_meta( $post_id, $key, true ) ) {
					// If the custom field already has a value, update it.
					update_post_meta( $post_id, $key, $value );
				} else {
					// If the custom field doesn't have a value, add it.
					add_post_meta( $post_id, $key, $value );
				}
				if ( ! $value ) {
					// Delete the meta key if there's no value
					delete_post_meta( $post_id, $key );
				}
			endforeach;
		}

		return $post_id;


	}
}
