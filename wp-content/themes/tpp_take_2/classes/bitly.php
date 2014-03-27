<?php
/**
 * User: leeparsons
 * Date: 13/03/2014
 * Time: 22:47
 */
 
class Bitly {

    private $access_token = 'R_7c64e96b4f1fd644658f24ce93dca64e';

    public function shorten($url = '')
    {
        //R_7c64e96b4f1fd644658f24ce93dca64e

        $response = '';


        $url = "https://api-ssl.bitly.com/v3/shorten?access_token=" . $this->access_token . "&longUrl=" . $url;

        $handle = curl_init();

        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $handle, CURLOPT_TIMEOUT, 5 );
        curl_setopt( $handle, CURLOPT_URL, $url );
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_HEADER, false );

        $response = curl_exec( $handle );

        $errno = curl_errno( $handle );
        if ( $errno != CURLE_OK )
            throw new BitlyServiceError(curl_error( $handle ), $errno);

        curl_close( $handle );

        error_log(print_r($response, true));

        if ( !empty( $response ) )
        {
            $response = @json_decode( $response, true );
            if ( $response === null )
                throw new BitlyServiceError('JSON could not be decoded', -1);

            if ( 200 == $response['status_code'] && 'OK' == $response['status_txt'] )
                return $response;
            else
                throw new BitlyServiceError($response['status_txt'], $response['status_code']);
        }

        return false;
    }

}