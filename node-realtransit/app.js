var sys = require('sys');
var http = require('http');
var request = require('request');
var parser = require('xml2json');


//http://localhost/agency/stopid

var agency = 'actransit';
var stopid = '54445';

//Respond with JSON -- end server
getPredictions(stopid);

/*

 Retrieve XML
 http://www.actransit.org/rider-info/nextbus-xml-data/
 - The data provided via the XML data feed are public records, but the feed is not.
 - AC Transit contracts NextBus Inc. to provide this feed until April 2013
 - No user may execute polling commands more often than every 10 seconds.
 - http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a=actransit&stopId=<stop id>&routeTag=<route tag>

 @params - BusStop ID
 @output - BusLine, Arrival in Mins
 {
   "buslines" : [
        '57': {
            arrivals: [3,22]
        },
        'NL': {
            arrivals: [20]
        }
   ]
 }

*/
function getPredictions(stopid) {
//    request({uri:'http://webservices.nextbus.com/service/publicXMLFeed?command=predictions&a='+ agency +'&stopId='+ stopid }, function (error, response, body) {

      request({uri:'http://127.0.0.1:1337/' }, function (error, response, body) {

        if (!error && response.statusCode == 200) {

            var json = parser.toJson(body,{object: true});


            //console.log( json.body.predictions[1].direction.prediction[0].minutes ); //loop on length

            console.log( json.body.predictions[1].direction.prediction );

            var prediction = { "buslines" : [] };

            for(var i=0; i < json.body.predictions.length; i++ ) {

                if( json.body.predictions[i]["direction"] ) {

                    var busline = json.body.predictions[i]["routeTitle"];

                    var arrivals = [];

                    for(var x=0; x < json.body.predictions[i]["direction"]["prediction"].length ; x++ ) {

                        arrivals[x]  = json.body.predictions[i]["direction"]["prediction"][x]["minutes"] ;

                    }

                    //console.log( arrivals );

                    prediction.buslines.push( { "busline": busline, "arrivals" : arrivals });

                }
            }

            //console.log( prediction );


            //store(stopid,json);

        }
    })
}

// Store in Redis
// Expire after 300 seconds
function store(stopid,json) {
    client.multi([
        ["set", stopid, json, redis.print],
        ["expire", stopid, 300]
    ]).exec(function (err, replies) {
        console.log(replies);
    });
}


/*
npm install hiredis redis request xml2json
*/

// Put a message in the console verifying that the HTTP server is up and running
//console.log("Server running at http://127.0.0.1:8080/");

/*
http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('Online\n');
}).listen(1337, "127.0.0.1");
console.log('Server running at http://127.0.0.1:1337/');
*/

