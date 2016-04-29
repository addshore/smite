<?php

namespace Smite;

class SmiteSession {

	const COOKIE_NAME = 'SMITE_KEY';

	/**
	 * @return null|string
	 */
	public function getCurrentApiKey() {
		if( !isset( $_POST['key'] ) && !isset( $_COOKIE[self::COOKIE_NAME] ) ) {
			return null;
		}

		if( isset( $_POST['key'] ) ) {
			return $_POST['key'];
		} else {
			return $_COOKIE[self::COOKIE_NAME];
		}
	}

	public function addKeyToCookie( $apiKey ) {
		setcookie( self::COOKIE_NAME, $apiKey );
	}
	
	public function removeCookie() {
		setcookie("SMITE_KEY", "", time() - 3600);
	}

}