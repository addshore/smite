<?php

namespace Smite;

class SmiteStore {

	private $file;

	public function __construct( $key ) {
		$this->file = __DIR__ . '/../data/' . $key . '.json';
		if( !file_exists( $this->file ) ) {
			file_put_contents( $this->file, json_encode( [
				'timer' => null,
				'running' => [],
				'seconds' => [],
			] ) );
		}
	}

	private function getData() {
		return json_decode( file_get_contents( $this->file ), true );
	}

	private function putData( $data ) {
		file_put_contents( $this->file, json_encode( $data ) );
	}

	public function getTimer() {
		$data = $this->getData();
		return $data['timer'];
	}

	public function startTimer( $key ) {
		$data = $this->getData();
		if( array_key_exists( $key, $data['running'] ) ) {
			return;
		}

		$elapsed = $this->stopTimer();
		$this->recordSeconds( $elapsed, $this->getRunningTimers() );

		$data = $this->getData();
		$data['running'][] = $key;
		$data['timer'] = time();

		$this->putData( $data );
	}

	public function stopAllTimers() {
		$elapsed = $this->stopTimer();
		$this->recordSeconds( $elapsed, $this->getRunningTimers() );
		$data = $this->getData();
		$data['running'] = [];
		$this->putData( $data );
	}

	public function getRunningTimers() {
		$data = $this->getData();
		return $data['running'];
	}

	public function getSeconds() {
		$data = $this->getData();
		return $data['seconds'];
	}

	public function getFullMins() {
		$seconds = $this->getSeconds();
		$mins = [];
		foreach( $seconds as $key => &$second ) {
			$min = \intdiv( $second, 60 );
			if( $min > 0 ) {
				$mins[$key] = $min;
			}
		}
		return $mins;
	}

	public function removeMins( $key, $mins ) {
		$this->removeSeconds( $key, $mins * 60 );
	}

	private function removeSeconds( $key, $seconds ) {
		$data = $this->getData();
		if( !array_key_exists( $key, $data['seconds'] ) ) {
			throw new \RuntimeException( "Trying to remove seconds from a non existant item" );
		}
		if( $data['seconds'][$key] < $seconds ) {
			throw new \RuntimeException( "Trying to remove too many seconds!" );
		}
		$data['seconds'][$key] += -$seconds;
		$this->putData( $data );
	}

	private function recordSeconds( $seconds, array $timers ) {
		$numberOfTimers = count( $timers );
		if( $numberOfTimers == 0 ) {
			return;
		}

		$data = $this->getData();

		$each = \intdiv( $seconds, $numberOfTimers );
		$remainder = $seconds % $numberOfTimers;

		foreach( $timers as $timer ) {
			@$data['seconds'][$timer] += $each;
		}

		//Also add the remainder to a random emtry
		@$data['seconds'][$timers[array_rand( $timers )]] += $remainder;

		$this->putData( $data );
	}

	/**
	 * @return int elapsed timer time
	 */
	private function stopTimer() {
		$data = $this->getData();
		$startTime = $data['timer'];
		$data['timer'] = null;
		$elapsed = time() - $startTime;
		$this->putData( $data );
		return $elapsed;
	}

}