<?php
namespace Ciudaddemascotas\Remarkety\Controller\Form;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $logger;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
        parent::__construct($context);
    }
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }  
}
