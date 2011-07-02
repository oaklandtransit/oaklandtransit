# The Deets

A lightweight read-only REST API that retrieves NextBus XML data and locally caches bus line data in JSON for use by the BettaSTOP application.

## Data Endpoints

URL: /{agency}/stopid

### Retrieve XML

### Parse

### Respond with JSON
    * Input - BusStop ID
    * Returns - BusLine, Arrival in Mins, (Long,Lat?)

### Store in Redis
    * Expire after 30 seconds


## Accuracy of Data
Data retrieved from the underlying NextBus API is cached by BettaSTOP. Stop Locations are cached for one day. Vehicle location and prediction data is cached for up to 30 seconds.

## Disclaimer
This API was created to provide a lightweight resource for community centered applications that require public data to improve the bus experience. Use of this data is at the risk of the user.

## Trademarks and Copyright -- incomplete --
NextBus is a trademark of [NextBus Inc.](http://NextBus.com)

The data provided by this API is from a publically available data access API provided by NextBus. The author of BettaSTOP does not claim any rights to this data, nor any right to sub-licence the data to users. Users of this API are subject to the terms and conditions of the underlying API.

The data provided by this API is copyright either NextBus itself or the respective transit agencies.


##
Heavily Inspired by [ProximoBus](http://proximobus.appspot.com/)
