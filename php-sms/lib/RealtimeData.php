<?php

 //$RT = new RealtimeData();
 //$data = $RT->getRealtimeArrival('actransit','51100');
 //$RT->formatMsg($data);

class RealtimeData {

	var $_validAgencies = array('actransit','sf-muni');
	var $_apiURL = 'http://proximobus.appspot.com';

	/**
	 * getRealtimeArrival()
	 *
	 * $param string $agency  one of the agencies from ProximoBus, e.g. 'sf-muni' or 'actransit'
	 * $param int $stopNumber a stop number in the bay area, e.g. 56833
	 * $param string $busLine a bus line, e.g. 54 (optional; will give all buses at the stop if you don't specify)
	 * @return array of next bus arrivals; e.g.
	 * array(1) {
	 *	  [54]=>
	 *	  array(2) {
	 *	    [0]=>
	 *	    float(22)
	 *	    [1]=>
	 *	    float(52)
	 *	  }
	 *	}
	 *
	 */
	function getRealtimeArrival ($agency, $stopNumber, $busLine = null) {

		// check arguments
		if (!in_array($agency, $this->_validAgencies))
			throw new Exception ('Unknown agency');

		if (!is_numeric($stopNumber))
			throw new Exception ('Invalid stop number');

		$results = $this->call("{$this->_apiURL}/agencies/{$agency}/stops/{$stopNumber}/predictions.json");

		// iterate through the results to provide next bus data
		$nextBus = array();
		foreach ($results->items as $item) {
			if (is_null($busLine) || $item->route_id == $busLine) {
				$nextBus[$item->route_id][] = $item->minutes;
			}
		}
		return $nextBus;

	}

	/**
	 * limit to 160 chars, limit to 2 arrival times
	 *
	 * @param $data
	 */
	public function formatMsg ($data) {
	    $msg = '';

        foreach($data as $route => $mins) {
            $msg .=  'Line ' . $route . " in ";
            for ($x=0; $x < 2 ; $x++) {
                $msg .= $mins[$x]." ";
            }

            $msg .= "mins / ";
        }

        $msg = substr( trim($msg), 0, -1);

        return trim($msg);
    }

	/**
	 * call(): perform the CURL call the Proximo Bus API
	 *
	 * @param $url
	 */
	private function call($url) {

		// var_dump($url);

		// use curl to make the actual call
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);

		//var_dump($data);

		$results = json_decode($data);

		// now we start handling the various error cases
		// up first: we couldn't parse the JSON results
		if (!$results) {
			throw new Exception("Could not parse results from API call. String:\n".$data);
		}

		return $results;

	}

}

?>