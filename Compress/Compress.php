<?php
namespace Compress;

/**
 * Compresses and encrypt files including zip, image, pdf and lots more to reduce storage comsumptions.
 * Please note that the data compressed are left in binary format and stored into a text file.
 * This compressor only compresses file for storage.
 *
 * @link https://manomite.net 
 * @author Manomite Limited <manomitehq@gmail.com>
 * @version 3.0.0
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
     * Compression and Uncompression chunk size
     *
     * @param Int $compress_chunk_size
     */
    public int $compress_chunk_size = 100 * 1024 * 1024; // 100 MB
    /**
     * Encryption cipher
     *
     * @default aes-256-cbc
     */
    public string $cipher = 'aes-256-cbc';
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Encryption file temporary storage.                                                                       //
    //Do not touch here please.                                                                               //
    private string $temp = __DIR__ . '/compress_tmp_file'; //
    private string $dest_temp = __DIR__ . '/dest_compress_tmp_file'; //
    private string $encrypt_key = ''; //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Holding data folder for storing all compress errors and informations.
    private $data_dir;

    public function _construct(){
        $this->data_dir = Compress\Platform\Platform::getDataDir().'/compress';
        if(!is_dir($this->data_dir)){
            mkdir($this->data_dir, 0777, true);
        }
    }
    /**
     * Text compress
     * Updated the compressor for large content processing to avoid memory eat-up
     * @param String $data
     */
    public function compressor(string $data):string
    {
        $compressed_data = '';
        $compressed_size = strlen($data);

        if($compressed_size > $this->compress_chunk_size){
            $start = 0;
            while($start < $compressed_size){
                $chunk = substr($data, $start, $this->compress_chunk_size);
                $compressed_chunk = $this->complex_compress(gzcompress($chunk, 9));
                $compressed_data .= $compressed_chunk;
                $start += $this->compress_chunk_size;
            }
        } else {
            $compressed_data = $this->complex_compress(gzcompress($data));
        }
        return $compressed_data;
    }
    /**
     * Decompress Text
     * Updated the uncompressor for large content processing to avoid memory eat-up
     * @param String $compressed_data
     */
    public function uncompressor(string $compressed_data)
    {
        $decompressed_data = '';
        $decompressed_size = strlen($compressed_data);

        if($decompressed_size > $this->compress_chunk_size){
            $start = 0;
            while($start < $decompressed_size){
                $chunk = substr($compressed_data, $start, $this->compress_chunk_size);
                $decompressed_chunk = $this->complex_uncompress(gzuncompress($chunk));
                $decompressed_data .= $decompressed_chunk;
                $start += $this->compress_chunk_size;
            }
        } else {
            $decompressed_data = $this->complex_uncompress(gzuncompress($compressed_data));
        }
        return $decompressed_data;
    }
    /**
     * File Compresser
     *
     * @param String $filePath
     * @param String $storePath
     * @param Mixed $options ["removeMeta" => false, "encrypt" => false, "key" => "password", "scanFile" => ["token" => $token, "service" => $service, "csp" => $csp, "region" => $region]]
     */
    public function compressFile(string $filePath, string $storePath, mixed $options = [])
    {
        //Check all files in waiting_vault
        $encrypt_key = '';
        try {
            $file = @fopen($filePath, "rb");
            $content = fread($file, filesize($filePath));
            //Check if option is not empty
            if (!empty($options) && is_array($options)) {
                 //Content Scanner
                 $scanConfig = isset($options["scanFile"]) ? (is_array($options["scanFile"]) ? $options["scanFile"] : false) : false;
                 if ($scanConfig !== false) {
                     $response = $this->scanContent($content, $scanConfig);
                     if($response !== false and is_array($response)){
                         //Check if status is accepted
                         if($response['status'] === 'accepted'){
                             //Continue compression, but flag file for rechecking during decompressing
                             $this->file_put(json_encode(array_merge(array('config' => $options["scanFile"]), array('response' => $response))), $this->data_dir.'/flagged/'.hash('md5', $content));
                         }
                         if($response['status'] === 'success'){
                             //This file is corrupted and dangerous
                             //Log error and display a nice error
                             $this->file_put(json_encode($response), $this->data_dir.'/quarantine/'.hash('md5', $content));
                             throw new \Exception("File with path {$storePath} is too dangerous to be compressed. Please check the log file at {$this->data_dir}/quarantine/".hash('md5', $content)." for more informations.");
                         }
                     }
                 }
                //Meta heads are removed in new version which cannot be reversed
                $isMeta = isset($options["removeMeta"]) ? ($options["removeMeta"] === true ? true : false) : false;
                if ($isMeta) {
                    $replace = '@<meta[^>]*?>';
                    $with = ' ';
                    $content = str_replace($replace, $with, $content);
                    $content = preg_replace("/$replace/", $with, $content);

                    // This section removes all comments from content. Best for files having comments. Please note that this is a permenent removal and cannot be reversed back on decompress.
                    $content = preg_replace('/\/\/.*$/m', '', $content);
                }
                //Check if encryption is set
                $isEncrypt = isset($options["encrypt"]) ? ($options["encrypt"] === true ? true : false) : false;
                if ($isEncrypt) {
                    //mt_rand() was used to avoid file collition among many users compressing at the same time.
                    $t = $this->temp . '_' . mt_rand() . '.encrypt';
                    $d = $this->dest_temp . '_' . mt_rand() . '.encrypt';
                    $this->file_put($content, $t);
                    $key = isset($options["key"]) ? $options["key"] : null;
                    $encrypt_key = $this->encryptFile($t, $d, $key);
                    $file_s = @fopen($d, "rb");
                    $content = fread($file_s, filesize($d));
                    fclose($file_s);
                    unlink($t);
                    unlink($d);
                }
               
            }
            $content = $this->compressor($content);
            fclose($file);
            $this->file_put($content, $storePath);
            //Make sure you store your encryption key for this file if you used the encryption option
            return $encrypt_key;
        } catch (\Exception $e) {
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
    public function uncompressFile(string $fileInputPath, string $fileOutputPath, string $encrypt_key = null)
    {
        try {
            $file = fopen($fileInputPath, "rb");
            $content = fread($file, filesize($fileInputPath));
            $f = $this->data_dir.'/flagged/'.hash('md5', $content);

            if(file_exists($f)){
                //Opeartion check file
                $config = json_decode(file_get_contents($f), true);
                $scan = new \Compress\SDK\Pangea($config['config']['token'], $config['config']['service'], $config['config']['csp'], $config['config']['region']);
                $response = $scan->async_call($config['response']['request_id']);
                if (is_array($response) and !empty($response)) {
                    if (isset($response['status'])) {
                        if (strtolower($response['status']) === 'success') {
                            $verdict = strtolower($response['result']['data']['verdict']);
                            if($verdict !== 'unknown'){
                                $this->file_put(json_encode($response), $this->data_dir.'/quarantine/'.basename($f));
                                unlink($f);
                                throw new \Exception("File with path {$fileInputPath} is too dangerous to be decompressed and has been removed. Please check the log file at {$this->data_dir}/quarantine/".basename($f)." for more informations.");
                            } 
                        }
                    }
                }
                unlink($f);
            }
            $contents = $this->uncompressor($content);

            //Check if key is given; File might be encrypted
            if (!empty($encrypt_key)) {
                $t = $this->temp . '_' . mt_rand() . '.decrypt';
                $d = $this->dest_temp . '_' . mt_rand() . '.decrypt';
                $this->file_put($contents, $t);
                $this->decryptFile($t, $d, $encrypt_key);
                $file_s = @fopen($d, "rb");
                $contents = fread($file_s, filesize($d));
                fclose($file_s);
                unlink($t);
                unlink($d);
            }
            $this->file_put($contents, $fileOutputPath);
            fclose($file);
            return true;
        } catch (\Exception $e) {
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

            while (!feof($fpSource)) {
                $plaintext = fread($fpSource, $ivLenght * $this->block);
                $ciphertext = openssl_encrypt($plaintext, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);
                $iv = substr($ciphertext, 0, $ivLenght);

                fwrite($fpDest, $ciphertext);
            }

            fclose($fpSource);
            fclose($fpDest);
            return $key;
        } catch (\Exception $e) {
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

            while (!feof($fpSource)) {
                $ciphertext = fread($fpSource, $ivLenght * ($this->block + 1));
                $plaintext = openssl_decrypt($ciphertext, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);
                $iv = substr($plaintext, 0, $ivLenght);

                fwrite($fpDest, $plaintext);
            }

            fclose($fpSource);
            fclose($fpDest);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Writes huge data to a file without eating memory.
     *
     * @param string $data The data to write to the file.
     * @param string $output_file The path to the output file.
     *
     * @return void
     */
    private function file_put($data, $output_file) // 1 MB
    {
        // Open the output file for writing.
        $output_file_handle = fopen($output_file, 'w');
        $start = 0;

        // While there are still chunks to write from the data, write a chunk to the output file.
        while ($start < strlen($data)) {
            $chunk = substr($data, $start, $this->compress_chunk_size);
            fwrite($output_file_handle, $chunk);
            $start += $this->compress_chunk_size;
        }
        // Close the output file.
        fclose($output_file_handle);
    }

    /**
     * This uses the Pangea's File Intel API to get informations such as file's disposition, ranging from malicious (malware, ransomware, trojan horses, spyware, adware) to known good content (operating system files, known third-party software packages).
     * 
     * @param string $content The data to write to the file.
     * @param Array $config ["token" => $token, "service" => $service, "csp" => $csp, "region" => $region]
     */
    private function scanContent(string $content, array $config = [])
    {
        if ((isset($config['token']) and !empty($config['token'])) and (isset($config['service']) and !empty($config['service'])) and (isset($config['csp']) and !empty($config['csp'])) and (isset($config['region']) and !empty($config['token']))) {
            $scan = new \Compress\SDK\Pangea($config['token'], $config['service'], $config['csp'], $config['region']);
            $providers = ["reversinglabs", "crowdstrike"];
            $hash_types = ["sha256", "sha1", "md5"];
            foreach ($providers as $provider) {
                foreach ($hash_types as $hash_type) {
                    $response = $scan->file_intel($hash_type, $provider, $content);
                    file_put_contents(__DIR__ . '/' . $provider . '_' . $hash_type . '.json', json_encode($response));
                    if (is_array($response) and !empty($response)) {
                        if (isset($response['status'])) {
                            if (strtolower($response['status']) === 'success') {
                                $verdict = strtolower($response['result']['data']['verdict']);
                                if($verdict !== 'unknown'){
                                    return $response;
                                }
                            }
                            if(strtolower($response['status']) === 'accepted'){
                                $payload = array(
                                    'request_id' => $response['request_id'],
                                    'status' => 'accepted'
                                );
                                return $payload;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    private function complex_compress($data){
        //future implementations
        return $data;
    }

    private function complex_uncompress($data){
        //future implementations
        return $data;
    }

}