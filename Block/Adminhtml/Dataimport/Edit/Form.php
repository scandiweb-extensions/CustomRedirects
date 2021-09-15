<?php

namespace ScandiPWA\CustomRedirects\Block\Adminhtml\Dataimport\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var
     */
    protected $_assetRepo;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Repository $assetRepo
     * @param array $data
     */
    public function __construct(
        Context     $context,
        Registry    $registry,
        FormFactory $formFactory,
        Repository  $assetRepo,
        array       $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $path = $this->_assetRepo->getUrl("../../../../../../media/csv/sample/samplefile.csv");

        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );

        $form->setHtmlIdPrefix('datalocation_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Import Redirects'), 'class' => 'fieldset-wide']
        );

        $importdata_script =  $fieldset->addField(
            'importdata',
            'file',
            array(
                'label'     => 'Upload File',
                'required'  => true,
                'name'      => 'importdata',
                'note' => 'Allow File type: .csv',
            )
        );

        $importdata_script->setAfterElementHtml("
        <span id='sample-file-span' ><a id='sample-file-link' href='".$path."'  >Download Sample File</a></span>
            <script type=\"text/javascript\">
            document.getElementById('scandipwa_customredirects_importdata').onchange = function () {
                var fileInput = document.getElementById('scandipwa_customredirects_importdata');
                var filePath = fileInput.value;
                var allowedExtensions = /(\.csv|\.xls)$/i;
                if(!allowedExtensions.exec(filePath))
                {
                    alert('Please upload file having extensions .csv');
                    fileInput.value = '';
                }
            };
            </script>"
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
