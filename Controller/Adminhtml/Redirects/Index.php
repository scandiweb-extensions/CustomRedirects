<?php

namespace ScandiPWA\CustomRedirects\Controller\Adminhtml\Redirects;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'ScandiPWA_CustomRedirects::redirects';

    protected $resultPageFactory = false;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ScandiPWA_CustomRedirects::redirects');
        $resultPage->addBreadcrumb(__('ScandiPWA Redirects'), __('ScandiPWA Redirects'));
        $resultPage->getConfig()->getTitle()->prepend(__('ScandiPWA Redirects'));

        return $resultPage;
    }


    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ScandiPWA_CustomRedirects::scandipwa_customredirects');
    }
}
