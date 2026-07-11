<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Neo Feeder Web Service Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the connection details for your Neo Feeder WS.
    | Typically Neo Feeder is hosted locally in the campus server environment.
    |
    */

    'url' => env('FEEDER_URL', 'http://localhost:8082/ws/live2.php'),
    'username' => env('FEEDER_USERNAME', '031060'), // Kode PT UMIBA
    'password' => env('FEEDER_PASSWORD', 'pddiktipassword123'),
    'actype' => env('FEEDER_ACTYPE', 'json'), // format data (json/xml)
];
