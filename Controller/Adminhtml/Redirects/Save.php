<?php

namespace ScandiPWA\CustomRedirects\Controller\Adminhtml\Redirects;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use ScandiPWA\CustomRedirects\Model\RedirectsFactory;

class Save extends Action
{
    protected $RedirectsFactory;

    /**
     * @param Context $context
     * @param RedirectsFactory $BadgeFactory
     */
    public function __construct(
        Context $context,
        RedirectsFactory $RedirectsFactory
    ) {
        $this->RedirectsFactory = $RedirectsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $arr = $_POST['general'];
        if (!isset($arr['id'])) {
            $data = [
                "from" => $arr['from'],
                "to" => $arr['to'],
                "is_regex" => $arr['is_regex']
            ];
            $this->RedirectsFactory->create()
                ->setData($data)->save();
        } else {
            $data = [
                'id' => $arr['id'],
                "from" => $arr['from'],
                "to" => $arr['to'],
                "is_regex" => $arr['is_regex']
            ];
            $this->RedirectsFactory->create()->setData($data)->save();
        }
        return $this->resultRedirectFactory->create()->setPath('scandipwa_customredirects/redirects/index');
    }
}
