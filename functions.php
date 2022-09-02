<?php

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Asia/Baku');

function debug($arr) {
    echo '<br><br><pre>'.print_r($arr, true).'</pre>';
}


/***---------------- Tahir's Google Sheet Functions ----------------***/

/**
 * @throws \Google\Exception
 */
// Adding new registered members
function registerSheet($name, $surname, $email, $timeZone, $level)
{
    $client = new \Google_Client();
    $client->setApplicationName('Google Sheets and API');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '';

    $registerDate = date("d-M-Y");

    $range = 'Data';
    $values = [     // blank spaces and 0s added to the non-registered cells for accessibility //
        [$name, $surname, $email, $registerDate, '', $timeZone, '', $level, '', ' ', ' ', ' ', ' ', ' ']
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 'USER_ENTERED'
    ];

    return $service->spreadsheets_values->append(      // adds new user to the table //
        $spreadsheetId,
        $range,
        $body,
        $params
    );
}



// Adding new registered members
/**
 * @throws \Google\Exception
 */
function registerPayment($name, $surname, $email, $timeZone, $level, $packetType, $amount, $cp, $cpBalance): Google_Service_Sheets_AppendValuesResponse
{
    $client = new \Google_Client();
    $client->setApplicationName('Google Sheets and API');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '1ItXsuGkAi9wTS2ck-4u7z72rirqT9CwtAxqRTdoVcmY';

    $registerDate = date("d-M-Y");

    $range = 'Data';
    $values = [     // blank spaces and 0s added to the non-registered cells for accessibility //
        [$name, $surname, $email, $registerDate, '', $timeZone, '', $level, '', date("d-M-Y"), $packetType, $amount, $cp, $cpBalance]
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 'USER_ENTERED'
    ];

    return $service->spreadsheets_values->append(      // adds new user to the table //
        $spreadsheetId,
        $range,
        $body,
        $params
    );
}





// add first payment
$email = 'joe_biden@box.az';
$amount = 79;
$cp = 30;
$cpBalance = 30;
$package = 'Intensive';

/**
 * @throws \Google\Exception
 */
function addPaymentSheet($email, $amount, $cp, $package, $cpBalance) {
    $client = new \Google_Client();
    $client->setApplicationName('Google Sheets and API');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '1ItXsuGkAi9wTS2ck-4u7z72rirqT9CwtAxqRTdoVcmY';

    $range = 'Data';
    $response = $service -> spreadsheets_values -> get($spreadsheetId, $range);
    $values = $response -> getValues();

    $newValues = '';
    $newRange = '';
    $paymentDate = date("m/d/Y");

    for($i = 0; $i < count($values); ++$i){             // Finding index of the user //
        if($values[$i][2] == $email){
            $newValues = [
                $values[$i]
            ];
            $newRange = $i + 1;
        }
    }

    if($newValues) {

        $newValues[0][12] = $paymentDate;
        $newValues[0][13] = $package;
        $newValues[0][14] = $amount;
        $newValues[0][15] = $cp;
        $newValues[0][16] = $cpBalance;

        $newRange = 'Data!' . $newRange . ':' . $newRange;
        $requestBody = new Google_Service_Sheets_ValueRange([
            'values' => $newValues
        ]);
        $params = [
            'valueInputOption'=> 'USER_ENTERED'
        ];

        $service -> spreadsheets_values -> update($spreadsheetId, $newRange, $requestBody, $params);    // adds payment information to the new registered user //
    }
}




$email = 'joe_biden@box.az';
$cp = 50;
$cpBalance = 30;
$package = 'Relaxed';
$amount = 169;
function payAgainSheet($email, $cp, $cpBalance, $amount, $package){
    require __DIR__ . '/vendor/autoload.php';

    $client = new \Google_Client();
    $client -> setApplicationName('Google Sheets and API');
    $client -> setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client -> setAccessType('offline');
    $client -> setAuthConfig(__DIR__ . '/credentials.json');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '1ItXsuGkAi9wTS2ck-4u7z72rirqT9CwtAxqRTdoVcmY';

    $range = 'Sheet1';
    $response = $service -> spreadsheets_values -> get($spreadsheetId, $range);
    $values = $response -> getValues();

    $newValues = '';
    $paymentDate = date("m/d/Y");

    for($i = 0; $i < count($values); ++$i){     // finding index of the user //
        if($values[$i][2] == $email){
            $newValues = [
                $values[$i]
            ];
        }
    }

    $newValues[0][13] = $package;
    $newValues[0][15] = $cp;
    $newValues[0][16] = $cpBalance;
    $newValues[0][12] = $paymentDate;
    $newValues[0][14] = $amount;

    $newRange = 'A1';

    $body = new Google_Service_Sheets_ValueRange([
        'values'=>$newValues
    ]);

    $params = [
        'valueInputOption'=>'RAW'
    ];

    $service->spreadsheets_values->append(
        $spreadsheetId,
        $newRange,
        $body,
        $params
    );

}




/**
 * @throws \Google\Exception
 */
function updateValueSheet($email, $theCell, $theValue) {
    $cells = [];

    $client = new \Google_Client();
    $client -> setApplicationName('Google Sheets and API');
    $client -> setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client -> setAccessType('offline');
    $client -> setAuthConfig(__DIR__ . '/credentials.json');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '1ItXsuGkAi9wTS2ck-4u7z72rirqT9CwtAxqRTdoVcmY';

    $range = 'Data';
    $response = $service -> spreadsheets_values -> get($spreadsheetId, $range);
    $values = $response -> getValues();

    $newRange = 0;

    for($i = 0; $i < count($values[1]); ++$i){              // Get labels of cells //
        array_push($cells, $values[1][$i]);
    }

    $newValues = array();

    for($i = 0; $i < count($values); ++$i){                 // Find index of the email //
        if($values[$i][3] == $email){
            $newValues = [
                $values[$i]
            ];
            $newRange = $i + 1;
        }
    }

    $cellIndex = 0;

    for($i = 0; $i < count($cells); ++$i){
        if($cells[$i] == $theCell){
            $cellIndex = $i;                    // finds last record of the user //
        }
    }

    if($newValues) {

        $newValues[0][$cellIndex] = $theValue;

        $newRange = 'Data!' . $newRange . ':' . $newRange;
        $requestBody = new Google_Service_Sheets_ValueRange([
            'values'=>$newValues
        ]);
        $params = [
            'valueInputOption'=> 'USER_ENTERED'
        ];

        $service -> spreadsheets_values -> update($spreadsheetId, $newRange, $requestBody, $params);
    }
}



?>