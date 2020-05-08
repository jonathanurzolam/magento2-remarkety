<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ciudaddemascotas\Remarkety\Controller\Form;

use Ciudaddemascotas\Remarkety\Helper\Remarkety;
use Magento\Framework\Controller\ResultFactory;

class Process extends \Magento\Framework\App\Action\Action
{
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Ciudaddemascotas\Remarkety\Helper\Remarkety $remarkety
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->remarkety=$remarkety;
    }
    
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->logger->addInfo("Ciudaddemascotas Remarkety", ["Form data" => json_encode($_POST)]);
        $customer=json_decode($this->remarkety->getCustomer($_POST["email"]), true);
        if (count($customer['customers'])==0) {
            $this->logger->addInfo("Ciudaddemascotas Remarkety", ["Customer" => 'crear']);
            $new_tags=[];
            $new_tags[0]=$_POST["pet_type"];
            $new_tags[1]=$_POST["pet_breed"];
            $new_tags[2]=$_POST["pet_birthday"];
            $external_tags=$new_tags;
            $data=[
                'customer'=>[
                    "guest"=> false,
                    "email"=> $_POST["email"],
                    "first_name"=> ucwords(strtolower($_POST["firstname"])),
                    "last_name"=> ucwords(strtolower($_POST["lastname"])),
                    "default_address"=>[
                        "city"=>$_POST["city"],
                    ],
                    "pet_birthday"=>$_POST["pet_birthday"],
                    "pet_name_cdm"=>ucwords(strtolower($_POST["pet_name"])),
                    'tags'=>$external_tags
                ]
            ];
            $result=$this->remarkety->createCustomer($data);
            $this->logger->addInfo("Ciudaddemascotas Remarkety", ["Customer" => json_encode($result)]);
        }else{
            $customer_data=$customer['customers'][0];
            $hash=$customer_data['hash'];
            $new_tags=[];
            if ($_POST["pet_type"]=='perro_gato') {
                $new_tags=['perro', 'gato',ucwords(strtolower($_POST["pet_breed"]))];
            } else {
                $new_tags=[$_POST["pet_type"],ucwords(strtolower($_POST["pet_breed"]))];
            }
            
            $old_tags=$customer_data["tags"];
            $external_tags=array_merge($new_tags, $old_tags);
            $data=[
                'customer'=>[
                    "guest"=> false,
                    "group"=>$customer_data['group'],
                    "email"=> $_POST["email"],
                    "first_name"=> ucwords(strtolower($_POST["firstname"])),
                    "last_name"=> ucwords(strtolower($_POST["lastname"])),
                    "default_address"=>[
                        "city"=>$_POST["city"],
                    ],
                    "pet_birthday"=>$_POST["pet_birthday"],
                    "pet_name_cdm"=>ucwords(strtolower($_POST["pet_name"])),
                    'tags'=>$external_tags
                ]
            ];
            $result=$this->remarkety->updateCustomer($hash, $data);
            $this->logger->addInfo("Ciudaddemascotas Remarkety", ["Customer" => json_encode($result)]);
        }
        
        //$this->messageManager->addSuccess(__('Form successfully submitted'));
        $this->messageManager->addSuccessMessage('Actualización realizada !');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/remarkety/form/index');
        return $resultRedirect;
        
    }
}
