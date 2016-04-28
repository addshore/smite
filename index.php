<?php

use Smite\SmiteStore;
use SysEleven\MiteEleven\MiteClient;
use SysEleven\MiteEleven\RestClient;

require_once __DIR__ . '/vendor/autoload.php';

if( !function_exists( 'intdiv' ) ) {
	function intdiv($a, $b){
		return ($a - $a % $b) / $b;
	}
}

$projectMap = [
	'Admin' => 1484086,
	'Internal Project' => 1499396,
	'Wikidata' => 1003630,
	'GTWL Wish' => 1499393,
	'International Wish' => 1759952,
	'MediaWiki' => 1759950,
	'WMF CommTech Scaling' => 1759949,
];

$serviceMap = [
	'General' => 205675,
	'Lizenzverweisgenerator' => 311487,
	'Phragile' => 336970,
	'Analytics' => 337456,
];

$map = [
	'Lizenzverweisgenerator' => [ $projectMap['Internal Project'], $serviceMap['Lizenzverweisgenerator'] ],
	'Phragile' => [ $projectMap['Internal Project'], $serviceMap['Phragile'] ],
	'Wikidata' => [ $projectMap['Wikidata'], $serviceMap['General'] ],
	'Wikidata Analytics' => [ $projectMap['Wikidata'], $serviceMap['Analytics'] ],
	'GTWL Wish' => [ $projectMap['GTWL Wish'], $serviceMap['General'] ],
	'GTWL Analytics' => [ $projectMap['GTWL Wish'], $serviceMap['Analytics'] ],
	'International Wish' => [ $projectMap['International Wish'], $serviceMap['General'] ],
	'MediaWiki' => [ $projectMap['MediaWiki'], $serviceMap['General'] ],
];

$output = '';;

function startPage() {
	global $output;
	$output .= '<html>';
	$output .= '<head>';
	$output .= '</head>';
	$output .= '<body>';
}

function endPage() {
	global $output;
	$output .= '</body>';
	$output .= '</html>';
}

function addLoginForm( $extraMessage = '' ) {
	global $output;
	$output .= '<h1>Please enter your API key!</h1>';
	$output .= '<p>You can get this from <a href="https://wmd.mite.yo.lk/myself">https://wmd.mite.yo.lk/myself</a></p>';
	$output .= '<form action="index.php" method="post">';
	$output .= '<input type="text" name="key">';
	$output .= '<input type="submit" value="Submit">';
	$output .= '</form>';
	$output .= '<p>' . $extraMessage . '</p>';
}

function addButton( $mapName, $key ) {
	global $output;
	$output .= '<form action="index.php" method="post">';
	$output .= '<input type="submit" value="' . $mapName . '">';
	$output .= "<input type='hidden' name='item' value='" . $mapName . "'>";
	$output .= "<input type='hidden' name='key' value='" . $key . "'>";
	$output .= '</form>';
}

function main() {
	global $output, $map;
	if( !isset( $_POST['key'] ) && !isset( $_COOKIE['SMITE_KEY'] ) ) {
		addLoginForm();
		return;
	}

	if( isset( $_POST['key'] ) ) {
		$apiKey = $_POST['key'];
	} else {
		$apiKey = $_COOKIE['SMITE_KEY'];
	}

	$client = new RestClient('https://wmd.mite.yo.lk',$apiKey);
	$client = new MiteClient($client);
	$store = new SmiteStore( $apiKey );
	try{
		$client->getMyself();
	}
	catch( Exception $e ) {
		addLoginForm( 'Did you enter your key wrong?' );
		return;
	}
	setcookie( 'SMITE_KEY', $apiKey );

	if( isset( $_POST['logout'] ) ) {
		setcookie("SMITE_KEY", "", time() - 3600);
		addLoginForm("Logged out!");
		return;
	}

	if( isset( $_POST['save'] ) ) {
		$fullMins = $store->getFullMins();

		foreach( $fullMins as $key => $mins ){
			list( $projectId, $serviceId ) = $map[$key];
			$entries = $client->listEntries( [
				'project_id' => $projectId,
				'service_id' => $serviceId,
				'at' => 'today',
			] );
			if( empty( $entries ) ) {
				$client->createEntry([
					'minutes' => $mins,
					'project_id' => $projectId,
					'service_id' => $serviceId,
					'note' => "Created by Smite with $mins mins!\n",
				]);
			} else {
				$entry = $entries[0]['time_entry'];
				$client->updateEntry(
					$entry['id'],
					[
						'minutes' => $entry['minutes'] + $mins,
						'note' => $entry['note']  . "\n" . "Smite added $mins mins.\n",
					]
				);
			}
			$store->removeMins( $key, $mins );
		}
	}

	if( isset( $_POST['item'] ) ) {
		$item = $_POST['item'];
		if( $item == 'STOP' ) {
			$store->stopAllTimers();
		} else {
			$store->startTimer( $item );
		}
	}

	$timers = $store->getRunningTimers();

	$output .= "<h2>Start timers</h2>";
	$output .= "<p>Current timer started at: " . $store->getTimer() . "</p>";
	foreach( $map as $itemName => list( $projectId, $serviceId ) ) {
		if( in_array( $itemName, $timers ) ) {
			$output .= '<p>' . $itemName . ' running</p>';
		} else {
			addButton( $itemName, $apiKey );
		}
	}
	addButton( 'STOP', $apiKey );

	$output .= "<h2>Seconds recorded</h2>";
	$output .= "<ul>";
	$totalSeconds = 0;
	foreach( $store->getSeconds() as $secondKey => $seconds ) {
		$output .= "<li>$secondKey: $seconds</li>";
		$totalSeconds =+ $seconds;
	}
	$output .= "</ul>";
	$output .= "<p>Total seconds: $totalSeconds</p>";

	$output .= '<form action="index.php" method="post">';
	$output .= '<input type="submit" value="Save mins">';
	$output .= "<input type='hidden' name='save' value='save'>";
	$output .= "<input type='hidden' name='key' value='" . $apiKey . "'>";
	$output .= '</form>';

	$output .= '<form action="index.php" method="post">';
	$output .= '<input type="submit" value="Logout">';
	$output .= "<input type='hidden' name='logout' value='logout'>";
	$output .= "<input type='hidden' name='key' value='" . $apiKey . "'>";
	$output .= '</form>';

}

main();
echo $output;