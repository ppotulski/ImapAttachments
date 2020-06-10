<?php

require_once('Imap.php');
require_once('ImapAttachment.php');

class ImapAttachments extends ArrayObject
{
    private $message;

    /**
     * ImapAttachments constructor.
     * @param ImapMessage $message
     * @throws Exception
     */
    public function __construct(ImapMessage $message)
    {
        $array = $this->setMessage($message);
        parent::__construct($array);
    }

    /**
     * @param ImapMessage $message
     * @return array
     * @throws Exception
     */
    private function setMessage(ImapMessage $message)
    {
        $this->message = $message;
        return $this->parseStructure($message->fetchStructure());
    }

    /**
     * @param $structure
     * @return array
     */
    private function parseStructure($structure)
    {
        $attachments = [];

        if (!isset($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $index => $part) {
            if (!$part->ifdisposition) continue;

            $attachment = new stdClass;
            $attachment->isAttachment = false;
            $attachment->number = $index + 1;
            $attachment->bytes = $part->bytes;
            $attachment->encoding = $part->encoding;
            $attachment->filename = null;
            $attachment->name = null;
            $part->ifdparameters
            && ($attachment->filename = $this->getAttribute($part->dparameters, 'filename'))
            && $attachment->isAttachment = true;
            $part->ifparameters
            && ($attachment->name = $this->getAttribute($part->parameters, 'name'))
            && $attachment->isAttachment = true;

            $attachment->isAttachment
            && $attachments[] = new ImapAttachment($this->message, $attachment);
        }

        return $attachments;
    }

    /**
     * @param $params
     * @param $name
     * @return string|null
     */
    private function getAttribute($params, $name)
    {
        foreach ($params as $object) {
            if ($object->attribute == $name) {
                return Imap::decodeToUTF8($object->value);
            }
        }

        return null;
    }

}