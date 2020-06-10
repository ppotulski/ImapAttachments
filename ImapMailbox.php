<?php
/*
 * if call to: undefined function imap_open()
 * then
 * sudo apt-get update -y
 * sudo apt-get install -y php7.2-imap
 */
require_once('ImapMessage.php');

class ImapMailbox implements IteratorAggregate, Countable
{
    private $stream;

    /**
     * ImapMailBox constructor.
     * @param $hostname
     * @param $username
     * @param $password
     * @throws Exception
     */
    public function __construct($hostname, $username, $password)
    {
        $stream = imap_open($hostname, $username, $password);

        if (false === $stream) {
            throw new Exception('Connect failed: ' . imap_last_error());
        }

        $this->stream = $stream;
    }

    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return stdClass
     * @throws Exception
     */
    public function check()
    {
        $info = imap_check($this->stream);

        if (false === $info) {
            throw new Exception('Check failed: ' . imap_last_error());
        }

        return $info;
    }

    /**
     * @param string $criteria
     * @param int $options
     * @param int $charset
     * @return ImapMessage[]
     * @throws Exception
     */
    public function search($criteria, $options = null, $charset = null)
    {
        $emails = imap_search($this->stream, $criteria, $options, $charset);

        if (false === $emails) {
            throw new Exception('Search failed: ' . imap_last_error());
        }

        foreach ($emails as &$email) {
            $email = $this->getMessageByNumber($email);
        }

        return $emails;
    }

    /**
     * @param int $number
     * @return ImapMessage
     */
    public function getMessageByNumber($number)
    {
        return new ImapMessage($this, $number);
    }

    public function getOverview($sequence = null)
    {
        if (null === $sequence) {
            $sequence = sprintf('1:%d', count($this));
        }

        return new ImapOverview($this, $sequence);
    }

    /**
     * @return Traversable|void
     * @throws Exception
     */
    public function getIterator()
    {
        return $this->getOverview()->getIterator();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count()
    {
        return $this->check()->Nmsgs;
    }

}