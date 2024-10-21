<?php

namespace EvoPhp\Actions\Notifications;

use EvoPhp\Actions\Notifications\Interfaces\TextMessageInterface;
use EvoPhp\Actions\Notifications\Handlers\NigeriaBulkSMS;

class TextMessage
{
    private $handler;

    public function __construct(TextMessageInterface | NULL $handler = NULL)
    {
        if($handler == NULL) {
            $this->handler = new NigeriaBulkSMS;
        } else {
            $this->handler = $handler;
        }
    }

    public function send($notificationObject) {
        return $this->handler->send($notificationObject);
    }

    public static function formatMobileNumber( $number, $args=null ) {
        $defaults = array(
            'countryCode' => '234', // (234) 555555555
            'localPrefix' => '0',   // (0) 555555555
            'firstDigit' => '7,8,9', // 0 (8) 55555555
            'IDD' => '00', // (00) 234 555555555 
            'localLength' => 11 // length as dialled locally e.g. 08030000000 = 11 digits
        );
        $args = array_merge( $defaults, (array) $args );
        
        // only keep digits: no spaces, dashes, plus signs, etc.
        $number = preg_replace( "/[^0-9]/", "", $number ); 
        $firstDigits = array_map(function($i) { return trim($i); }, explode(",", $args['firstDigit']));
        // Phone number is like 0555555555	
        if ( $number[0] == $args['localPrefix'] && strlen( $number ) == (int) $args['localLength'] ) {
            $number = substr( $number, strlen( $args['localPrefix'] ) );
            $result = $args['countryCode'] . $number;
        }
        // Phone number is like 555555555
        elseif($key = array_search($number[0], $firstDigits)) {
            if(strlen( $number ) == (int) $args['localLength'] - strlen( $firstDigits[$key] )) {
                $result = $args['countryCode'] . $number;
            }
        }
        // Phone number is like 00966555555555
        elseif ( substr( $number, 0, strlen( $args['IDD'] ) ) == $args['IDD'] && strlen( $number ) == (int) $args['localLength'] - strlen( $args['localPrefix'] ) + strlen( $args['IDD'] ) + strlen( $args['countryCode'] ) ) { 
            $result = substr( $number, strlen( $args['IDD'] ) );
        } 
        // Phone number is like 966555555555
        elseif ( substr( $number, 0, strlen( $args['countryCode'] ) ) == $args['countryCode'] && strlen( $number ) == (int) $args['localLength'] - strlen( $args['localPrefix'] ) + strlen( $args['countryCode'] ) ) { 
            $result = $number;
        } 
        // else omit
            
        return $result ?? $number;
    }
}
