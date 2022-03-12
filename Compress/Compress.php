<?php
namespace Compress;
/**
 * Compresses and encrypt files including zip, image, pdf and lots more
 *
 * @link https://blog.manomite.net for Programming Tutorials
 * @author Manomite <manomitehq@gmail.com>
 * @version 2.0.0
 */
class Compress
{
    /**
     * Encryption block chunk size
     *
     * @param Int $block
     */
     public int $block = 1024;
     /**
     * Encryption cipher
     *
     * @default aes-256-cbc
     */
     public $cipher = 'aes-256-cbc';
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////
     //Encryption file temporary storage.                                                                       //
     //Do not touch here please.                                                                               //
     private $temp = __DIR__.'/compress_tmp_file';                                                            //
     private $dest_temp = __DIR__.'/dest_compress_tmp_file';                                                 //
     private $encrypt_key = null;                                                                           //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
     /**
     * Text compress
     *
     * @param String $content
     */
    public static function compressor(String $content)
    {
        return gzcompress($content);
    }
   /**
     * Decompress Text
     *
     * @param String $content
     */
    public static function uncompressor(String $content)
    {
        return gzuncompress($content);
    }
    /**
     * File Compresser
     *
     * @param String $filePath
     * @param String $storePath
     * @param Array $options ["removeMeta" => false, "encrypt" => false, "key" => "password"]
     */
    public static function compressFile(String $filePath, String $storePath, array $options = [])
    {
        try {
            $file = @fopen($filePath, "rb");
            $content = fread($file, filesize($filePath));
            //Check if option is not empty
            if (!empty($options)) {
                //Meta heads are removed in new version which cannot be reversed
                $isMeta = isset($options["removeMeta"]) ? ($options["removeMeta"] === true ? true : false) : false;
                if ($isMeta) {
                    $replace    = '@<meta[^>]*?>';
                    $with       = ' ';
                    $content    = str_replace($replace, $with, $content);
                    $content    = preg_replace("/$replace/", $with, $content);
                }
                //Check if encryption is set
                $isEncrypt = isset($options["encrypt"]) ? ($options["encrypt"] === true ? true : false) : false;
                if ($isEncrypt) {
                    //mt_rand() was used to avoid file collition among many users compressing at the same time.
                    $t = $this->temp.'_'.mt_rand().'.encrypt';
                    $d = $this->dest_temp.'_'.mt_rand().'.encrypt';
                    file_put_contents($t, $content);
                    $key = isset($options["key"]) ? $options["key"] : null;
                    $encrypt_key = $this->encryptFile($t, $d, $key);
                    $file_s = @fopen($d, "rb");
                    $content = fread($file_s, filesize($d));
                    fclose($file_s);
                    unlink($t);
                    unlink($d);
                }
            }
            $content = $this->compressor(serialize($content));
            fclose($file);
            file_put_contents($storePath, $content);
            //Make sure you store your encryption key for this file if you used the encryption option
            return $encrypt_key;
        } catch (\Extension $e) {
            return $e->getMessage();
        }
    }
   /**
     * File DeCompresser
     *
     * @param String $fileInputPath
     * @param String $fileOutputPath
     * @param String $encrypt_key
     */
    public static function uncompressFile($fileInputPath, $fileOutputPath, $encrypt_key = null)
    {
        try {
            $file = fopen($fileInputPath, "rb");
            $contents = unserialize($this->uncompressor(fread($file, filesize($fileInputPath))));
            //Check if key is given; File might be encrypted
            if(!empty($encrypt_key)){
                $t = $this->temp.'_'.mt_rand().'.decrypt';
                $d = $this->dest_temp.'_'.mt_rand().'.decrypt';
                file_put_contents($t, $contents);
                $this->decryptFile($t, $d, $encrypt_key);
                $file_s = @fopen($d, "rb");
                $contents = fread($file_s, filesize($d));
                fclose($file_s);
                unlink($t);
                unlink($d);
            }
            file_put_contents($fileOutputPath, $contents);
            fclose($file);
            return true;
        } catch (\Extension $e) {
            return $e->getMessage();
        }
    }
    /**
     * @param  $source  Path of the unencrypted file
     * @param  $dest  Path of the encrypted file to created
     * @param  $key  Encryption key
     */
    private function encryptFile($source, $dest, $key = null)
    {
        try {
            if (!extension_loaded('openssl')) {
                return "Sorry! openssl extension is not installed on your system. Please install it using sudo apt-get install openssl";
            }

            if (empty($key)) {
                $key = openssl_random_pseudo_bytes(120);
            }

            $ivLenght = openssl_cipher_iv_length($this->cipher);
            $iv = openssl_random_pseudo_bytes($ivLenght);

            $fpSource = fopen($source, 'rb');
            $fpDest = fopen($dest, 'w');

            fwrite($fpDest, $iv);

            while (! feof($fpSource)) {
                $plaintext = fread($fpSource, $ivLenght * $this->block);
                $ciphertext = openssl_encrypt($plaintext, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);
                $iv = substr($ciphertext, 0, $ivLenght);

                fwrite($fpDest, $ciphertext);
            }

            fclose($fpSource);
            fclose($fpDest);
            return $key;
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * @param  $source  Path of the encrypted file
     * @param  $dest  Path of the decrypted file
     * @param  $key  Encryption key
     */
    private function decryptFile($source, $dest, $key)
    {
        try {
            $ivLenght = openssl_cipher_iv_length($this->cipher);

            $fpSource = fopen($source, 'rb');
            $fpDest = fopen($dest, 'w');

            $iv = fread($fpSource, $ivLenght);

            while (! feof($fpSource)) {
                $ciphertext = fread($fpSource, $ivLenght * ($this->block + 1));
                $plaintext = openssl_decrypt($ciphertext, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);
                $iv = substr($plaintext, 0, $ivLenght);

                fwrite($fpDest, $plaintext);
            }

            fclose($fpSource);
            fclose($fpDest);
            return true;
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
}