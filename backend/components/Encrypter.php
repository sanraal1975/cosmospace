<?php

class Encrypter {
 
    private static $Key = "wBQrDnWhGdpkn5l92uKPfWzixlhbbyxBoyh5K8PJME5Xci8qKMXoCXoWT5tarJE7Z8qdJnAJKEAiJkKwWBcYduUhtkww13OMBcsa";
 
    public static function encrypt($input) 
    {
        $output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Encrypter::$Key), $input, MCRYPT_MODE_CBC, md5(md5(Encrypter::$Key))));
        return $output;
    }
 
    public static function decrypt($input) 
    {
        $output = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(Encrypter::$Key), base64_decode($input), MCRYPT_MODE_CBC, md5(md5(Encrypter::$Key))), "\0");
        return $output;
    }
 
}
?>