<?php
class LF_User {
	function get_cookie_name() {
		return 'leeflets_' . md5( LF_ADMIN_URL );
	}

	function is_logged_in() {

	}
}
