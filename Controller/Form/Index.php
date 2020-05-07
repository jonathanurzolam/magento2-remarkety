<?php
namespace Ciudaddemascotas\Remarkety\Controller\Form;

class Index extends \Magento\Framework\App\Action\Action
{
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
