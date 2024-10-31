<?php

function airtable_api_google_maps_get_longlat($address_param)
{
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode ( $address_param ) . '&sensor=false&key=' . AirtableApiAdmin::get_options('google_api_key');

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'Aircore/1.0' );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );

    $response_json = curl_exec( $ch );

    if( $curl_error_number = curl_errno( $ch ) ) {
        
        curl_close ( $ch );
        return array('error' => 'curl', 'code' => $curl_error_number);
    }

    $response = json_decode( $response_json, TRUE );

    // Grab the latitude and longitude.
    $return = array();
    $return[ 'Latitude' ] = $response["results"][0]["geometry"]["location"]["lat"];
    $return[ 'Longitude' ] = $response["results"][0]["geometry"]["location"]["lng"];

    curl_close ( $ch );

    return $return;
}

function airtable_api_distance(
    $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}