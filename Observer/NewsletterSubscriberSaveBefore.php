<?php
namespace Ciudaddemascotas\Remarkety\Observer;

use Magento\Framework\Event\Observer;

class NewsletterSubscriberSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ciudaddemascotas\Remarkety\Helper\Remarkety $remarkety)
    {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->remarkety=$remarkety;
    }
    public function execute(Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $email = $subscriber->getEmail();
        $subscriberStatus = $subscriber->getSubscriberStatus();
        try {
            $magento_data= $this->getCustomerData($email);
            $remarkety_data=json_decode($this->remarkety->getCustomer($email), true);
            $magento_data_count=count($magento_data);
            $remarkety_data_count=count($remarkety_data['customers']);
            if ($magento_data_count==0 && $remarkety_data_count==0) {
                # Crear como Amigo pet lover
                $this->sendToRemarkety($email, "Amigo", "Pet lover", false);
            } else if ($magento_data_count==0 && $remarkety_data_count>1) {
                # Actualizar con datos de Remarkety
                $remarkety_customer=$remarkety_data['customers'][0];
                $this->sendToRemarkety($email, $remarkety_customer['first_name'], $remarkety_customer['last_name'], $remarkety_customer['hash']);
            } else if ($magento_data_count>0 && $remarkety_data_count==0) {
                # Crear con datos de Magento
                $this->sendToRemarkety($email, $magento_data['firstname'], $magento_data['lastname'], false);
            }
            
        } catch (Exception $e) {
            $this->logger->addInfo('Remarkety', ["Error"=>'Getting customer']);
            //throw new \Magento\Framework\Exception\LocalizedException(__("The customer email isn't defined."));
        }
    }
    /**
     * Get store id by website id
     *
     * @param int $id
     * @return id
     */
    public function getCustomerData($email)
    {
        $websiteId=$this->getWebsiteId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory');
        $customer=$customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);// load customer by email address
        return $customer->getData();
    }
    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }
    public function sendToRemarkety($email, $first_name, $last_name, $hash)
    {
        $data=[
            'customer'=>[
                "accepts_marketing"=> true,
                "guest"=> false,
                "email"=> $email,
                "first_name"=> $first_name,
                "last_name"=> $last_name
            ]
        ];
        if (!$hash) {
            $result=$this->remarkety->createCustomer($data);
        } else {
            $result=$this->remarkety->updateCustomer($hash, $data);
        }
        //$this->logger->addInfo("Remarkety", ["Action result" => json_encode($result)]);
    }
}