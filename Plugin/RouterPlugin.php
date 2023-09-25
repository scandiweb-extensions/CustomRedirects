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
        $requestPath = $request->getRequestUri();
        $redirectsCollection = $this->redirectCollectionFactory->create();
        $match = $redirectsCollection->addFieldToFilter('from', ['eq' => $requestPath])->getData();

        if (!count($match)) {
            $urlComponents = parse_url($requestPath);
            $filteredURL = $urlComponents['path'];

            $match = $redirectsCollection->addFieldToFilter('from', ['eq' => $filteredURL])->getData();
        }

        if (!count($match)) {
            /**
             * $collection->clear() will reset data in the collection, but
             * it won't reset the underlying SQL query modifications we made using addFieldToFilter
             */
            $redirectsCollection = $this->redirectCollectionFactory->create();
            $redirectsCollection->getSelect()
                ->where(
                    '"' . $requestPath  . '"' . ' REGEXP main_table.from'
                )->where(
                    self::IS_REGEX . " = " . true
                );

            $match = $redirectsCollection->getData();

            if (count($match)) {
                $destination = preg_replace('/' . $match[0]['from'] . '/',  $match[0]['to'], $requestPath);

                $this->responseFactory->create()->setRedirect($destination, 301)->sendResponse();
                exit;
            }
        }

        if (count($match)) {
            $this->responseFactory->create()->setRedirect($match[0]['to'], 301)->sendResponse();
            exit;
        }
    }
}
