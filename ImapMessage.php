<?php

require_once('ImapAttachments.php');

class ImapMessage
{
    private $mailbox;
    private $number;
    private $stream;

    public function __construct(ImapMailbox $mailbox, $number)
    {
        $this->mailbox = $mailbox;
        $this->number = $number;
        $this->stream = $mailbox->getStream();
    }

    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return string
     */
    public function fetchBody($number)
    {
        return imap_fetchbody($this->stream, $this->number, $number);
    }

    /**
     * @return object|void
     * @throws Exception
     */
    public function fetchStructure()
    {
        $structure = imap_fetchstructure($this->stream, $this->number);

        if (false === $structure) {
            throw new Exception('FetchStructure failed: ' . imap_last_error());
        }

        return $structure;
    }

    /**
     * @param $mailbox
     * @throws Exception
     */
    public function moveToSpecificMailbox($mailbox)
    {
        $messageMoved = imap_mail_move($this->stream, $this->number, $mailbox);

        if (false === $messageMoved) {
            throw new Exception('Message move to ' . $mailbox . ' failed: ' . imap_last_error());
        }
    }

    /**
     * @return ImapAttachments
     * @throws Exception
     */
    public function getAttachments()
    {
        return new ImapAttachments($this);
    }

    public function __toString()
    {
        return (string)$this->number;
    }
}