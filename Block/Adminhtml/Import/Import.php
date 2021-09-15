<?php

namespace ScandiPWA\CustomRedirects\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;

class Import extends Container {

    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'ScandiPWA_CustomRedirects';
        $this->_controller = 'adminhtml_dataimport';
        parent::_construct();
        $this->buttonList->remove('back');
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->remove('reset');

        $this->addButton(
            'backhome',
            [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('scandipwa_customredirects/redirects/index')),
                'class' => 'back',
                'level' => -2
            ]
        );


    }


    public function getHeaderText()
    {
        return __('Import Redirects Data');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }


    public function getFormActionUrl()
    {
        return $this->getUrl('scandipwa_customredirects/redirects/bulksave');
    }
}
