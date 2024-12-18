<?php 

namespace EvoPhp\Api;

use EvoPhp\Database\Query;
use EvoPhp\Resources\User;
use EvoPhp\Database\Session;

class Operations
{
    use Auth;

    public function __construct()
    {
        
    }

    public function __call($method, $args) {
        if(method_exists($this, $method)) {
            return $this->$method($args[0]);
        }
    }

    /**
     * setting the property of the api
     */
    public function __set($property, $value) {
    	$this->$property = $value;
    }

    /**
     * getting the property of the api
     */
    public function __get($property) {
    	return $this->$property;
    }
    
    /**
     * removeslashes
     * 
     * a static method that takes a string as an argument, 
     * replaces some characters and then strips any remaining slashes 
     * from the string and returns it.
     * @param  string $string
     * @return string
     */
    static public function removeslashes($string)
    {
        $string=str_replace("\\n", "<br/>", $string);
        $string=str_replace("\\r", "", $string);
        $string=implode("",explode("\\",$string));
        return stripslashes(trim($string));
    }
    
    /**
     * sanitize
     *
     * a static method that takes a mixed input, 
     * and a boolean for allowing HTML and an object for query, 
     * it then returns the output after sanitizing it.
     * @param  mixed $input
     * @param  boolean $allowHtml optional
     * @param  object $Query optional
     * @return string
     */
    static public function sanitize($input, $allowHtml = true, $Query = false) {

        if(!$Query)
            $Query = new Query;

        if (is_array($input)) {
            $output = [];
            foreach($input as $var=>$val) {
                $output[$var] = self::sanitize($val, $allowHtml, $Query);
            }
        }
        elseif (gettype($input) === "string") {
            $output = $Query->_real_escape( $input );
        } else $output = $input;
        return $output;
    }

        
    /**
     * replaceLine
     *
     * a static method that takes a string url , 
     * search and replace string , reads an array of lines from the file at $url, 
     * then it replaces any line that contains $search with $replace, 
     * then write the data back to the file at $url
     * @param  mixed $url
     * @param  mixed $search
     * @param  mixed $replace
     * @return void
     */
    static public function replaceLine($url, $search, $replace) {
        $data = file($url); // reads an array of lines
        $data = array_map(function($data) use ($search, $replace) {
            return stristr($data, $search) ? $replace : $data;
        }, $data);
        file_put_contents($url, implode('', $data));
    }
    
    /**
     * is_serialized
     *
     * a static method that takes a variable and verifies whether a variable is a serialized string.
     * @param  mixed $data
     * @param  mixed $strict
     * @return void
     */
    static public function is_serialized( $data, $strict = true ) {
        // if it isn't a string, it isn't serialized.
        if ( ! is_string( $data ) ) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' == $data ) {
            return true;
        }
        if ( strlen( $data ) < 4 ) {
            return false;
        }
        if ( ':' !== $data[1] ) {
            return false;
        }
        if ( $strict ) {
            $lastc = substr( $data, -1 );
            if ( ';' !== $lastc && '}' !== $lastc ) {
                return false;
            }
        } else {
            $semicolon = strpos( $data, ';' );
            $brace     = strpos( $data, '}' );
            // Either ; or } must exist.
            if ( false === $semicolon && false === $brace ) {
                return false;
            }
            // But neither must be in the first X characters.
            if ( false !== $semicolon && $semicolon < 3 ) {
                return false;
            }
            if ( false !== $brace && $brace < 4 ) {
                return false;
            }
        }
        $token = $data[0];
        switch ( $token ) {
            case 's':
                if ( $strict ) {
                    if ( '"' !== substr( $data, -2, 1 ) ) {
                        return false;
                    }
                } elseif ( false === strpos( $data, '"' ) ) {
                    return false;
                }
                // or else fall through
            case 'a':
            case 'O':
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
        }
        return false;
    }

    static public function serialize($array) {
        $str = serialize($array);
        return base64_encode($str);
    }

    static public function unserialize($str) {
        if(!self::is_serialized($str)) {
            $str = base64_decode($str);
        }
        return self::is_serialized($str) ? unserialize($str) : array();
    }

    static public function count($array) {
        return is_array($array) ? count($array) : 0;
    }

    static public function toCamelCase($string)
    {
        $result = strtolower($string);
            
        preg_match_all('/_[a-z]/', $result, $matches);

        foreach($matches[0] as $match)
        {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    static public function validatePhoneNumber($phone, $min = 10, $max = 14)
    {
        // Allow +, - and . in phone number
        $filteredPhoneNumber = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phoneToCheck = str_replace("-", "", $filteredPhoneNumber);
        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phoneToCheck) < $min || strlen($phoneToCheck) > $max) {
            return false;
        } else {
            return true;
        }
    }

    static public function getFullname($user, string $order = "SMO") {
        $fullname = "";
        if(gettype($user) == "object") {}
        else if(gettype($user) == "array") {
            $user = (object) $user;
        } else {
            $u = new User;
            $user = $u->get($user);
        }
        if(!$user) return $fullname;
        $order = str_split($order);
        if(Operations::count($order)):
            foreach($order as $o):
                switch($o) {
                    case "S":
                        $fullname .= $user->surname ?? "";
                        break;

                    case "M":
                        $fullname .= $user->middle_name ?? "";
                        break;
                    
                    case "O":
                        $fullname .= $user->other_names ?? "";
                        break;

                    case "T":
                        $fullname .= $user->title ?? "";
                        break;

                    default:
                        $fullname .= "";
                        break;
                }
                $fullname .= " ";
            endforeach;
        endif;
        return trim($fullname);
    }

    static public function internationalizePhoneNumber($phone_number, $country_code = '') {
        $test = explode(",", $phone_number);
        if(self::count($test) > 1) {
            $test = self::trimArray($test);
            $res = "";
            foreach ($test as $number) {
                $res .= self::internationalizePhoneNumber($number, $country_code).",";
            }
            $res = substr($res, 0, -1);
            return $res;
        }
        //Remove any parentheses and the numbers they contain:
        $phone_number = preg_replace("/\([0-9]+?\)/", "", $phone_number);
    
        //Remove plus signs in country code
        $country_code = str_replace("+", "", $country_code);
    
        //Strip spaces and non-numeric characters:
        $phone_number = preg_replace("/[^0-9]/", "", $phone_number);
    
        //Strip out leading zeros:
        $phone_number = ltrim($phone_number, '0');
    
        if ( !preg_match('/^'.$country_code.'/', $phone_number)  ) {
            $phone_number = $country_code.$phone_number;
        }
    
        return $phone_number;
    }

    static public function trimArray($input) {
        $res = [];
        if(self::count($input) > 0 && $input) {
            foreach ($input as $key => $value) {
                $res[trim($key)] = trim($value);
            }
        }
        return $res;
    }

    static public function randomString(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    static public function getInnerContents($str, $startDelimiter, $endDelimiter) {
        $contents = array();
        $startDelimiterLength = @strlen($startDelimiter);
        $endDelimiterLength = @strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = @strpos($str, $startDelimiter, $startFrom))) {
          $contentStart += $startDelimiterLength;
          $contentEnd = @strpos($str, $endDelimiter, $contentStart);
          if (false === $contentEnd) {
            break;
          }
          $contents[] = @substr($str, $contentStart, $contentEnd - $contentStart);
          $startFrom = $contentEnd + $endDelimiterLength;
        }
      
        return $contents;
    }

    static public function doAction($action_name, ...$args) {
        \EvoPhp\Actions\Action::do($action_name, ...$args);
    }

    static public function applyFilters($filter, $var, ...$args) {
        return \EvoPhp\Actions\Filter::apply($filter, $var, ...$args);
    }

    static public function checkAccess(array | string $scope) {
        $instance = new self;
        if(is_string($scope)) {
            if(trim($scope) == "") {
                $scope = [];
            } else {
                $scope = explode(",", $scope);
                $scope = $instance::trimArray($scope);
            }
        }
        $instance->accessLevel = $scope;
        return $instance->getAuthorization();
    }

    static public function varExport($expression, $return=FALSE) {
        $export = \var_export($expression, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
            "/,\s+]/" => ']'
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool)$return) return $export; else echo $export;
    }

    static public function callAPI($url, $data = [], $method = "POST", $headers = "APIKEY: 111111111111111111111", $contentType = "application/json"){
        $curl = curl_init();
        if(gettype($headers) == 'string') {
            $headers = [$headers];
        } 
        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: '.$contentType,
            ...$headers
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        $err = curl_error($curl);
        if(!$result){
            if($method == "GET") {
                $config = new Config;
                $arrDev=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                    "http" => array(
                        "method" => "GET",
                        "header" => $headers
                    )
                );
                $arrLive=array(
                    "http" => array(
                        "method" => "GET",
                        "header" => $headers
                    )
                );
                $context = $config->mode == "development" ? stream_context_create($arrDev) : stream_context_create($arrLive);
                try {
                    $result = file_get_contents($url, false, $context);
                    if(!$result) {
                        http_response_code(522);
                        throw new \Exception("Connection failure");
                    }
                } catch(\Exception $e) {
                    echo "cURL Error #:" . $err ."<br/>";
                    echo "file_get_contents Error #:". $e->getMessage();
                }
                
            } else {
                http_response_code(522);
                die("Connection Failure");
                return;
            }
        }
        curl_close($curl);
        return json_decode($result);
    }

    static public function currencyConverter($currency, $base = "USD", $amount = 1) {
        $config = new Config;

        $convert = function($data, $currency, $base) use ($amount){
            $data = (array) $data;
            if(isset($data[$base]) && isset($data[$currency])) {
                $rateToBase = $data[$base];
                $rateToCurrency = $data[$currency];
                $ret = ($rateToCurrency/$rateToBase) * $amount;
                return $ret;
            } else return false;
        };

        $getFromServer = function($currency, $base, $endPoint, $rateKey, $refreshes) use ($convert) {
            if($res = self::callAPI($endPoint, [], "GET")) {
                if($res->result == 'success') {
                    if(isset($res->$rateKey)) {
                        $data = (object) [
                            "rates" => $res->$rateKey,
                            "expiry" => (time() + ($refreshes * 60 * 60)) * 1000
                        ];
                        \EvoPhp\Resources\Records::update("exchange_rates", $data);
                        return $convert($res->$rateKey, $currency, $base);
                    } else return false;
                } else return false;
            }
        };

        $endPoint = $config->currency['endPoint'] ?? "https://api.exchangerate.host/latest";
        $apiKey = $config->currency['apiKey'] ?? "";
        $rateKey = $config->currency['rateKey'] ?? "rates";
        $refreshes = $config->currency['refreshes'] ?? 1;

        $endPoint = str_replace(["{key}", "{base}"], [$apiKey, $base], $endPoint);
        $exrates = \EvoPhp\Resources\Records::get("exchange_rates");
        
        if($exrates) {
            $exrates = (array) $exrates;
            if((time() * 1000) > $exrates['expiry']) {
                return $getFromServer($currency, $base, $endPoint, $rateKey, $refreshes);
            }
            return $convert($exrates['rates'], $currency, $base);
        }
        return $getFromServer($currency, $base, $endPoint, $rateKey, $refreshes);
    }

    public static function getIndex($meta = false, $cache = true) {
        $session = Session::getInstance();
        if(isset($session->index) && $cache) {
            return $session->index;
        }
        if(!$meta) {
            $u = $session->getResourceOwner();
            if(!$u) return "/";
            $user = new User;
            $meta = $user->get($u->user_id);
        }
        $config = new Config;
        if(isset($config->Auth["roles"][$meta->role])) {
            if(isset($config->Auth["roles"][$meta->role]["index"])) {
                $session->index = $config->Auth["roles"][$meta->role]["index"];
                return $session->index;
            }
        }
        $session->index = "/";
        return $session->index;
    }

    public static function testAccess($password) {
        $session = Session::getInstance();
        $id = $session->getResourceOwner()->user_id;
        $user = new User;
        $meta = $user->get($id);
        return self::encrypt($password) == $meta->password;
    }

    public static function fullProtocol() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        // Get the host
        $host = $_SERVER['HTTP_HOST'];

        // Combine protocol and host
        return $protocol . $host;
    }

    /**
     * Generate an array of objects with first and last dates of previous months, including the current month.
     *
     * @param int $monthsBack Number of months to include (default is 12, current month inclusive).
     * @return array An array of objects with 'first_date' and 'last_date'.
     */
    public static function getPreviousMonthsDates($monthsBack = 12) {
        $currentMonth = new \DateTime(); // Current date
        $months = []; // Array to hold the date objects

        for ($i = 0; $i < $monthsBack; $i++) {
            // Clone the current month and subtract $i months (includes current month at i=0)
            $date = (clone $currentMonth)->modify("-$i months");

            // Get the first and last days of the month
            $firstDay = $date->format('Y-m-01');
            $lastDay = $date->format('Y-m-t');

            // Month and Year Name (e.g., June 2024)
            $monthYear = $date->format('F Y');

            // Add to the months array as an object
            $months[] = (object) [
                'first_date' => $firstDay,
                'last_date'  => $lastDay,
                'month_year' => $monthYear
            ];
        }

        return $months;
    }
}