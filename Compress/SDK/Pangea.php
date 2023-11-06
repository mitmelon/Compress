<?php
namespace Compress\SDK;
/**
 * Pangea PHP SDK Implementations
 *
 * @link https://pangea.cloud/ 
 * @author Manomite Limited <manomitehq@gmail.com>
 * @version 1.0.0
 */
use \Curl\Curl;

class Pangea {
    protected $transport;
    protected $endpoint;

    public $version = 'v1';

    public function __construct($token, $service, $csp, $region){
		
        $this->endpoint = strtolower("https://{$service}.{$csp}.{$region}.pangea.cloud");
        $this->transport = new Curl();
        $this->transport->setHeader('Authorization', "Bearer {$token}");
        $this->transport->setHeader('Content-Type', 'application/json');
    }

    private function post($path, array $data){
        try {
            return $this->response($this->transport->post($this->endpoint.$path, json_encode($data)));
        } catch(\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    private function get($path, array $data){
        try {
            return $this->response($this->transport->get($this->endpoint.$path, $data));
        } catch(\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Retrieve a reputation score for a file hash from a provider, including an optional detailed report.
     *
     * @param String $hash_type (sha25, md5, sha1)
     * @param String $provider
     * @param String $content
     */
    public function file_intel(string $hash_type, string $provider, string $content ){
        $hash = hash($hash_type, $content);
        $response = $this->post('/'.$this->version.'/reputation', [
            'hash_type' => $hash_type,
            'hash' => $hash,
            'provider' => $provider,
            'raw' => true,
            'verbose' => true
        ]);
        return $response;
    }

    /**
     * Asynchronous call
     *
     * @param String $request_id
     */
    public function async_call(string $request_id){
        $response = $this->get('/request/'.$request_id, []);
        return $response;
    }

    private function error($message){
        throw new \Exception($message);
    }

    private function response($response){
        return json_decode(json_encode($response), true);
    }
}