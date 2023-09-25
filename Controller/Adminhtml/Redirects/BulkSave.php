<?php

namespace ScandiPWA\CustomRedirects\Controller\Adminhtml\Redirects;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\ScopeInterface;
use ScandiPWA\CustomRedirects\Model\RedirectsFactory;
use ScandiPWA\CustomRedirects\Model\ResourceModel\Redirects\Collection as RedirectCollection;

class BulkSave extends Action
{
    /**
     * @var RedirectsFactory
     */
    protected $redirectsFactory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var RedirectCollection
     */
    protected $redirectsCollection;

    /**
     * @param Context $context
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(
        Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        AdapterFactory $adapterFactory,
        RedirectsFactory $redirectsFactory,
        RedirectCollection $redirectsCollection
    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->redirectsFactory = $redirectsFactory;
        $this->redirectsCollection = $redirectsCollection;
    }

    public function execute()
    {
        if ((isset($_FILES['importdata']['name'])) && ($_FILES['importdata']['name'] !== '')) {
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'importdata']);
                $uploaderFactory->setAllowedExtensions(['csv', 'xls']);
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);

                $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('ScandiPWA_CustomRedirects');

                $result = $uploaderFactory->save($destinationPath);

                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                } else {
                    $imagePath = 'ScandiPWA_CustomRedirects' . $result['file'];
                    $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationfilePath = $mediaDirectory->getAbsolutePath($imagePath);

                    /* file read operation */
                    $f_object = fopen($destinationfilePath, "r");
                    $column = fgetcsv($f_object);;

                    if ($f_object) {
                        if (($column[0] == 'from')
                            && ($column[1] == 'to')
                            && ($column[2] == 'is_regex')
                        ) {
                            $redirects = $this->redirectsFactory->create();
                            $count = 0;
                            $duplicates = 0;

                            while (($columns = fgetcsv($f_object)) !== FALSE) {
                                $duplicate = $redirects->getCollection()->addFieldToFilter('from', ['eq' => $columns[0]])->getData();

                                if (!count($duplicate)) {
                                    $data = [
                                        "from" => $columns[0],
                                        "to" => $columns[1],
                                        "is_regex" => $columns[2]
                                    ];
                                    $redirects->setData($data)->save();
                                    $count++;
                                } else {
                                    $duplicates++;
                                }
                            }

                            $this->messageManager->addSuccess(__('A total of %1 record(s) have been Added. Duplicates %2', $count, $duplicates));
                            $this->_redirect('scandipwa_customredirects/redirects/index');
                        } else {
                            $this->messageManager->addError(__("invalid Formated File"));
                            $this->_redirect('scandipwa_customredirects/redirects/index');
                        }
                    } else {
                        $this->messageManager->addError(__("File hase been empty"));
                        $this->_redirect('scandipwa_customredirects/redirects/index');
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('scandipwa_customredirects/redirects/index');
            }
        } else {
            $this->messageManager->addError(__("Please try again."));
            $this->_redirect('[scandipwa_customredirects/redirects/index');
        }
    }
}
