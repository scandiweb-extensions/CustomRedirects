<?php
namespace ScandiPWA\CustomRedirects\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Redirects extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'scandipwa_redirects';

    protected $_cacheTag = 'scandipwa_redirects';

    protected $_eventPrefix = 'scandipwa_redirects';

    protected function _construct()
    {
        $this->_init('ScandiPWA\CustomRedirects\Model\ResourceModel\Redirects');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
