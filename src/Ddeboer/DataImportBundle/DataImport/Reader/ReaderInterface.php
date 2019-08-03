<?php

namespace Ddeboer\DataImportBundle\DataImport\Reader;

/**
 * Iterator that reads data to be imported
 *
 * @author David de Boer <david@ddeboer.nl>
 */
interface ReaderInterface extends \Iterator
{
    /**
     * Get the field (column, property) names
     *
     * @return array
     */
    public function getFields();
}
