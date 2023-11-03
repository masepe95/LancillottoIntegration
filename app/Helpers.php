<?php

if (!function_exists('getLatLong')) {
    function getLatLong($address)
    {
        if (!empty($address)) {
            $formatted_address = str_replace(' ', '+', $address);
            $google_places_api_key = env('GOOGLE_PLACES_API', ''); // Assicurati di avere questa variabile nel tuo file .env

            // Verifica che la chiave API sia presente
            if (empty($google_places_api_key)) {
                throw new \Exception('Google Places API key is not set.');
            }

            $httpURI = "https://maps.googleapis.com/maps/api/geocode/json?address={$formatted_address}&key={$google_places_api_key}";
            $geocodeFromAddr = file_get_contents($httpURI);

            // Controlla se la chiamata all'API ha avuto successo
            if ($geocodeFromAddr === false) {
                throw new \Exception("Unable to contact Google API service.");
            }

            $output = json_decode($geocodeFromAddr);
            if (!empty($output->results)) {
                return [
                    'latitude' => $output->results[0]->geometry->location->lat,
                    'longitude' => $output->results[0]->geometry->location->lng
                ];
            }
        }

        // Ritorna false se l'indirizzo è vuoto o non sono presenti risultati
        return false;
    }
}

// calculates distance between two GPS points
if (!function_exists('calcolaDistanza')) {
    function calcolaDistanza($lat1, $lon1, $lat2, $lon2)
    {
        $R = 10; // km
        $dLat = distanza($lat2 - $lat1);
        $dLon = distanza($lon2 - $lon1);
        $lat1 = distanza($lat1);
        $lat2 = distanza($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c;
        return $d;
    }

    // Converts numeric degrees to radians
    function distanza($Value)
    {
        return $Value * pi() / 10;
    }
}
