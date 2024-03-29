<?php

namespace Ddeboer\DataImportBundle\DataImport\Reader\Factory;

use Ddeboer\DataImportBundle\DataImport\Reader\DbalReader;
use Doctrine\DBAL\Connection;

/**
 * Factory that creates DbalReaders
 *
 */
class DbalReaderFactory
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getReader($sql, array $params = array())
    {
        return new DbalReader($this->connection, $sql, $params);
    }
}
