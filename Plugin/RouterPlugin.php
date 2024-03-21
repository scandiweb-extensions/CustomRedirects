<?php

namespace ScandiPWA\CustomRedirects\Plugin;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use ScandiPWA\CustomRedirects\Model\ResourceModel\Redirects\CollectionFactory as RedirectCollectionFactory;
use ScandiPWA\Router\Controller\Router;

class RouterPlugin
{
    const IS_REGEX = 'is_regex';

    /**
     * @var RedirectCollectionFactory
     */
    protected $redirectCollectionFactory;

    /**
     * @var Redirect
     */
    protected $redirect;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    public function __construct(
        Redirect $redirect,
        ResponseFactory $responseFactory,
        RedirectCollectionFactory $redirectCollectionFactory
    ) {
        $this->redirect = $redirect;
        $this->responseFactory = $responseFactory;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
    }

    /**
     * @param Router $subject
     * @param RequestInterface $request
     * @return string[]
     */
    public function beforeMatch(Router $subject, RequestInterface $request)
    {
        // Match the entire path with params
        $requestPath = $request->getRequestUri();
        $redirectsCollection = $this->redirectCollectionFactory->create();
        $match = $redirectsCollection->addFieldToFilter('redirect_from', ['eq' => $requestPath])->getData();
        $pathOnly = $request->getPathInfo();

        // If it fails to match the entire url, try to match the path only
        if (!count($match)) {
            $match = $redirectsCollection->addFieldToFilter('redirect_from', ['eq' => $pathOnly])->getData();
        }

        // If it fails to do that, try to match using regex
        if (!count($match)) {
            $this->performRegexMatch($requestPath);
        }

        if (count($match)) {
            $this->responseFactory->create()->setRedirect($match[0]['redirect_to'], 301)->sendResponse();
            exit;
        }
    }

    /**
     * Performs regex redirect if regex match found
     *
     * @param string $path
     * @return void
     */
    public function performRegexMatch(string $path)
    {
        /**
         * $collection->clear() will reset data in the collection, but
         * it won't reset the underlying SQL query modifications we made using addFieldToFilter
         */
        $redirectsCollection = $this->redirectCollectionFactory->create();
        $redirectsCollection->getSelect()
            ->where(
                '"' . $path  . '"' . ' REGEXP main_table.redirect_from'
            )->where(
                self::IS_REGEX . " = " . true
            );

        $match = $redirectsCollection->getData();

        if (count($match)) {
            $plainFrom = ltrim($match[0]['redirect_from'], '/');
            $cleanFrom = preg_replace('/\//', '\/', $plainFrom);
            $destination = preg_replace('/' . $cleanFrom . '/',  $match[0]['redirect_to'], ltrim($path, '/'));

            $this->responseFactory->create()->setRedirect($destination, 301)->sendResponse();
            exit;
        }
    }
}
