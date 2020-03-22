<?php

namespace Cap\Rma\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;

class Dashboard extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * Form action to send data in Form Controller.
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/rma/customer/form';
    }
}
