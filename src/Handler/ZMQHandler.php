<?php
namespace Websoftwares\Monolog\Handler;
/**
* Sends your logs over a ZeroMQ (ØMQ) socket.
*
* @package Websoftwares
* @subpackage Monolog
* @license http://opensource.org/licenses/MIT
* @author Boris <boris@websoftwar.es>
*/
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;

class ZMQHandler extends AbstractProcessingHandler
{
    /**
     * @var \ZMQSocket
     */
    protected $zmqSocket;

    /**
     * @see http://api.zeromq.org/4-0:zmq-sendmsg
     * @var int
     */
    protected $zmqMode = \ZMQ::MODE_DONTWAIT;

    /**
     * @var boolean
     */
    protected $multipart = false;

    /**
     * @param \zmqSocket $zmqSocket instance of \ZMQSocket for now only the send types allowed
     * @param int        $zmqMode   ZMQ mode
     * @param boolean    $multipart send multipart message
     * @param int        $level
     * @param bool       $bubble    Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        \zmqSocket $zmqSocket,
        $zmqMode = \ZMQ::MODE_DONTWAIT,
        $multipart = false,
        $level = Logger::DEBUG,
        $bubble = true)
    {
        $zmqSocketType = $zmqSocket->getSocketType();

        // If u need the the Bi-directional types make a PR
        if (! $zmqSocketType == \ZMQ::SOCKET_PUB || ! $zmqSocketType == \ZMQ::SOCKET_PUSH) {
            throw new \Exception("Invalid socket type used, only PUB, PUSH allowed.");
        }

        $this->zmqSocket = $zmqSocket;
        $this->zmqMode = $zmqMode;
        $this->multipart = $multipart;
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if ($this->multipart) {
            $this->zmqSocket->send($record['channel'],$this->zmqMode);
            $this->zmqSocket->send($record['formatted']);
        } else {
            $this->zmqSocket->send($record["formatted"],$this->zmqMode);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new JsonFormatter();
    }
}
