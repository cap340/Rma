<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Model\Request;
use Cap\Rma\Model\RequestRepository;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Zend_Pdf_Exception;

class Pdf extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cap_Rma::request_print';

    /**
     * @var RequestRepository
     */
    protected $requestRepository;

    /**
     * @var Request\Pdf\Request
     */
    protected $requestToPdf;

    /**
     * ToPdf constructor.
     *
     * @param Action\Context $context
     * @param RequestRepository $requestRepository
     * @param Request\Pdf\Request $requestToPdf
     */
    public function __construct(
        Action\Context $context,
        RequestRepository $requestRepository,
        Request\Pdf\Request $requestToPdf
    ) {
        $this->requestRepository = $requestRepository;
        $this->requestToPdf = $requestToPdf;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws LocalizedException
     * @throws Zend_Pdf_Exception
     */
    public function execute()
    {
        $requestId = $this->getRequest()->getParam('request_id');

        /** @var Request $model */
        $model = $this->requestRepository->getById($requestId);

        $data = [
            'filename' => 'rma-request-' . $model->getRequestId() . '.pdf',
            'request_id' => $model->getRequestId(),
            'created_at' => $model->getCreatedAt(),
            'increment_id' => $model->getIncrementId(),
            'customer_name' => $model->getCustomerName(),
            'customer_email' => $model->getCustomerEmail(),
            'description' => $model->getDescription()
        ];

        $this->requestToPdf->addData($data);
        $this->requestToPdf->getPdf();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
