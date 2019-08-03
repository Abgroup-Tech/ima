<?php

namespace Ddeboer\DataImportBundle\DataImport\Reader\Factory;
use Ddeboer\DataImportBundle\DataImport\Reader\ExcelReader;

/**
 * Factory that creates ExcelReaders
 *
 */
class ExcelReaderFactory
{
    protected $headerRowNumber;
    protected $activeSheet;

    public function __construct($headerRowNumber = null, $activeSheet = null)
    {
        $this->headerRowNumber = $headerRowNumber;
        $this->activeSheet = $activeSheet;
    }

    public function getReader(\SplFileObject $file)
    {
        return new ExcelReader($file, $this->headerRowNumber, $this->activeSheet);
    }
}
