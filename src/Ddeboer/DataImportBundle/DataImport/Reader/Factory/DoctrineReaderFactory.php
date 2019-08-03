<?php

namespace Ddeboer\DataImportBundle\DataImport\Reader\Factory;

use Ddeboer\DataImportBundle\DataImport\Reader\DoctrineReader;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Factory that creates DoctrineReaders
 *
 */
class DoctrineReaderFactory
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getReader($object)
    {
        return new DoctrineReader($this->objectManager, $object);
    }
}
