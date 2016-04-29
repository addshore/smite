<?php

namespace Smite;

class SmitePage {

	private $body = '';

	public function output() {
		echo '<html>';
		echo '<head>';
		echo '</head>';
		echo '<body>';
		echo $this->body;
		echo '</body>';
		echo '</html>';
	}
	
	public function addTimers( $map, $timer, $timers, $apiKey ) {
		$this->body .= "<h2>Start timers</h2>";
		$this->body .= "<p>Current timer started at: " . $timer . "</p>";
		foreach( $map as $itemName => list( $projectId, $serviceId ) ) {
			if( in_array( $itemName, $timers ) ) {
				$this->body .= '<p>' . $itemName . ' running</p>';
			} else {
				$this->addButton( $itemName, $apiKey );
			}
		}
	}
	
	public function addRecordedSeconds( $secondsList ) {
		$this->body .= "<h2>Seconds recorded</h2>";
		$this->body .= "<ul>";
		$totalSeconds = 0;
		foreach( $secondsList as $secondKey => $seconds ) {
			$this->body .= "<li>$secondKey: $seconds</li>";
			$totalSeconds =+ $seconds;
		}
		$this->body .= "</ul>";
		$this->body .= "<p>Total seconds: $totalSeconds</p>";
	}
	
	public function addLoginForm( $extraMessage = '' ) {
		$this->body .= '<h1>Please enter your API key!</h1>';
		$this->body .= '<p>You can get this from <a href="https://wmd.mite.yo.lk/myself">https://wmd.mite.yo.lk/myself</a></p>';
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input type="text" name="key">';
		$this->body .= '<input type="submit" value="Submit">';
		$this->body .= '</form>';
		$this->body .= '<p>' . $extraMessage . '</p>';
	}

	public function addButton( $mapName, $key ) {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input type="submit" value="' . $mapName . '">';
		$this->body .= "<input type='hidden' name='item' value='" . $mapName . "'>";
		$this->body .= "<input type='hidden' name='key' value='" . $key . "'>";
		$this->body .= '</form>';
	}
	
	public function addSaveButton( $apiKey ) {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input type="submit" value="Save mins">';
		$this->body .= "<input type='hidden' name='save' value='save'>";
		$this->body .= "<input type='hidden' name='key' value='" . $apiKey . "'>";
		$this->body .= '</form>';
	}
	
	public function addLogoutButton() {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input type="submit" value="Logout">';
		$this->body .= "<input type='hidden' name='logout' value='logout'>";
		$this->body .= '</form>';
	}

	public function addToBody( $string ) {
		$this->body .= $string;
	}

}