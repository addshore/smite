<?php

namespace Smite;

class SmitePage {

	private $body = '';

	public function output() {
		echo '<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>sMite</title>
	<link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body role="document" >';
		echo $this->getNavBar();
		echo'
<div class="container" role="main" style="margin-top:50px">
<div class="row">';
		echo $this->body;
		echo '
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>';
	}

	public function getNavBar() {
		return '
<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">sMite</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li>' . $this->getLogoutButton() . '</li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>';
	}

	public function addTimers( $map, $timer, $timers, $apiKey ) {
		$this->body .= '<div class="col-sm-4">';
		$this->body .= "<h2>Start timers</h2>";
		$this->body .= "<p>Current timer started at: " . $timer . "</p>";
		foreach( $map as $itemName => list( $projectId, $serviceId ) ) {
			if( in_array( $itemName, $timers ) ) {
				$this->body .= '<p>' . $itemName . ' running</p>';
			} else {
				$this->addButton( $itemName, $apiKey );
			}
		}
		$this->addStoputton( $apiKey );
		$this->body .= '</div>';
	}
	
	public function addRecordedSeconds( $secondsList, $apiKey ) {
		$this->body .= '<div class="col-sm-4">';
		$this->body .= "<h2>Seconds recorded</h2>";
		$this->body .= "<ul>";
		$totalSeconds = 0;
		foreach( $secondsList as $secondKey => $seconds ) {
			$this->body .= "<li>$secondKey: $seconds</li>";
			$totalSeconds =+ $seconds;
		}
		$this->body .= "</ul>";
		$this->body .= "<p>Total seconds: $totalSeconds</p>";
		$this->addSaveButton( $apiKey );
		$this->body .= '</div>';
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

	private function addButton( $mapName, $key ) {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input class="btn btn-lg btn-success" type="submit" value="' . $mapName . '">';
		$this->body .= "<input type='hidden' name='item' value='" . $mapName . "'>";
		$this->body .= "<input type='hidden' name='key' value='" . $key . "'>";
		$this->body .= '</form>';
	}
	
	private function addSaveButton( $apiKey ) {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input class="btn btn-lg btn-info" type="submit" value="Save mins">';
		$this->body .= "<input type='hidden' name='save' value='save'>";
		$this->body .= "<input type='hidden' name='key' value='" . $apiKey . "'>";
		$this->body .= '</form>';
	}

	private function addStoputton( $apiKey ) {
		$this->body .= '<form action="index.php" method="post">';
		$this->body .= '<input class="btn btn-lg btn-danger" type="submit" value="STOP">';
		$this->body .= "<input type='hidden' name='item' value='STOP'>";
		$this->body .= "<input type='hidden' name='key' value='" . $apiKey . "'>";
		$this->body .= '</form>';
	}
	
	private function getLogoutButton() {
		return '<form action="index.php" method="post">
<input class="btn btn-lg btn-info" type="submit" value="Logout">
<input type="hidden" name="logout" value="logout">
</form>';
	}

	public function addToBody( $string ) {
		$this->body .= $string;
	}

}