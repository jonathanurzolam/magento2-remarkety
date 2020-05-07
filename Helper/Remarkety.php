<?php
namespace Ciudaddemascotas\Remarkety\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Remarkety extends AbstractHelper{
    /**
     * ChronoApi constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,  
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory)
	{
        parent::__construct($context); 
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->token='80b2d98ac4ac9071d6c98c8dabdd616c57cfca86';
        $this->store_id='jXbY9GaK';
        $this->base_url="https://app.remarkety.com/api/v2/stores/$this->store_id/";
    }
    
    public function getCustomer($email)
    {
        try {
            $url=$this->base_url."customers?page=1&limit=1&email=$email";
            //return $url;
            $client = $this->httpClientFactory->create();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::GET);
            $client->setHeaders(['Content-Type: application/json', 'Accept: application/json', 'x-token: '.$this->token]);
            $response = $client->request();
            $body = $response->getBody();
            return $body;
            $string = json_decode(json_encode($body),true);
            return json_decode($string);
        } catch (Exception $e) {
            return json_encode($e);
        }
    }
    public function updateCustomer($hash, $data)
    {
        try {
            $url=$this->base_url."customers/$hash";
            $client = $this->httpClientFactory->create();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::PUT);
            $client->setHeaders(['Content-Type: application/json', 'Accept: application/json', 'x-token: '.$this->token]);
            $client->setRawData(json_encode($data));
            $response = $client->request();
            $body = $response->getBody();
            $string = json_decode(json_encode($body),true);
            return json_decode($string);
        } catch (Exception $e) {
            return json_encode($e);
        }
    }
    public function createCustomer($data)
    {
        try {
            $url=$this->base_url."customers";
            $client = $this->httpClientFactory->create();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::POST);
            $client->setHeaders(['Content-Type: application/json', 'Accept: application/json', 'x-token: '.$this->token]);
            $client->setRawData(json_encode($data));
            $response = $client->request();
            $body = $response->getBody();
            $string = json_decode(json_encode($body),true);
            return json_decode($string);
        } catch (Exception $e) {
            return json_encode($e);
        }
    }
}
