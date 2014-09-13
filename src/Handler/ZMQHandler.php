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
     * @param \zmqSocket $zmqSocket instance of \ZMQSocket
     * @param int        $zmqMode   \ZMQ::MODE_SNDMORE // use this if u want to send multi-part with channel.
     * @param int        $level
     * @param bool       $bubble    Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        \zmqSocket $zmqSocket,
        $zmqMode = \ZMQ::MODE_DONTWAIT,
        $level = Logger::DEBUG,
        $bubble = true)
    {
        $this->zmqSocket = $zmqSocket;
        $this->zmqMode = $zmqMode;
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if ($this->zmqMode == \ZMQ::MODE_SNDMORE) {
            $this->zmqSocket->send($record['channel'],$this->zmqMode);
            $this->zmqSocket->send($record["formatted"]);
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
