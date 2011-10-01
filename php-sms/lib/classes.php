<?php

/**
 * Users
 *
 */
class User {

    public $phone;
    public $email;
    public $username;
    private $_id;

    public function __construct($params) {
        if ( !isset( $params["From"] ) ) {
            throw new RuntimeException('SMS requires a phone number.');
        }
        $this->phone = trim($params["From"]);
        $this->_get();
    }

    /*
    *   Retrieve user record from DB if exists, else create
    */
    private function _get() {
        $m = new Mongo();
        $collection = $m->betta->users;
        $collection->ensureIndex( array( 'phone' => 1 ) );

        $query = array( 'phone' =>  $this->phone );
        $user = $collection->findOne( $query );

        if ( !$user ) {
            $this->_create();
            $this->_get();
        } else {
            $this->_set($user);
        }
    }

    private function _create() {
        $user["phone"]  = $this->phone;
        $user["created"]= new MongoDate();

        $m = new Mongo();
        $collection = $m->betta->users;
        $collection->insert( $user );
    }

    private function _set($user) {
        if ( isset($user['email']) ) {
            $this->email = $user['email'];
        }
        if ( isset($user['username']) ) {
            $this->username = $user['username'];
        }
        if ( isset($user['created']) ) {
            $this->created = $user['created'];
        }
    }
}


/**
 * Parse Incoming Text to divine User intent
 *
 *
 *  Always assume that an sms that begins with a 5 digit stop is a realtime info request
 */
class Intent {
    public $body;
    public $nextpath;
    public $path;
    public $agency = "actransit";
    public $stopid;
    public $rawpost;
    private $_validpaths = array('realtime','board','noboard','arrival','feedback');

    public function __construct($body) {
        if ( isset($_SESSION["nextpath"]) ) {
            $this->nextpath = $_SESSION["nextpath"];
        }
        $this->body = $body;
        $this->rawpost = $_POST;
        $this->divine();
    }

    public function divine() {
        preg_match("/^[0-9]{5}/", $this->body, $matches);

        if ( isset( $matches[0] ) ) {
            $this->path = 'realtime';
            $this->stopid = $matches[0];
            return;
        }

        if ( strcmp( strtolower($this->body), '1on', 10)) {
            $this->path = 'board';
            return;
        }

        if ( strcmp( strtolower($this->body), '1off', 10)) {
            $this->path = 'noboard';
            return;
        }

        if ( strcmp( strtolower($this->body), '5', 10)) {
            $this->path = 'arrival';
            return;
        }

        if ( in_array($this->nextpath, $this->_validpaths) ) {
            $this->path = $this->nextpath;
        }
    }

}


/**
 * Record User Activity to the database
 * Award points
 */
class Checkin {
    public function __construct(User $user, Response $response ) {
        $this->user     = $user;
        $this->response = $response;

        try {
            $this->log();
        } catch (Exception $e) {
            Slim_Log::error( $e->getMessage() );
        }
    }

    public function log() {
        $m = new Mongo();
        $collection = $m->betta->checkins;
        $collection->ensureIndex( array( 'phone' => 1, 'points' => 1, "stopid" => 1 ) );

        $checkin = array(
            "phone"     => $this->user->phone,
            "points"    => $this->response->points,
            "agency"    => $this->response->intent->agency,
            "stopid"    => $this->response->intent->stopid,
            "path"      => $this->response->intent->path,
            "rawpost"   => $this->response->intent->rawpost,
            "message"   => $this->response->message,
            "ts"        => new MongoDate()
        );

        $collection->insert( $checkin );
    }
}


/**
 * SMS Responses
 * abstract the points to elsewhere
 *
 */
class Response {

    public $points;
    public $intent;
    public $message = 'There was an error. Try again.';

    public function __construct(Intent $intent) {
        $this->intent = $intent;
        $callable = $intent->path;
        return $this->$callable();
    }

    public function realtime() {
        include_once "RealtimeData.php";
        $RT   = new RealtimeData();
        $data = $RT->getRealtimeArrival($this->intent->agency, $this->intent->stopid );

        $this->points = 2;
        $this->message = $RT->formatMsg( $data ) . " +2pts text [1on] when you get on the bus - text [1off] if you can't get on";
        $_SESSION['nextpath'] = 'board';
    }

    /*
        1on
    */
    public function board() {
        $this->points = 3;
        $this->message = "+3pts Text [5] on your arrival";
        $_SESSION['nextpath'] = 'arrival';
    }

    /*
        1off
    */
    public function noboard() {
        $this->points = 3;
        $this->message = "+3pts I couldn't get on because: [1] wheel [2] bike [3] seat";
        $_SESSION['nextpath'] = 'board';
    }

    public function arrival() {
        $this->points = 5;
        $this->message = "+5pts How was your ride?";
        $_SESSION['nextpath'] = 'feedback';
    }

    public function feedback() {
        $this->points = 2;
        $this->message = "+2pts Thanks for your feedback!";
        unset($_SESSION['nextpath']);
    }

}

/**
 * Leaderboard Info
 *
 * needs to be cached to redis
 */
class Leaders {

    public $users = null;
    public $stops = null;
    public $lines = null;

    public function __construct() {
        self::tabulateusers();
        self::tabulatestops();

        $this->users = self::users();
        $this->stops = self::stops();
        $this->lines = self::lines();
    }

    private function users() {
        $m = new Mongo();
        $collection = $m->betta->topusers;
        $cursor = $collection->find()->sort( array("value" => -1));

        foreach ($cursor as $u) {
            $users[] = $u;
        }
        return $users;
    }

    private function stops() {
        $m = new Mongo();
        $collection = $m->betta->topstops;
        $cursor = $collection->find()->sort( array("value" => -1));

        foreach ($cursor as $s) {
            $stops[] = $s;
        }
        return $stops;
    }

    private function lines() {
        $d = array (
            'NL' => array('long' => '' , 'lat' => ''),
            '57' => array('long' => '' , 'lat' => ''),
            '1R' => array('long' => '' , 'lat' => '')
        );
        return $d;
    }

    /*
    * Run every 5 minutes - cron
    *
    */
    public function tabulateusers() {
        $m  = new Mongo();
        $db = $m->betta;

        $map = new MongoCode("function() { emit(this.phone,this.points); }");
        $reduce = new MongoCode("function(k, vals) { ".
        "var sum = 0;".
        "for (var i in vals) {".
            "sum += vals[i];".
        "}".
        "return sum; }");

        $points = $db->command(array(
            "mapreduce" => "checkins",
            "map"       => $map,
            "reduce"    => $reduce,
            "out"       => array("replace" => "topusers")
        ));

        $users = $db->selectCollection($points['result'])->find();
    }

    /*
    * Run every 5 minutes - cron
    *
    */
    public function tabulatestops() {
        $m  = new Mongo();
        $db = $m->betta;

        $map = new MongoCode("function() { emit(this.stopid,1); }");
        $reduce = new MongoCode("function(k, vals) { ".
        "var sum = 0;".
        "for (var i in vals) {".
            "sum += vals[i];".
        "}".
        "return sum; }");

        $stops = $db->command(array(
            "mapreduce" => "checkins",
            "map"       => $map,
            "reduce"    => $reduce,
            "out"       => array("replace" => "topstops")
        ));

        $users = $db->selectCollection($stops['result'])->find();

    }


}

