<?php
/*
Copyright 2020 itservicejung.de - All Rights Reserved
*/

class YouFormsITJ_Dashboard {
	protected $settings;
	protected static $initd = false;

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

		//Check post_type of this Plugin if not match block code
		if ($_GET["post_type"] !== YouFormsITJ_post_type) {
			return;
		}

		add_action( 'admin_notices', [ __CLASS__, 'feedback_notice' ] );

		self::$initd = true;
	}

	public static function feedback_notice() {
		if(!YouFormsITJ_Meta_Post_Editor::ITJ_CheckPostType()){
			return;
		};
		?>

	<div class="notice notice-success is-dismissible">
			<p><?php _e('👋 <a target="blank_" href="https://api.itservicejung.de/feedback?plugin='.YouFormsITJ_plugin_name.'">We need your suggestion on how we can improve <b>'.YouFormsITJ_plugin_name.'</b>!</a> 😛 ', 'sample-text-domain' ); ?></p>
	</div>
	<?php

	}

	public static function checkloading() {
			return true;
	}


}
