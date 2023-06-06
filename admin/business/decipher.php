<?php 

namespace Eventus\Admin\Business;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;


/**
* Decipher is a class that allows you to decipher data from FFHB API for all teams in your club.
*
* @package  Admin/Business
* @access   public
*/
class Decipher {
    /**
    * @var Decipher   $_instance  Var use to store an instance
    */
    private static $_instance;

    private $_cfk = '';

    /**
    * Returns an instance of the object
    *
    * @return Decipher
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) self::$_instance = new Decipher();
        return self::$_instance;
    }

    public function getCfk() {
        $client = new Client();
        $response = $client->get('https://www.ffhandball.fr/');
        $html = $response->getBody()->getContents();
        
        preg_match('/data-cfk="([^)]+)"/',$html,$match);

        return is_null($match) ? '' : $match[1];
    }

    /**
     * Decipher API results
     * {@link https://www.ffhandball.fr/wp-content/plugins/smartfire-blocks-project-library/build/static/js/shared/utils.ts}
     */
    public function decipher($strBase64, $key) {
        $str = base64_decode($strBase64);
        $result = '';
        $keyLen = strlen($key);
        for ($i=0; $i < strlen($str); $i++) {
            $result .= chr($this->utf8_char_code_at($str, $i) ^ $this->utf8_char_code_at($key, $i % $keyLen));
        }
        return json_decode($result, true);
    }

    /**
     * {@link https://stackoverflow.com/questions/10333098/utf-8-safe-equivalent-of-ord-or-charcodeat-in-php}
     */
    private function utf8_char_code_at($str, $index) {
        $char = mb_substr($str, $index, 1, 'UTF-8');
    
        if (mb_check_encoding($char, 'UTF-8')) {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
            return hexdec(bin2hex($ret));
        } else {
            return null;
        }
    }
}