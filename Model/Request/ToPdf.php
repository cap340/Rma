<?php

namespace Cap\Rma\Model\Request;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Zend_Pdf;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Exception;
use Zend_Pdf_Font;
use Zend_Pdf_Page;
use Zend_Pdf_Resource_Font;
use Zend_Pdf_Style;

class ToPdf extends DataObject
{
    /**
     * @var int
     */
    public $y;

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $pdf;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * ToPdf constructor.
     *
     * @param FileFactory $fileFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param StateInterface $inlineTranslation
     * @param TimezoneInterface $localeDate
     * @param array $data
     * @throws FileSystemException
     */
    public function __construct(
        FileFactory $fileFactory,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        StateInterface $inlineTranslation,
        TimezoneInterface $localeDate,
        array $data = []
    ) {
        $this->fileFactory = $fileFactory;
        $this->scopeConfig = $scopeConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->inlineTranslation = $inlineTranslation;
        $this->localeDate = $localeDate;
        parent::__construct($data);
    }

    /**
     * @throws Zend_Pdf_Exception
     * @throws Exception
     */
    public function execute()
    {
        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[0];

        $style = new Zend_Pdf_Style();
        $style->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->_setFontRegular($page, 12);
        $page->setStyle($style);

        $x = 30;
        $this->y = 850 - 100;
        $page->setStyle($style);

        $page->drawRectangle(
            30,
            $this->y + 10,
            $page->getWidth() - 30,
            $this->y + 70,
            Zend_Pdf_Page::SHAPE_DRAW_STROKE
        );

        $this->_setFontBold($page, 14);
        $page->drawText(__('Request', 'ID'), $x + 5, $this->y + 50, 'UTF-8');
        $this->_setFontRegular($page, 12);
        $page->drawText(__('Name :', 'NAME'), $x + 5, $this->y + 33, 'UTF-8');
        $page->drawText(__('Email : ', 'EMAIL'), $x + 5, $this->y + 16, 'UTF-8');

        $page->drawText(__('PRODUCT NAME'), $x + 60, $this->y - 10, 'UTF-8');
        $page->drawText(__('PRODUCT PRICE'), $x + 200, $this->y - 10, 'UTF-8');
        $page->drawText(__('QTY'), $x + 310, $this->y - 10, 'UTF-8');
        $page->drawText(__('SUB TOTAL'), $x + 440, $this->y - 10, 'UTF-8');
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
        $page->drawText(__('Total : %1', '$50.00'), $x + 435, $this->y - 85, 'UTF-8');
        $page->drawText(__('Test Footer example'), ($page->getWidth() / 2) - 50, $this->y - 200);

        $fileName = $this->getData('filename');
        $this->fileFactory->create(
            $fileName,
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
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
     * Set font as italic
     *
     * @param Zend_Pdf_Page $object
     * @param int $size
     * @return Zend_Pdf_Resource_Font
     * @throws Zend_Pdf_Exception
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_It-2.8.2.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }
}
