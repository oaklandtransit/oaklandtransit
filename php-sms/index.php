<?php

require 'slim/Slim.php';
require 'lib/classes.php';

Slim::init(array(
    'mode' => 'development'
));

Slim::configureMode('development', function () {
    Slim::config(array(
        'log.enable' => true,
        'log.path' => '/Users/jose/oakland/oaklandtransit/php-sms/logs',
        'debug' => true
    ));
});

Slim::configureMode('production', function () {
    Slim::config(array(
        'log.enable' => true,
        'log.path' => '/Users/jose/oakland/oaklandtransit/php-sms/logs',
        'debug' => false
    ));
});

//Homepage
Slim::get('/', function () {
    Slim::render('home.php');
});

/* curl -d "From=+15105552069&Body=53857" http://localhost/sms */

//Main SMS router
Slim::post('/sms', function () {
    $params = Slim::request()->post(); var_dump($params);

    $user = new User( $params ); var_dump( $user );
    $intent = new Intent( trim($params["Body"]) ); var_dump($intent->path);

    if ( is_null($intent->path) ) {
        $message = "Please enter your bus stop number #";
    }
    else {
        $response = new Response( $intent );
        $message = $response->message;

        $checkin = new Checkin( $user, $response );
    }

    Slim::render('sms.php',array("message" => $message ));

});


//Leaderboard
Slim::get('/leaderboard', function () {
    $l = new Leaders();
    Slim::render('leaderboard.php', array('leaders' => $l) );
});


//Leaderboard User Page
Slim::get('/leaderboard/:user', function ($user) {
    Slim::render(
        'leaderboard.php',
        array( 'user' => $user)
    );
});


/**
 * Cache our own data
 *
    Slim::get('/actransit/:stopid', function ($stopid) {
        header("content-type: application/json");
    });

 * Split out routes
    Slim::redirect('/sms/realtime', 301);   //2pts
    Slim::redirect('/sms/board', 301);      //3pts
    Slim::redirect('/sms/feedback', 301);   //
    Slim::redirect('/sms/arrival', 301);    //5pts
 */


/**
 * Run the Slim application
 */
Slim::run();

