<?php

namespace Ddeboer\DataImportBundle\DataImport\ValueConverter;

use Ddeboer\DataImportBundle\DataImport\Exception\UnexpectedValueException;

class MappingValueConverter implements ValueConverterInterface
{
    private $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function convert($input)
    {
        if (!isset($this->mapping[$input])) {
            throw new UnexpectedValueException(sprintf(
                'Cannot find mapping for value "%s"',
                $input
            ));
        }

        return $this->mapping[$input];
    }
}
