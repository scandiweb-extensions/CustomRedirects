<?php
namespace ScandiPWA\CustomRedirects\Model\ResourceModel\Redirects;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected $_eventPrefix = 'scandipwa_redirects_collection';

    protected $_eventObject = 'redirects_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ScandiPWA\CustomRedirects\Model\Redirects', 'ScandiPWA\CustomRedirects\Model\ResourceModel\Redirects');
    }

}
