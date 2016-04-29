<?php

use Smite\SmitePage;
use Smite\SmiteSession;
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

function getPage() {
	global $map;
	$page = new SmitePage();
	$session = new SmiteSession();

	$apiKey = $session->getCurrentApiKey();
	if( $apiKey === null ) {
		$page->addLoginForm();
		return $page;
	}

	$client = new RestClient('https://wmd.mite.yo.lk',$apiKey);
	$client = new MiteClient($client);
	$store = new SmiteStore( $apiKey );
	try{
		$client->getMyself();
	}
	catch( Exception $e ) {
		$page->addLoginForm('Did you enter your key wrong?');
		return $page;
	}
	$session->addKeyToCookie( $apiKey );

	if( isset( $_POST['logout'] ) ) {
		$session->removeCookie();
		$page->addLoginForm("Logged out!");
		return $page;
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

	$page->addTimers( $map, $store->getTimer(), $store->getRunningTimers(), $apiKey );
	$page->addButton( 'STOP', $apiKey );
	$page->addRecordedSeconds( $store->getSeconds() );
	$page->addSaveButton( $apiKey );
	$page->addLogoutButton();

	return $page;

}

getPage()->output();