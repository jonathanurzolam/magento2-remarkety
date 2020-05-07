<?php
namespace Ciudaddemascotas\Remarkety\Block;

class IndexBlock extends \Magento\Framework\View\Element\Template
{
    protected $urlInterface;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\UrlInterface $urlInterface
    )
    {
        $this->urlInterface=$urlInterface;
        parent::__construct($context);
    }

    public function getFormUrl()
    {
        return $this->urlInterface->getUrl('remarkety/form/process');
    }
}