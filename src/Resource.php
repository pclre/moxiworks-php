<?php


namespace MoxiworksPlatform;

use MoxiworksPlatform\Exception\RemoteRequestFailureException;


class Resource {

    public static function headers() {
        $headers = array (
            'Authorization' => static::authHeader(),
            'Accept' => static::acceptHeader(),
            'Content-Type' => static::contentTypeHeader()
        );
        return $headers;
    }

    public static function authHeader() {
        if (!Credentials::ready())
            throw new Exception\AuthorizationException('MoxiworksPlatform\Credentials must be set before using');
        $identifier = Credentials::identifier();
        $secret = Credentials::secret();
        $auth_string = base64_encode("$identifier:$secret");
        return "Basic $auth_string";
    }

    public static function acceptHeader() {
        return 'application/vnd.moxi-platform+json;version=1';

    }

    public static function contentTypeHeader() {
        return 'application/x-www-form-urlencoded';
    }

    public static function checkForErrorInResponse($json) {
        $message = (is_array($json) && key_exists('messages', $json) && is_array($json['messages'])) ?
            implode(',', $json['messages']) :
            "unable to perform remote action on Moxi Works platform\n";

        if (!is_array($json) || (key_exists('status', $json) && ($json['status'] == 'fail' || $json['status'] == 'error' ))) {
            throw new RemoteRequestFailureException($message);
        }
        return true;
    }

}