<?php

namespace Cap\Rma\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Email extends AbstractHelper
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Email constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param ManagerInterface $messageManager
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        Data $helper,
        ManagerInterface $messageManager,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder
    ) {
        $this->helper = $helper;
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param $receivers
     * @param $emailTemplate
     * @param $templateVar
     */
    public function sendEmail($receivers, $emailTemplate, $templateVar)
    {
        try {
            $email = $this->helper->getConfigEmailSender();
            $emailValue = 'trans_email/ident_' . $email . '/email';
            $emailNameValue = 'trans_email/ident_' . $email . '/name';
            $emailNameSender = $this->scopeConfig->getValue($emailNameValue, ScopeInterface::SCOPE_STORE);
            $emailSender = $this->scopeConfig->getValue($emailValue, ScopeInterface::SCOPE_STORE);
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($emailNameSender),
                'email' => $this->escaper->escapeHtml($emailSender),
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($templateVar)
                ->setFrom($sender)
                ->addTo($receivers);

            $transport = $transport->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

        } catch (Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(__('Failed to send email.' . $e->getMessage()));
            return;
        }
    }

    /**
     * @param $data
     */
    public function sendEmailAdmin($data)
    {
        //todo: if isset requestType in email subject
        $emailTemplate = $this->helper->getConfigEmailTemplateAdmin();
        $adminEmail = $this->helper->getConfigEmailAdmin();
        $adminEmails = explode(',', $adminEmail);
        $countEmail = count($adminEmails);
        if ($countEmail > 1) {
            foreach ($adminEmails as $value) {
                $value = str_replace(' ', '', $value);
                $emailTemplateData = [
                    'adminEmail' => $value,
                    'requestId' => $data['requestId'],
                    'orderIncrementId' => $data['orderIncrementId'],
                    'customerName' => $data['customerName'],
                    'description' => $data['description'],
                    'createdAt' => $data['createdAt'],
                ];
                $this->sendEmail($value, $emailTemplate, $emailTemplateData);
            }
        } else {
            $emailTemplateData = [
                'adminEmail' => $adminEmail,
                'requestId' => $data['requestId'],
                'orderIncrementId' => $data['orderIncrementId'],
                'customerName' => $data['customerName'],
                'description' => $data['description'],
                'createdAt' => $data['createdAt'],
            ];
            $this->sendEmail($adminEmail, $emailTemplate, $emailTemplateData);
        }
    }

    /**
     * @param $data
     */
    public function sendEmailCustomer($data)
    {
        //todo: if isset requestType in email subject
        $emailTemplate = $this->helper->getConfigEmailTemplateCustomer();
        $customerEmail = $data['customerEmail'];

        $emailTemplateData = [
            'customerEmail' => $customerEmail,
            'requestId' => $data['requestId'],
            'orderIncrementId' => $data['orderIncrementId'],
            'customerName' => $data['customerName'],
            'description' => $data['description'],
            'createdAt' => $data['createdAt'],
        ];
        $this->sendEmail($customerEmail, $emailTemplate, $emailTemplateData);
    }
}
