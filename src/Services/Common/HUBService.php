<?php

namespace App\Services\Common;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface ;

class HUBService
{
    public $hubUrl;
    public $storageUrl;
    public $categoryUrl;
    public $localAreaUrl;
    public $distributorUrl;
    public $productUrl;
    public $orderUrl;
    public $tiersUrl;
    public $loginUrl;
    public $refreshTokenUrl;
    public $keyClient;
    public $keySecret;

    public function __construct(protected ParameterBagInterface $_parameterBag) {
        

        $api                   = '/api/' ;

        $this->hubUrl          = $this->_parameterBag->get('hub_base_url') ;
        $this->keyClient       = $this->_parameterBag->get('hub_key_client') ;
        $this->keySecret       = $this->_parameterBag->get('hub_key_secret') ;
        $this->storageUrl      = $this->hubUrl.$api."storage";
        $this->categoryUrl     = $this->hubUrl.$api."category";
        $this->localAreaUrl    = $this->hubUrl.$api."local_area";
        $this->distributorUrl  = $this->hubUrl.$api."distributor";
        $this->productUrl      = $this->hubUrl.$api."product";
        $this->tiersUrl        = $this->hubUrl.$api."tiers";
        $this->loginUrl        = $this->hubUrl.$api."login_check";
        $this->orderUrl        = $this->hubUrl.$api."order";
        $this->refreshTokenUrl = $this->hubUrl.$api."token/refresh";
    }

    /**
     * HUB connexion
     *
     * @return void
     */
    public function connexion(){

        $datas   = [
            "username" => $this->keyClient,
            "password" => $this->keySecret
        ] ;
        
        $results = $this->sendRequest($this->loginUrl, 'POST', json_encode($datas)) ;
       
        return json_decode($results, true) ;
    }

    /**
     * Send request to HUB
     *
     * @param string $_url
     * @param string $_requestType
     * @param string $_datas
     * @param array $_headers
     * @return mixed
     */
    public function sendRequest($_url, $_requestType = "GET", $_datas = "", $_headers = [], $_token = ""){
        $headers = $_headers ;
        
        if(count($headers) == 0){
            $headers[] = 'Content-Type: application/json' ;
        }

        if(!empty($_token)){
            $headers[] = 'Authorization: Bearer '.$_token ;
            
        }
        

        $curl = curl_init($_url);

        $params = [
            CURLOPT_URL             => $_url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $_requestType,
            CURLOPT_HTTPHEADER      => $headers,
        ] ;

        if($_requestType == "POST"){
            $params[CURLOPT_POSTFIELDS] = $_datas ;
        }

        curl_setopt_array($curl, $params);
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return $response ;
    }

}