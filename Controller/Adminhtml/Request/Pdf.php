<?php

namespace Cap\Rma\Controller\Adminhtml\Request;

use Cap\Rma\Model\Request;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Zend_Pdf;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Exception;
use Zend_Pdf_Font;
use Zend_Pdf_Page;
use Zend_Pdf_Style;
use Cap\Rma\Model\RequestRepository;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

class Pdf extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var int
     */
    private $y;

    /**
     * @var RequestRepository
     */
    protected $requestRepository;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $varDir;

    /**
     * Folder where pdf are saved
     *
     * @var string
     */
    private $path = 'rma_request';

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * ToPdf constructor.
     *
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param RequestRepository $requestRepository
     * @param Filesystem $filesystem
     * @param File $driverFile
     * @throws FileSystemException
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        RequestRepository $requestRepository,
        Filesystem $filesystem,
        File $driverFile
    ) {
        $this->fileFactory = $fileFactory;
        $this->requestRepository = $requestRepository;
        $this->varDir = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->driverFile = $driverFile;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Zend_Pdf_Exception
     * @throws Exception
     */
    public function execute()
    {
        $requestId = $this->getRequest()->getParam('request_id');

        /** @var Request $model */
        $model = $this->requestRepository->getById($requestId);

        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[0];

        $style = new Zend_Pdf_Style();
        $style->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $style->setFont($font, 13);
        $page->setStyle($style);

        $x = 30;
        $this->y = 850 - 100;

        $style->setFont($font, 14);
        $page->setStyle($style);

        $page->drawRectangle(
            30,
            $this->y + 10,
            $page->getWidth() - 30,
            $this->y + 70,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );

        $style->setFont($font, 13);
        $page->setStyle($style);
        $page->drawText(__('Request #%1', $requestId), $x + 5, $this->y + 50, 'UTF-8');

        $style->setFont($font, 11);
        $page->setStyle($style);
        $page->drawText(__('Customer Name : %1', $model->getCustomerName()), $x + 5, $this->y + 33, 'UTF-8');
        $page->drawText(__('Customer Email : %1', $model->getCustomerEmail()), $x + 5, $this->y + 16, 'UTF-8');

        $style->setFont($font, 11);
        $page->setStyle($style);
        $page->drawText(__('PRODUCT NAME'), $x + 60, $this->y - 10, 'UTF-8');
        $page->drawText(__('PRODUCT PRICE'), $x + 200, $this->y - 10, 'UTF-8');
        $page->drawText(__('QTY'), $x + 310, $this->y - 10, 'UTF-8');
        $page->drawText(__('SUB TOTAL'), $x + 440, $this->y - 10, 'UTF-8');

        $style->setFont($font, 10);
        $page->setStyle($style);

        $page->drawText('$12.00', $x + 210, $this->y - 30, 'UTF-8');
        $page->drawText(10, $x + 330, $this->y - 30, 'UTF-8');
        $page->drawText('$120.00', $x + 470, $this->y - 30, 'UTF-8');
        $pro = 'TEST product';
        $page->drawText($pro, $x + 65, $this->y - 30, 'UTF-8');

        $page->drawRectangle(
            30,
            $this->y - 62,
            $page->getWidth() - 30,
            $this->y + 10,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );
        $page->drawRectangle(
            30,
            $this->y - 62,
            $page->getWidth() - 30,
            $this->y - 100,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );

        $style->setFont($font, 15);
        $page->setStyle($style);
        $page->drawText(__('Total : %1', '$50.00'), $x + 435, $this->y - 85, 'UTF-8');

        $style->setFont($font, 10);
        $page->setStyle($style);
        $page->drawText(__('Test Footer example'), ($page->getWidth() / 2) - 50, $this->y - 200);

        $fileName = 'rma-request-' . $requestId . '.pdf';
        $this->fileFactory->create(
            $fileName,
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
