<?php

namespace Cap\Rma\Block\Adminhtml\Request\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Info extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    protected $booleanOptions;

    /**
     * Label constructor.
     *
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
