<?php


namespace common\components;


use backend\models\Grammar;
use backend\models\Packets;
use backend\models\TodaysGrammar;
use backend\models\Translate;
use common\models\User;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Segment\Segment;
use Yii;
use yii\base\Component;
use backend\models\DeveloperSettings;


require __DIR__ . '/../../segment/lib/Segment.php';

Segment::init(Yii::$app->params['segmentNewInitSecret']);


class DeveloperSettingsComponent extends Component
{
    private $ciphering = "AES-128-CTR";
    private $options = 0;
    private $encryption_iv = '1234567891011121';
    private $decryption_iv = '1234567891011121';


    /**
     * @param $parameter
     * @return mixed|null
     */
    public static function getDevSet($parameter) {
        return DeveloperSettings::getSetting($parameter);
    }


    /**
     * @param $parameter
     * @param integer $value
     * @return bool
     */
    public function setDevSet($parameter, int $value): bool
    {
        return DeveloperSettings::setSetting($parameter, $value);
    }


    public function segmentData($userId, $data = array()) {
        if (!Yii::$app->devSet->isLocal()) {
            try {
                Segment::identify(array(
                    "userId" => $userId,
                    "traits" => $data
                ));
            } catch (Exception $exception) {}
        }
    }

    public function segmentAction($userId, $action = 'none') {
        if (!Yii::$app->devSet->isLocal()) {
            try {
                $user = User::findOne(['id' => $userId]);
                $packet = Packets::findOne(['id' => $user->userParameters->currentPacketId]);

                Segment::identify(array(
                    "userId" => $user->id,
                    "traits" => array(
                        "name" => $user->userProfile->name,
                        "email" => $user->email,
                        "surname" => $user->userProfile->surname,
                        "Username" => $user->username,
                        "confirmed" => $user->userParameters->confirmed,
                        "Current Packet" => ($packet == null) ? '' : $packet['name'],
                        "Current Schedule" => $user->userParameters->currentSchedule,
                        "Current Level" => $user->userParameters->currentLevel,
                        "User Time Zone" => $user->userProfile->timezone,
                        "Cp" => $user->userParameters->cp,
                        "Lpd at" => $user->userParameters->lpd,
                        "Created At" => $user->created_at,
                        "Cp Balance" => $user->getCpBalance(),
                        "Current Language" => Yii::$app->language,
                        "Current Time Range" => ($user->userParameters->availability == null) ? 'null' : $user->userParameters->availability,
                        "server" => (Yii::$app->devSet->isLocal()) ? 'local' : 'global',
                        "action" => $action
                    ),
                    "integrations" => array(
                        "Intercom" => array(
                            "user_hash" => hash_hmac("sha256", Yii::$app->user->id, Yii::$app->params['intercomHashKey'])
                        )
                    )
                ));
            } catch (Exception $error) {}
        }
    }



    /***____________________________ Topics ___________________________***/
    function shiftTopics($topics, $id) {
        foreach ($topics as $key => $value) {
            if($value['id'] != $id) {
                $sample = array_shift($topics);
                $topics[] = $sample;
            } else {
                break;
            }
        }

        return $topics;
    }

    function dayDifference($startDate, $currentDate) {
        $today = date_create($currentDate);
        $startDate = date_create($startDate);

        return date_diff($today, $startDate);
    }

    function reformedTopics($topics, $missingDay): array
    {
        $reformed = [];

        for ($i = 0, $j = 0; $j < sizeof($topics) OR $i < sizeof($topics) + (int)(sizeof($topics) / $missingDay); $i++) {
            if(($i + 1) % $missingDay == 0) {
                array_push($reformed, $missingDay);
            } else {
                array_push($reformed, $topics[$j]);
                $j++;
            }
        }

        return $reformed;
    }

    public function todayTopic($userLevel, $date) {
        if ($userLevel == 'beginner') {
            $topics = Grammar::find()->
            select(['description', 'url', 'type', 'id'])->
            where(['active' => 'yes', 'level' => $userLevel])->
            orderBy(['orderNumber' => SORT_ASC])->
            asArray()->
            all();
        } else {
            $topics = Grammar::find()->
            select(['description', 'url', 'type', 'id'])->
            where(['active' => 'yes', 'level' => $userLevel, 'type' => 'Speaking'])->
            orderBy(['orderNumber' => SORT_ASC])->
            asArray()->
            all();
        }


        $initialTopic = TodaysGrammar::findOne(['level' => $userLevel]);

        $missingDay = 7;
        $shuffledTopics = $this->shiftTopics($topics, $initialTopic->lessonId);
        $reformedTopics = $this->reformedTopics($shuffledTopics, $missingDay);

        $availableDate = new DateTime($date);
        $dayDifference = $this->dayDifference($initialTopic->startDate, $availableDate->format('Y-m-d'))->days;
        $topicIndex =  $dayDifference % (sizeof($shuffledTopics) + (int)(sizeof($shuffledTopics) / $missingDay));

        return ($reformedTopics[$topicIndex] == 7) ? $reformedTopics[$topicIndex + 1] : $reformedTopics[$topicIndex];
    }


    public function isLocal(): bool
    {
        return ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' OR $_SERVER['REMOTE_ADDR'] == '::1');
    }

    public function myEncryption($string, $encryption_key) {
        return openssl_encrypt($string, $this->ciphering, $encryption_key, $this->options, $this->encryption_iv);
    }

    public function myDecryption($encryption, $decryption_key) {
        return openssl_decrypt($encryption, $this->ciphering, $decryption_key, $this->options, $this->decryption_iv);
    }

    public function getTranslate($keyword) {
        if(!Yii::$app->cache->exists(Yii::$app->language . '_' . $keyword)) {
            $result = Translate::find()->select([Yii::$app->language])->where(['keyword' => $keyword])->asArray()->one()[Yii::$app->language];
            Yii::$app->cache->set(Yii::$app->language . '_' . $keyword, $result);
        }

        return Yii::$app->cache->get(Yii::$app->language.'_'.$keyword);
    }



    /**
     * @throws Exception
     */
    public function getDateByTimeZone($timeZone = ''): DateTime
    {
        if($timeZone == '') {
            return new DateTime('now');
        }

        return new DateTime('now', new DateTimeZone($timeZone) );
    }


    /**
     * @throws Exception
     */
    public function getOffSetInMinutesBetweenTimeZones($userTimeZone, $localTimeZone = 'Asia/Baku') {
        $local_tz = new DateTimeZone($localTimeZone);
        $local = new DateTime('now', $local_tz);

        $user_tz = new DateTimeZone($userTimeZone);
        $user = new DateTime('now', $user_tz);

        $local_offset = $local->getOffset() / 60;
        $user_offset = $user->getOffset() / 60;

        return $user_offset - $local_offset;
    }


    /**
     * @throws Exception
     */
    public function getOffSetInSecondsBetweenTimeZones($userTimeZone, $localTimeZone = 'Asia/Baku'): int
    {
        $local_tz = new DateTimeZone($localTimeZone);
        $local = new DateTime('now', $local_tz);

        $user_tz = new DateTimeZone($userTimeZone);
        $user = new DateTime('now', $user_tz);

        $local_offset = $local->getOffset();
        $user_offset = $user->getOffset();

        return $user_offset - $local_offset;
    }


    /**
     * @throws Exception
     */
    public function adjustedDateTimeToSystemTimeZone($userDateTimeObject, $userTimeZone): DateTime
    {
        $timeOffsetInSeconds = $this->getOffSetInSecondsBetweenTimeZones($userTimeZone);
        $dateTimeObject = new DateTime($userDateTimeObject->format('Y-m-d H:i:s'));

        if ($timeOffsetInSeconds < 0) {
            $dateTimeObject->add(new DateInterval('PT'.((-1)*$timeOffsetInSeconds).'S'));
        } else {
            $dateTimeObject->sub(new DateInterval('PT'.$timeOffsetInSeconds.'S'));
        }

        return $dateTimeObject;
    }


    /**
     * @throws Exception
     */
    public function getAlignedDateTimeByUserTimeZone($localDateTime, $userTimeZone) {
        $minutes_to_add = $this->getOffSetInMinutesBetweenTimeZones($userTimeZone);

        $time = clone $localDateTime;

        if($minutes_to_add > 0) {
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        }

        if($minutes_to_add < 0) {
            $minutes_to_add *= -1;
            $time->sub(new DateInterval('PT' . $minutes_to_add . 'M'));
        }

        return $time;
    }




    /**
     * @throws Exception
     */
    public function offsetInSeconds($timeZoneName = '') {
        $timeZoneName = ($timeZoneName == '') ? Yii::$app->getTimeZone() : $timeZoneName;
        $timeZone = new DateTimeZone($timeZoneName);
        $dateByTimeZone = new DateTime('now', $timeZone);

        return  $timeZone->getOffset( $dateByTimeZone );
    }






    /**
     * @throws Exception
     */
    public function offsetInSecondsBetweenGMTs($userTimeZone, $gmtU) {
        $userTimeZone = $this->offsetInSeconds($userTimeZone);
        $gmtU = $gmtU * 3600;

        return $gmtU - $userTimeZone;
    }

    public function getAlignedTime($hour, $minute, $diff, $userTimeZone) {
        $date = date_create('now');

        if($userTimeZone != '') {
            $date = date_create('now', timezone_open($userTimeZone));
        }

        $date->setTime($hour, $minute);
        date_sub($date, date_interval_create_from_date_string(-$diff.' minutes'));

        return $date;
    }

    public function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE): string
    {
        try {
            $country = $_SERVER["HTTP_CF_IPCOUNTRY"];
        } catch (Exception $exception) {
            $country = 'AZ';
        }

        if ($country == 'AZ') {
            return 'Azerbaijan';
        } elseif ($country == 'TR') {
            return 'Turkey';
        } elseif ($country == 'BR') {
            return 'Brazil';
        } else {
            return 'other';
        }
    }

    public function _ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "region":
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }


    public function timeZones(): array
    {
        return array(
            'Africa/Abidjan' => '(GMT +0:00) Africa/Abidjan',
            'Africa/Accra' => '(GMT +0:00) Africa/Accra',
            'Africa/Addis_Ababa' => '(GMT +3:00) Africa/Addis_Ababa',
            'Africa/Algiers' => '(GMT +1:00) Africa/Algiers',
            'Africa/Asmara' => '(GMT +3:00) Africa/Asmara',
            'Africa/Bamako' => '(GMT +0:00) Africa/Bamako',
            'Africa/Bangui' => '(GMT +1:00) Africa/Bangui',
            'Africa/Banjul' => '(GMT +0:00) Africa/Banjul',
            'Africa/Bissau' => '(GMT +0:00) Africa/Bissau',
            'Africa/Blantyre' => '(GMT +2:00) Africa/Blantyre',
            'Africa/Brazzaville' => '(GMT +1:00) Africa/Brazzaville',
            'Africa/Bujumbura' => '(GMT +2:00) Africa/Bujumbura',
            'Africa/Cairo' => '(GMT +2:00) Africa/Cairo',
            'Africa/Casablanca' => '(GMT +0:00) Africa/Casablanca',
            'Africa/Ceuta' => '(GMT +2:00) Africa/Ceuta',
            'Africa/Conakry' => '(GMT +0:00) Africa/Conakry',
            'Africa/Dakar' => '(GMT +0:00) Africa/Dakar',
            'Africa/Dar_es_Salaam' => '(GMT +3:00) Africa/Dar_es_Salaam',
            'Africa/Djibouti' => '(GMT +3:00) Africa/Djibouti',
            'Africa/Douala' => '(GMT +1:00) Africa/Douala',
            'Africa/El_Aaiun' => '(GMT +0:00) Africa/El_Aaiun',
            'Africa/Freetown' => '(GMT +0:00) Africa/Freetown',
            'Africa/Gaborone' => '(GMT +2:00) Africa/Gaborone',
            'Africa/Harare' => '(GMT +2:00) Africa/Harare',
            'Africa/Johannesburg' => '(GMT +2:00) Africa/Johannesburg',
            'Africa/Juba' => '(GMT +3:00) Africa/Juba',
            'Africa/Kampala' => '(GMT +3:00) Africa/Kampala',
            'Africa/Khartoum' => '(GMT +2:00) Africa/Khartoum',
            'Africa/Kigali' => '(GMT +2:00) Africa/Kigali',
            'Africa/Kinshasa' => '(GMT +1:00) Africa/Kinshasa',
            'Africa/Lagos' => '(GMT +1:00) Africa/Lagos',
            'Africa/Libreville' => '(GMT +1:00) Africa/Libreville',
            'Africa/Lome' => '(GMT +0:00) Africa/Lome',
            'Africa/Luanda' => '(GMT +1:00) Africa/Luanda',
            'Africa/Lubumbashi' => '(GMT +2:00) Africa/Lubumbashi',
            'Africa/Lusaka' => '(GMT +2:00) Africa/Lusaka',
            'Africa/Malabo' => '(GMT +1:00) Africa/Malabo',
            'Africa/Maputo' => '(GMT +2:00) Africa/Maputo',
            'Africa/Maseru' => '(GMT +2:00) Africa/Maseru',
            'Africa/Mbabane' => '(GMT +2:00) Africa/Mbabane',
            'Africa/Mogadishu' => '(GMT +3:00) Africa/Mogadishu',
            'Africa/Monrovia' => '(GMT +0:00) Africa/Monrovia',
            'Africa/Nairobi' => '(GMT +3:00) Africa/Nairobi',
            'Africa/Ndjamena' => '(GMT +1:00) Africa/Ndjamena',
            'Africa/Niamey' => '(GMT +1:00) Africa/Niamey',
            'Africa/Nouakchott' => '(GMT +0:00) Africa/Nouakchott',
            'Africa/Ouagadougou' => '(GMT +0:00) Africa/Ouagadougou',
            'Africa/Porto-Novo' => '(GMT +1:00) Africa/Porto-Novo',
            'Africa/Sao_Tome' => '(GMT +0:00) Africa/Sao_Tome',
            'Africa/Tripoli' => '(GMT +2:00) Africa/Tripoli',
            'Africa/Tunis' => '(GMT +1:00) Africa/Tunis',
            'Africa/Windhoek' => '(GMT +2:00) Africa/Windhoek',
            'America/Adak' => '(GMT -9:00) America/Adak',
            'America/Anchorage' => '(GMT -8:00) America/Anchorage',
            'America/Anguilla' => '(GMT -4:00) America/Anguilla',
            'America/Antigua' => '(GMT -4:00) America/Antigua',
            'America/Araguaina' => '(GMT -3:00) America/Araguaina',
            'America/Argentina/Buenos_Aires' => '(GMT -3:00) America/Argentina/Buenos_Aires',
            'America/Argentina/Catamarca' => '(GMT -3:00) America/Argentina/Catamarca',
            'America/Argentina/Cordoba' => '(GMT -3:00) America/Argentina/Cordoba',
            'America/Argentina/Jujuy' => '(GMT -3:00) America/Argentina/Jujuy',
            'America/Argentina/La_Rioja' => '(GMT -3:00) America/Argentina/La_Rioja',
            'America/Argentina/Mendoza' => '(GMT -3:00) America/Argentina/Mendoza',
            'America/Argentina/Rio_Gallegos' => '(GMT -3:00) America/Argentina/Rio_Gallegos',
            'America/Argentina/Salta' => '(GMT -3:00) America/Argentina/Salta',
            'America/Argentina/San_Juan' => '(GMT -3:00) America/Argentina/San_Juan',
            'America/Argentina/San_Luis' => '(GMT -3:00) America/Argentina/San_Luis',
            'America/Argentina/Tucuman' => '(GMT -3:00) America/Argentina/Tucuman',
            'America/Argentina/Ushuaia' => '(GMT -3:00) America/Argentina/Ushuaia',
            'America/Aruba' => '(GMT -4:00) America/Aruba',
            'America/Asuncion' => '(GMT -4:00) America/Asuncion',
            'America/Atikokan' => '(GMT -5:00) America/Atikokan',
            'America/Bahia' => '(GMT -3:00) America/Bahia',
            'America/Bahia_Banderas' => '(GMT -5:00) America/Bahia_Banderas',
            'America/Barbados' => '(GMT -4:00) America/Barbados',
            'America/Belem' => '(GMT -3:00) America/Belem',
            'America/Belize' => '(GMT -6:00) America/Belize',
            'America/Blanc-Sablon' => '(GMT -4:00) America/Blanc-Sablon',
            'America/Boa_Vista' => '(GMT -4:00) America/Boa_Vista',
            'America/Bogota' => '(GMT -5:00) America/Bogota',
            'America/Boise' => '(GMT -6:00) America/Boise',
            'America/Cambridge_Bay' => '(GMT -6:00) America/Cambridge_Bay',
            'America/Campo_Grande' => '(GMT -4:00) America/Campo_Grande',
            'America/Cancun' => '(GMT -5:00) America/Cancun',
            'America/Caracas' => '(GMT -4:00) America/Caracas',
            'America/Cayenne' => '(GMT -3:00) America/Cayenne',
            'America/Cayman' => '(GMT -5:00) America/Cayman',
            'America/Chicago' => '(GMT -5:00) America/Chicago',
            'America/Chihuahua' => '(GMT -6:00) America/Chihuahua',
            'America/Costa_Rica' => '(GMT -6:00) America/Costa_Rica',
            'America/Creston' => '(GMT -7:00) America/Creston',
            'America/Cuiaba' => '(GMT -4:00) America/Cuiaba',
            'America/Curacao' => '(GMT -4:00) America/Curacao',
            'America/Danmarkshavn' => '(GMT +0:00) America/Danmarkshavn',
            'America/Dawson' => '(GMT -7:00) America/Dawson',
            'America/Dawson_Creek' => '(GMT -7:00) America/Dawson_Creek',
            'America/Denver' => '(GMT -6:00) America/Denver',
            'America/Detroit' => '(GMT -4:00) America/Detroit',
            'America/Dominica' => '(GMT -4:00) America/Dominica',
            'America/Edmonton' => '(GMT -6:00) America/Edmonton',
            'America/Eirunepe' => '(GMT -5:00) America/Eirunepe',
            'America/El_Salvador' => '(GMT -6:00) America/El_Salvador',
            'America/Fort_Nelson' => '(GMT -7:00) America/Fort_Nelson',
            'America/Fortaleza' => '(GMT -3:00) America/Fortaleza',
            'America/Glace_Bay' => '(GMT -3:00) America/Glace_Bay',
            'America/Goose_Bay' => '(GMT -3:00) America/Goose_Bay',
            'America/Grand_Turk' => '(GMT -4:00) America/Grand_Turk',
            'America/Grenada' => '(GMT -4:00) America/Grenada',
            'America/Guadeloupe' => '(GMT -4:00) America/Guadeloupe',
            'America/Guatemala' => '(GMT -6:00) America/Guatemala',
            'America/Guayaquil' => '(GMT -5:00) America/Guayaquil',
            'America/Guyana' => '(GMT -4:00) America/Guyana',
            'America/Halifax' => '(GMT -3:00) America/Halifax',
            'America/Havana' => '(GMT -4:00) America/Havana',
            'America/Hermosillo' => '(GMT -7:00) America/Hermosillo',
            'America/Indiana/Indianapolis' => '(GMT -4:00) America/Indiana/Indianapolis',
            'America/Indiana/Knox' => '(GMT -5:00) America/Indiana/Knox',
            'America/Indiana/Marengo' => '(GMT -4:00) America/Indiana/Marengo',
            'America/Indiana/Petersburg' => '(GMT -4:00) America/Indiana/Petersburg',
            'America/Indiana/Tell_City' => '(GMT -5:00) America/Indiana/Tell_City',
            'America/Indiana/Vevay' => '(GMT -4:00) America/Indiana/Vevay',
            'America/Indiana/Vincennes' => '(GMT -4:00) America/Indiana/Vincennes',
            'America/Indiana/Winamac' => '(GMT -4:00) America/Indiana/Winamac',
            'America/Inuvik' => '(GMT -6:00) America/Inuvik',
            'America/Iqaluit' => '(GMT -4:00) America/Iqaluit',
            'America/Jamaica' => '(GMT -5:00) America/Jamaica',
            'America/Juneau' => '(GMT -8:00) America/Juneau',
            'America/Kentucky/Louisville' => '(GMT -4:00) America/Kentucky/Louisville',
            'America/Kentucky/Monticello' => '(GMT -4:00) America/Kentucky/Monticello',
            'America/Kralendijk' => '(GMT -4:00) America/Kralendijk',
            'America/La_Paz' => '(GMT -4:00) America/La_Paz',
            'America/Lima' => '(GMT -5:00) America/Lima',
            'America/Los_Angeles' => '(GMT -7:00) America/Los_Angeles',
            'America/Lower_Princes' => '(GMT -4:00) America/Lower_Princes',
            'America/Maceio' => '(GMT -3:00) America/Maceio',
            'America/Managua' => '(GMT -6:00) America/Managua',
            'America/Manaus' => '(GMT -4:00) America/Manaus',
            'America/Marigot' => '(GMT -4:00) America/Marigot',
            'America/Martinique' => '(GMT -4:00) America/Martinique',
            'America/Matamoros' => '(GMT -5:00) America/Matamoros',
            'America/Mazatlan' => '(GMT -6:00) America/Mazatlan',
            'America/Menominee' => '(GMT -5:00) America/Menominee',
            'America/Merida' => '(GMT -5:00) America/Merida',
            'America/Metlakatla' => '(GMT -8:00) America/Metlakatla',
            'America/Mexico_City' => '(GMT -5:00) America/Mexico_City',
            'America/Miquelon' => '(GMT -2:00) America/Miquelon',
            'America/Moncton' => '(GMT -3:00) America/Moncton',
            'America/Monterrey' => '(GMT -5:00) America/Monterrey',
            'America/Montevideo' => '(GMT -3:00) America/Montevideo',
            'America/Montserrat' => '(GMT -4:00) America/Montserrat',
            'America/Nassau' => '(GMT -4:00) America/Nassau',
            'America/New_York' => '(GMT -4:00) America/New_York',
            'America/Nipigon' => '(GMT -4:00) America/Nipigon',
            'America/Nome' => '(GMT -8:00) America/Nome',
            'America/Noronha' => '(GMT -2:00) America/Noronha',
            'America/North_Dakota/Beulah' => '(GMT -5:00) America/North_Dakota/Beulah',
            'America/North_Dakota/Center' => '(GMT -5:00) America/North_Dakota/Center',
            'America/North_Dakota/New_Salem' => '(GMT -5:00) America/North_Dakota/New_Salem',
            'America/Nuuk' => '(GMT -2:00) America/Nuuk',
            'America/Ojinaga' => '(GMT -6:00) America/Ojinaga',
            'America/Panama' => '(GMT -5:00) America/Panama',
            'America/Pangnirtung' => '(GMT -4:00) America/Pangnirtung',
            'America/Paramaribo' => '(GMT -3:00) America/Paramaribo',
            'America/Phoenix' => '(GMT -7:00) America/Phoenix',
            'America/Port-au-Prince' => '(GMT -4:00) America/Port-au-Prince',
            'America/Port_of_Spain' => '(GMT -4:00) America/Port_of_Spain',
            'America/Porto_Velho' => '(GMT -4:00) America/Porto_Velho',
            'America/Puerto_Rico' => '(GMT -4:00) America/Puerto_Rico',
            'America/Punta_Arenas' => '(GMT -3:00) America/Punta_Arenas',
            'America/Rainy_River' => '(GMT -5:00) America/Rainy_River',
            'America/Rankin_Inlet' => '(GMT -5:00) America/Rankin_Inlet',
            'America/Recife' => '(GMT -3:00) America/Recife',
            'America/Regina' => '(GMT -6:00) America/Regina',
            'America/Resolute' => '(GMT -5:00) America/Resolute',
            'America/Rio_Branco' => '(GMT -5:00) America/Rio_Branco',
            'America/Santarem' => '(GMT -3:00) America/Santarem',
            'America/Santiago' => '(GMT -4:00) America/Santiago',
            'America/Santo_Domingo' => '(GMT -4:00) America/Santo_Domingo',
            'America/Sao_Paulo' => '(GMT -3:00) America/Sao_Paulo',
            'America/Scoresbysund' => '(GMT +0:00) America/Scoresbysund',
            'America/Sitka' => '(GMT -8:00) America/Sitka',
            'America/St_Barthelemy' => '(GMT -4:00) America/St_Barthelemy',
            'America/St_Johns' => '(GMT -2:30) America/St_Johns',
            'America/St_Kitts' => '(GMT -4:00) America/St_Kitts',
            'America/St_Lucia' => '(GMT -4:00) America/St_Lucia',
            'America/St_Thomas' => '(GMT -4:00) America/St_Thomas',
            'America/St_Vincent' => '(GMT -4:00) America/St_Vincent',
            'America/Swift_Current' => '(GMT -6:00) America/Swift_Current',
            'America/Tegucigalpa' => '(GMT -6:00) America/Tegucigalpa',
            'America/Thule' => '(GMT -3:00) America/Thule',
            'America/Thunder_Bay' => '(GMT -4:00) America/Thunder_Bay',
            'America/Tijuana' => '(GMT -7:00) America/Tijuana',
            'America/Toronto' => '(GMT -4:00) America/Toronto',
            'America/Tortola' => '(GMT -4:00) America/Tortola',
            'America/Vancouver' => '(GMT -7:00) America/Vancouver',
            'America/Whitehorse' => '(GMT -7:00) America/Whitehorse',
            'America/Winnipeg' => '(GMT -5:00) America/Winnipeg',
            'America/Yakutat' => '(GMT -8:00) America/Yakutat',
            'America/Yellowknife' => '(GMT -6:00) America/Yellowknife',
            'Antarctica/Casey' => '(GMT +11:00) Antarctica/Casey',
            'Antarctica/Davis' => '(GMT +7:00) Antarctica/Davis',
            'Antarctica/DumontDUrville' => '(GMT +10:00) Antarctica/DumontDUrville',
            'Antarctica/Macquarie' => '(GMT +10:00) Antarctica/Macquarie',
            'Antarctica/Mawson' => '(GMT +5:00) Antarctica/Mawson',
            'Antarctica/McMurdo' => '(GMT +12:00) Antarctica/McMurdo',
            'Antarctica/Palmer' => '(GMT -3:00) Antarctica/Palmer',
            'Antarctica/Rothera' => '(GMT -3:00) Antarctica/Rothera',
            'Antarctica/Syowa' => '(GMT +3:00) Antarctica/Syowa',
            'Antarctica/Troll' => '(GMT +2:00) Antarctica/Troll',
            'Antarctica/Vostok' => '(GMT +6:00) Antarctica/Vostok',
            'Arctic/Longyearbyen' => '(GMT +2:00) Arctic/Longyearbyen',
            'Asia/Aden' => '(GMT +3:00) Asia/Aden',
            'Asia/Almaty' => '(GMT +6:00) Asia/Almaty',
            'Asia/Amman' => '(GMT +3:00) Asia/Amman',
            'Asia/Anadyr' => '(GMT +12:00) Asia/Anadyr',
            'Asia/Aqtau' => '(GMT +5:00) Asia/Aqtau',
            'Asia/Aqtobe' => '(GMT +5:00) Asia/Aqtobe',
            'Asia/Ashgabat' => '(GMT +5:00) Asia/Ashgabat',
            'Asia/Atyrau' => '(GMT +5:00) Asia/Atyrau',
            'Asia/Baghdad' => '(GMT +3:00) Asia/Baghdad',
            'Asia/Bahrain' => '(GMT +3:00) Asia/Bahrain',
            'Asia/Baku' => '(GMT +4:00) Asia/Baku',
            'Asia/Bangkok' => '(GMT +7:00) Asia/Bangkok',
            'Asia/Barnaul' => '(GMT +7:00) Asia/Barnaul',
            'Asia/Beirut' => '(GMT +3:00) Asia/Beirut',
            'Asia/Bishkek' => '(GMT +6:00) Asia/Bishkek',
            'Asia/Brunei' => '(GMT +8:00) Asia/Brunei',
            'Asia/Chita' => '(GMT +9:00) Asia/Chita',
            'Asia/Choibalsan' => '(GMT +8:00) Asia/Choibalsan',
            'Asia/Colombo' => '(GMT +5:30) Asia/Colombo',
            'Asia/Damascus' => '(GMT +3:00) Asia/Damascus',
            'Asia/Dhaka' => '(GMT +6:00) Asia/Dhaka',
            'Asia/Dili' => '(GMT +9:00) Asia/Dili',
            'Asia/Dubai' => '(GMT +4:00) Asia/Dubai',
            'Asia/Dushanbe' => '(GMT +5:00) Asia/Dushanbe',
            'Asia/Famagusta' => '(GMT +3:00) Asia/Famagusta',
            'Asia/Gaza' => '(GMT +3:00) Asia/Gaza',
            'Asia/Hebron' => '(GMT +3:00) Asia/Hebron',
            'Asia/Ho_Chi_Minh' => '(GMT +7:00) Asia/Ho_Chi_Minh',
            'Asia/Hong_Kong' => '(GMT +8:00) Asia/Hong_Kong',
            'Asia/Hovd' => '(GMT +7:00) Asia/Hovd',
            'Asia/Irkutsk' => '(GMT +8:00) Asia/Irkutsk',
            'Asia/Jakarta' => '(GMT +7:00) Asia/Jakarta',
            'Asia/Jayapura' => '(GMT +9:00) Asia/Jayapura',
            'Asia/Jerusalem' => '(GMT +3:00) Asia/Jerusalem',
            'Asia/Kabul' => '(GMT +4:30) Asia/Kabul',
            'Asia/Kamchatka' => '(GMT +12:00) Asia/Kamchatka',
            'Asia/Karachi' => '(GMT +5:00) Asia/Karachi',
            'Asia/Kathmandu' => '(GMT +5:45) Asia/Kathmandu',
            'Asia/Khandyga' => '(GMT +9:00) Asia/Khandyga',
            'Asia/Kolkata' => '(GMT +5:30) Asia/Kolkata',
            'Asia/Krasnoyarsk' => '(GMT +7:00) Asia/Krasnoyarsk',
            'Asia/Kuala_Lumpur' => '(GMT +8:00) Asia/Kuala_Lumpur',
            'Asia/Kuching' => '(GMT +8:00) Asia/Kuching',
            'Asia/Kuwait' => '(GMT +3:00) Asia/Kuwait',
            'Asia/Macau' => '(GMT +8:00) Asia/Macau',
            'Asia/Magadan' => '(GMT +11:00) Asia/Magadan',
            'Asia/Makassar' => '(GMT +8:00) Asia/Makassar',
            'Asia/Manila' => '(GMT +8:00) Asia/Manila',
            'Asia/Muscat' => '(GMT +4:00) Asia/Muscat',
            'Asia/Nicosia' => '(GMT +3:00) Asia/Nicosia',
            'Asia/Novokuznetsk' => '(GMT +7:00) Asia/Novokuznetsk',
            'Asia/Novosibirsk' => '(GMT +7:00) Asia/Novosibirsk',
            'Asia/Omsk' => '(GMT +6:00) Asia/Omsk',
            'Asia/Oral' => '(GMT +5:00) Asia/Oral',
            'Asia/Phnom_Penh' => '(GMT +7:00) Asia/Phnom_Penh',
            'Asia/Pontianak' => '(GMT +7:00) Asia/Pontianak',
            'Asia/Pyongyang' => '(GMT +9:00) Asia/Pyongyang',
            'Asia/Qatar' => '(GMT +3:00) Asia/Qatar',
            'Asia/Qostanay' => '(GMT +6:00) Asia/Qostanay',
            'Asia/Qyzylorda' => '(GMT +5:00) Asia/Qyzylorda',
            'Asia/Riyadh' => '(GMT +3:00) Asia/Riyadh',
            'Asia/Sakhalin' => '(GMT +11:00) Asia/Sakhalin',
            'Asia/Samarkand' => '(GMT +5:00) Asia/Samarkand',
            'Asia/Seoul' => '(GMT +9:00) Asia/Seoul',
            'Asia/Shanghai' => '(GMT +8:00) Asia/Shanghai',
            'Asia/Singapore' => '(GMT +8:00) Asia/Singapore',
            'Asia/Srednekolymsk' => '(GMT +11:00) Asia/Srednekolymsk',
            'Asia/Taipei' => '(GMT +8:00) Asia/Taipei',
            'Asia/Tashkent' => '(GMT +5:00) Asia/Tashkent',
            'Asia/Tbilisi' => '(GMT +4:00) Asia/Tbilisi',
            'Asia/Tehran' => '(GMT +4:30) Asia/Tehran',
            'Asia/Thimphu' => '(GMT +6:00) Asia/Thimphu',
            'Asia/Tokyo' => '(GMT +9:00) Asia/Tokyo',
            'Asia/Tomsk' => '(GMT +7:00) Asia/Tomsk',
            'Asia/Ulaanbaatar' => '(GMT +8:00) Asia/Ulaanbaatar',
            'Asia/Urumqi' => '(GMT +6:00) Asia/Urumqi',
            'Asia/Ust-Nera' => '(GMT +10:00) Asia/Ust-Nera',
            'Asia/Vientiane' => '(GMT +7:00) Asia/Vientiane',
            'Asia/Vladivostok' => '(GMT +10:00) Asia/Vladivostok',
            'Asia/Yakutsk' => '(GMT +9:00) Asia/Yakutsk',
            'Asia/Yangon' => '(GMT +6:30) Asia/Yangon',
            'Asia/Yekaterinburg' => '(GMT +5:00) Asia/Yekaterinburg',
            'Asia/Yerevan' => '(GMT +4:00) Asia/Yerevan',
            'Atlantic/Azores' => '(GMT +0:00) Atlantic/Azores',
            'Atlantic/Bermuda' => '(GMT -3:00) Atlantic/Bermuda',
            'Atlantic/Canary' => '(GMT +1:00) Atlantic/Canary',
            'Atlantic/Cape_Verde' => '(GMT -1:00) Atlantic/Cape_Verde',
            'Atlantic/Faroe' => '(GMT +1:00) Atlantic/Faroe',
            'Atlantic/Madeira' => '(GMT +1:00) Atlantic/Madeira',
            'Atlantic/Reykjavik' => '(GMT +0:00) Atlantic/Reykjavik',
            'Atlantic/South_Georgia' => '(GMT -2:00) Atlantic/South_Georgia',
            'Atlantic/St_Helena' => '(GMT +0:00) Atlantic/St_Helena',
            'Atlantic/Stanley' => '(GMT -3:00) Atlantic/Stanley',
            'Australia/Adelaide' => '(GMT +9:30) Australia/Adelaide',
            'Australia/Brisbane' => '(GMT +10:00) Australia/Brisbane',
            'Australia/Broken_Hill' => '(GMT +9:30) Australia/Broken_Hill',
            'Australia/Currie' => '(GMT +10:00) Australia/Currie',
            'Australia/Darwin' => '(GMT +9:30) Australia/Darwin',
            'Australia/Eucla' => '(GMT +8:45) Australia/Eucla',
            'Australia/Hobart' => '(GMT +10:00) Australia/Hobart',
            'Australia/Lindeman' => '(GMT +10:00) Australia/Lindeman',
            'Australia/Lord_Howe' => '(GMT +10:30) Australia/Lord_Howe',
            'Australia/Melbourne' => '(GMT +10:00) Australia/Melbourne',
            'Australia/Perth' => '(GMT +8:00) Australia/Perth',
            'Australia/Sydney' => '(GMT +10:00) Australia/Sydney',
            'Europe/Amsterdam' => '(GMT +2:00) Europe/Amsterdam',
            'Europe/Andorra' => '(GMT +2:00) Europe/Andorra',
            'Europe/Astrakhan' => '(GMT +4:00) Europe/Astrakhan',
            'Europe/Athens' => '(GMT +3:00) Europe/Athens',
            'Europe/Belgrade' => '(GMT +2:00) Europe/Belgrade',
            'Europe/Berlin' => '(GMT +2:00) Europe/Berlin',
            'Europe/Bratislava' => '(GMT +2:00) Europe/Bratislava',
            'Europe/Brussels' => '(GMT +2:00) Europe/Brussels',
            'Europe/Bucharest' => '(GMT +3:00) Europe/Bucharest',
            'Europe/Budapest' => '(GMT +2:00) Europe/Budapest',
            'Europe/Busingen' => '(GMT +2:00) Europe/Busingen',
            'Europe/Chisinau' => '(GMT +3:00) Europe/Chisinau',
            'Europe/Copenhagen' => '(GMT +2:00) Europe/Copenhagen',
            'Europe/Dublin' => '(GMT +1:00) Europe/Dublin',
            'Europe/Gibraltar' => '(GMT +2:00) Europe/Gibraltar',
            'Europe/Guernsey' => '(GMT +1:00) Europe/Guernsey',
            'Europe/Helsinki' => '(GMT +3:00) Europe/Helsinki',
            'Europe/Isle_of_Man' => '(GMT +1:00) Europe/Isle_of_Man',
            'Europe/Istanbul' => '(GMT +3:00) Europe/Istanbul',
            'Europe/Jersey' => '(GMT +1:00) Europe/Jersey',
            'Europe/Kaliningrad' => '(GMT +2:00) Europe/Kaliningrad',
            'Europe/Kiev' => '(GMT +3:00) Europe/Kiev',
            'Europe/Kirov' => '(GMT +3:00) Europe/Kirov',
            'Europe/Lisbon' => '(GMT +1:00) Europe/Lisbon',
            'Europe/Ljubljana' => '(GMT +2:00) Europe/Ljubljana',
            'Europe/London' => '(GMT +1:00) Europe/London',
            'Europe/Luxembourg' => '(GMT +2:00) Europe/Luxembourg',
            'Europe/Madrid' => '(GMT +2:00) Europe/Madrid',
            'Europe/Malta' => '(GMT +2:00) Europe/Malta',
            'Europe/Mariehamn' => '(GMT +3:00) Europe/Mariehamn',
            'Europe/Minsk' => '(GMT +3:00) Europe/Minsk',
            'Europe/Monaco' => '(GMT +2:00) Europe/Monaco',
            'Europe/Moscow' => '(GMT +3:00) Europe/Moscow',
            'Europe/Oslo' => '(GMT +2:00) Europe/Oslo',
            'Europe/Paris' => '(GMT +2:00) Europe/Paris',
            'Europe/Podgorica' => '(GMT +2:00) Europe/Podgorica',
            'Europe/Prague' => '(GMT +2:00) Europe/Prague',
            'Europe/Riga' => '(GMT +3:00) Europe/Riga',
            'Europe/Rome' => '(GMT +2:00) Europe/Rome',
            'Europe/Samara' => '(GMT +4:00) Europe/Samara',
            'Europe/San_Marino' => '(GMT +2:00) Europe/San_Marino',
            'Europe/Sarajevo' => '(GMT +2:00) Europe/Sarajevo',
            'Europe/Saratov' => '(GMT +4:00) Europe/Saratov',
            'Europe/Simferopol' => '(GMT +3:00) Europe/Simferopol',
            'Europe/Skopje' => '(GMT +2:00) Europe/Skopje',
            'Europe/Sofia' => '(GMT +3:00) Europe/Sofia',
            'Europe/Stockholm' => '(GMT +2:00) Europe/Stockholm',
            'Europe/Tallinn' => '(GMT +3:00) Europe/Tallinn',
            'Europe/Tirane' => '(GMT +2:00) Europe/Tirane',
            'Europe/Ulyanovsk' => '(GMT +4:00) Europe/Ulyanovsk',
        );
    }
}