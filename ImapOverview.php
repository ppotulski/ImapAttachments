<?php

class ImapOverview extends ArrayObject
{
    private $mailbox;

    /**
     * ImapOverview constructor.
     * @param ImapMailbox $mailbox
     * @param $sequence
     * @throws Exception
     */
    public function __construct(ImapMailbox $mailbox, $sequence)
    {
        $result = imap_fetch_overview($mailbox->getStream(), $sequence);

        if (false === $result) {
            throw new Exception('Overview failed: ' . imap_last_error());
        }

        $this->mailbox = $mailbox;

        foreach ($result as $overview) {
            if (!isset($overview->subject)) {
                $overview->subject = '';
            } else {
                $overview->subject = Imap::decodeToUTF8($overview->subject);
            }
        }

        parent::__construct($result);
    }
}