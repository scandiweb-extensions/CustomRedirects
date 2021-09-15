<?php

namespace ScandiPWA\CustomRedirects\Controller\Adminhtml\Redirects;

use Magento\Framework\Controller\ResultFactory;

class Bulk extends \Magento\Backend\App\Action
{
    /**
     * Badge controller form.php
     *
     */

    public function execute()
    {
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->getConfig()->getTitle()->prepend(__("Bulk Redirects Import"));

        return $page;
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
