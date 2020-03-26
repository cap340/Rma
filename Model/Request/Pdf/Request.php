<?php

namespace Cap\Rma\Model\Request\Pdf;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Zend_Pdf;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Exception;
use Zend_Pdf_Font;
use Zend_Pdf_Page;
use Zend_Pdf_Resource_Font;
use Zend_Pdf_Style;

class Request extends DataObject
{
    /**
     * @var int
     */
    public $y;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Request constructor.
     *
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @param array $data
     */
    public function __construct(
        Filesystem $filesystem,
        FileFactory $fileFactory,
        array $data = []
    ) {
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->fileFactory = $fileFactory;
        parent::__construct($data);
    }

    /**
     * @throws Zend_Pdf_Exception
     * @throws Exception
     */
    public function getPdf()
    {
        $requestId = $this->getData('requestId');
        $createdAt = $this->getData('createdAt');
        $incrementId = $this->getData('incrementId');
        $customerName = $this->getData('customerName');
        $customerEmail = $this->getData('customerEmail');
        $description = $this->getData('description');

        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[0];

        $style = new Zend_Pdf_Style();
        $style->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $page->setStyle($style);
        $x = 30;
        $this->y = 850 - 100;

        // Header
        $page->drawRectangle(
            30,
            $this->y + 10,
            $page->getWidth() - 30,
            $this->y + 70,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );
        $this->_setFontBold($page, 14);
        $page->drawText(__('Request #%1', $requestId), $x + 10, $this->y + 50, 'UTF-8');
        $this->_setFontRegular($page, 11);
        $page->drawText(__('Created At: %1', $createdAt), $x + 10, $this->y + 35, 'UTF-8');
        $this->_setFontBold($page, 11);
        $page->drawText(__('Order: %1', $incrementId), $x + 10, $this->y + 20, 'UTF-8');

        // Body
        $page->drawRectangle(
            30,
            $this->y + 70,
            $page->getWidth() - 30,
            $this->y - 700,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );
        $this->_setFontBold($page, 11);
        $page->drawText(__('Name: %1', $customerName), $x + 10, $this->y - 10, 'UTF-8');
        $page->drawText(__('Email: %1', $customerEmail), $x + 10, $this->y - 25, 'UTF-8');
        $page->drawText(__('Description:'), $x + 10, $this->y - 45, 'UTF-8');
        $this->_setFontRegular($page, 10);
        $page->drawText(__('%1', $description), $x + 10, $this->y - 60, 'UTF-8');

        $filename = $this->getData('filename');
        $this->fileFactory->create(
            $filename,
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    /**
     * Set font as bold
     *
     * @param Zend_Pdf_Page $object
     * @param int $size
     * @return Zend_Pdf_Resource_Font
     * @throws Zend_Pdf_Exception
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as regular
     *
     * @param Zend_Pdf_Page $object
     * @param int $size
     * @return Zend_Pdf_Resource_Font
     * @throws Zend_Pdf_Exception
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Re-4.4.1.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }
}
