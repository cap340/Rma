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
     * @var RequestRepository
     */
    protected $requestRepository;

    /**
     * @var Request\ToPdf
     */
    protected $toPdf;

    /**
     * ToPdf constructor.
     *
     * @param Action\Context $context
     * @param RequestRepository $requestRepository
     * @param Request\ToPdf $toPdf
     */
    public function __construct(
        Action\Context $context,
        RequestRepository $requestRepository,
        Request\ToPdf $toPdf
    ) {
        $this->requestRepository = $requestRepository;
        $this->toPdf = $toPdf;
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

        $filename = 'rma-request-' . $requestId . '.pdf';
        $this->toPdf->setData('filename', $filename);
        $this->toPdf->execute();
    }
}
