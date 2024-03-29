<?php

namespace Ddeboer\DataImportBundle\DataImport\Writer;

/**
 * Class allowing multiple writers to write in same stream
 *
 * @author Benoît Burnichon <bburnichon@gmail.com>
 */
class StreamMergeWriter extends AbstractStreamWriter
{
    /** @var string */
    private $discriminantField = 'discr';
    /** @var AbstractStreamWriter[] */
    private $writers = array();

    /**
     * Set Discriminant field
     *
     * @param string $discriminantField
     * @return $this
     */
    public function setDiscriminantField($discriminantField)
    {
        $this->discriminantField = (string) $discriminantField;
        return $this;
    }

    /**
     * Get Discriminant Field
     *
     * @return string
     */
    public function getDiscriminantField()
    {
        return $this->discriminantField;
    }

    /**
     * @inheritdoc
     */
    public function writeItem(array $item)
    {
        if ((isset($item[$this->discriminantField])
                || array_key_exists($this->discriminantField, $item))
            && $this->hasStreamWriter($key = $item[$this->discriminantField])
        ) {
            $writer = $this->getStreamWriter($key);

            $writer->writeItem($item);
        }

        return $this;
    }

    /**
     * Set Stream writers
     * @param AbstractStreamWriter[] $writers
     *
     * @return $this
     */
    public function setStreamWriters(array $writers)
    {
        foreach ($writers as $key => $writer) {
            $this->setStreamWriter($key, $writer);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param AbstractStreamWriter $writer
     * @return $this
     */
    public function setStreamWriter($key, AbstractStreamWriter $writer)
    {
        $writer->setStream($this->getStream());
        $writer->setCloseStreamOnFinish(false);
        $this->writers[$key] = $writer;

        return $this;
    }

    /**
     * Get a previously registered Writer
     *
     * @param string $key
     * @return AbstractStreamWriter
     */
    public function getStreamWriter($key)
    {
        return $this->writers[$key];
    }

    /**
     * Get list of registered Writers
     *
     * @return AbstractStreamWriter[]
     */
    public function getStreamWriters()
    {
        return $this->writers;
    }

    /**
     * Is a writer registered for key?
     *
     * @param string $key
     * @return bool
     */
    public function hasStreamWriter($key)
    {
        return isset($this->writers[$key]);
    }

    public function setStream($stream)
    {
        parent::setStream($stream);
        foreach ($this->getStreamWriters() as $writer) {
            $writer->setStream($stream);
        }

        return $this;
    }
}
