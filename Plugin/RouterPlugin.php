<?php

namespace ScandiPWA\CustomRedirects\Plugin;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use ScandiPWA\CustomRedirects\Model\RedirectsFactory;
use ScandiPWA\Router\Controller\Pwa;
use ScandiPWA\Router\Controller\Router;

class RouterPlugin {
    /**
     * @var RedirectsFactory
     */
    protected $redirectsFactory;
    /**
     * @var Redirect
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    public function __construct(
        Redirect $redirect,
        ResponseFactory $responseFactory,
        RedirectsFactory $redirectsFactory
    ){
        $this->redirect = $redirect;
        $this->responseFactory = $responseFactory;
        $this->redirectsFactory = $redirectsFactory;
    }

    /**
     * @param Router $subject
     * @param RequestInterface $request
     * @return string[]
     */
    public function beforeMatch(Router $subject, RequestInterface $request)
    {
        $requestPath = $request->getRequestUri();
        $redirects = $this->redirectsFactory->create();
        $match = $redirects->getCollection()->addFieldToFilter('from', ['eq'=> $requestPath])->getData();

        if(count($match)) {
            $this->responseFactory->create()->setRedirect($match[0]['to'], 301)->sendResponse();
            exit;
        }
    }
}
