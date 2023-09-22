<?php
/**
 * This file contains classes which allow tracks to be
 * encrypted and decrypted
 * @package Com.Kaleidovision.MusicPortal
 * @subpackage Com.Kaleidovision.MusicPortal.TrackEncryption
 */

namespace Com\Kaleidovision\MusicPortal;

class TrackEncryption {
    private $__key = array(
        15, 13, 14, 12, 7, 5, 6, 4, 11, 9, 10, 8, 3, 1, 2, 0,
        47, 45, 46, 44, 39, 37, 38, 36, 43, 41, 42, 40, 35, 33, 34, 32,
        31, 29, 30, 28, 23, 21, 22, 20, 27, 25, 26, 24, 19, 17, 18, 16,
        63, 61, 62, 60, 55, 53, 54, 52, 59, 57, 58, 56, 51, 49, 50, 48,
        143, 141, 142, 140, 135, 133, 134, 132, 139, 137, 138, 136, 131, 129, 130, 128,
        175, 173, 174, 172, 167, 165, 166, 164, 171, 169, 170, 168, 163, 161, 162, 160,
        159, 157, 158, 156, 151, 149, 150, 148, 155, 153, 154, 152, 147, 145, 146, 144,
        191, 189, 190, 188, 183, 181, 182, 180, 187, 185, 186, 184, 179, 177, 178, 176,
        79, 77, 78, 76, 71, 69, 70, 68, 75, 73, 74, 72, 67, 65, 66, 64,
        111, 109, 110, 108, 103, 101, 102, 100, 107, 105, 106, 104, 99, 97, 98, 96,
        95, 93, 94, 92, 87, 85, 86, 84, 91, 89, 90, 88, 83, 81, 82, 80,
        127, 125, 126, 124, 119, 117, 118, 116, 123, 121, 122, 120, 115, 113, 114, 112,
        207, 205, 206, 204, 199, 197, 198, 196, 203, 201, 202, 200, 195, 193, 194, 192,
        239, 237, 238, 236, 231, 229, 230, 228, 235, 233, 234, 232, 227, 225, 226, 224,
        223, 221, 222, 220, 215, 213, 214, 212, 219, 217, 218, 216, 211, 209, 210, 208,
        255, 253, 254, 252, 247, 245, 246, 244, 251, 249, 250, 248, 243, 241, 242, 240 
    );
    
    private $__encrypted;
    private $__buffer;
    private $__fileSize;
    private $__decrypted;
    public $blocks = 8192;
    
    public function decryptAndStream($input = '', $httpRange = '') {
        if(!file_exists($input)) exit;
        
        $this->__encrypted = fopen($input, 'rb');
        $this->__fileSize = filesize($input);
        
        // Partial content (iPhone compatibility)
        $start = $end = $length = 0;
        if(!empty($httpRange)) {
            list(, $range) = explode('=', $httpRange, 2);
            $range = explode('-', $range);
            $start = $range[0];
            $end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            $length = $end - $start;
            
            if ($start < $this->__fileSize && $end < $this->__fileSize) {
                if (@fseek($this->__encrypted, $start, SEEK_SET) != 0)
                    die("err");
                header("HTTP/1.1 206 Partial Content");
                header("Content-Range: bytes $start-$end/$this->__fileSize");
                header('Content-Length: ' . $length);
            } else {
                header("HTTP/1.1 416 Requested Range Not Satisfiable");
                die();
            }
        } else {
            header('Content-Length: '. $this->__fileSize);
        }
        header('Content-Type: audio/mpeg');
        
        session_write_close();
        @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
        ob_implicit_flush(1);
        
        set_time_limit(0);
        while(!feof($this->__encrypted)) {
            if(connection_aborted()) die('User disconnected.');
            
            $currentPointer = ftell($this->__encrypted);
            
            if(!empty($length) && $this->blocks > ($length - $currentPointer))
                $endPointer = $length;
            else
                $endPointer = $currentPointer + $this->blocks;
            
            $this->__buffer .= fread($this->__encrypted, $this->blocks);
            
            for($i = $currentPointer; $i < $endPointer && $i < $this->__fileSize; $i++) {
                $this->__buffer[$i] = chr($this->__key[ord($this->__buffer[$i])]);
            }
            
            echo substr($this->__buffer, $currentPointer);
            flush();
            usleep(4000);
        }
        fclose($this->__encrypted);
        
        // Once whole file is streamed, exit the PHP script
        exit;
    }

    public function decrypt($input) {
        if(!file_exists($input)) exit;

        $this->__encrypted = fopen($input, 'rb');
        $this->__fileSize = filesize($input);
        $this->__buffer = fread($this->__encrypted, $this->__fileSize);
        fclose($this->__encrypted);
        $bufferLength = strlen($this->__buffer);

        $this->__decrypted = '';
        for($i = 0; $i < $bufferLength; $i++) {
            $this->__decrypted .= chr($this->__key[ord($this->__buffer[$i])]);
        }

        $path = 'php://memory';
        $file = fopen($path, 'w');
        fwrite($file, $this->__decrypted);
        rewind($file);

        $this->__decrypted = null;

        return $file;
    }


    /*public function decrypt($input, $output = 'php://memory') {
        if(!file_exists($input)) exit;

        $this->__encrypted = fopen($input, 'rb');
        $this->__decrypted = fopen($output, 'w');

        while(false !== ($char = fgetc($this->__encrypted))) {
            fwrite($this->__decrypted, chr($this->__key[ord($char)]));
        }
        fclose($this->__encrypted);

        rewind($this->__decrypted);

        return $this->__decrypted;
    }*/
}