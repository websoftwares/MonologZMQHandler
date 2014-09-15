<?php
namespace Websoftwares\Tests\Monolog;
use Websoftwares\Monolog\Handler\ZMQHandler;
use Monolog\Logger;
/**
 * Class ZMQHandlerTest
 */
class ZMQHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateAsObjectSucceeds()
    {
        $context = new \ZMQContext();
        $publisher = $this->getMock("ZMQSocket", null, [$context, \ZMQ::SOCKET_PUSH]);
        $this->assertInstanceOf('Websoftwares\Monolog\Handler\ZMQHandler', new ZMQHandler($publisher));
    }

    public function testHandlerModeDontWaitPushSucceeds()
    {
        $messages = [];

        $context = new \ZMQContext();
        $publisher = $this->getMock("ZMQSocket", ["send"], [$context, \ZMQ::SOCKET_PUSH]);

        $publisher->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function ($message, $mode) use (&$messages) {
                $messages[] = [$message, $mode];
            }));

        $expected = [
            [
            'message' => 'test',
            'context' => [
                'data' => [],
                'foo' => 34,
            ],
            'level' => 300,
            'level_name' => 'WARNING',
            'channel' => 'test',
            'extra' => [],
            ],
           \ZMQ::MODE_DONTWAIT
        ];

        $handler = new ZMQHandler($publisher);
        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass(), 'foo' => 34]);
        $handler->handle($record);
        $this->assertCount(1, $messages);
        $messages[0][0] = json_decode($messages[0][0], true);
        unset($messages[0][0]['datetime']);
        $this->assertEquals($expected, $messages[0]);
    }

    public function testHandlerSndMorePubSucceeds()
    {
        $messages = [];

        $context = new \ZMQContext();
        $publisher = $this->getMock("ZMQSocket", ["send"], [$context, \ZMQ::SOCKET_PUB]);

        $publisher->expects($this->any())
            ->method('send')
            ->will($this->returnCallback(function ($channelMessage, $mode) use (&$messages) {
                $messages[] = [$channelMessage, $mode];
            }));

        $expected = [
            [
            'message' => 'test',
            'context' => [
                'data' => [],
                'foo' => 34,
            ],
            'level' => 300,
            'level_name' => 'WARNING',
            'channel' => 'test',
            'extra' => [],
            ],
           null
        ];

        $handler = new ZMQHandler($publisher, \ZMQ::MODE_SNDMORE, true);
        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass(), 'foo' => 34]);
        $handler->handle($record);
        $this->assertCount(2, $messages);
        $this->assertEquals(["test",\ZMQ::MODE_SNDMORE],$messages[0]);
        $messages[1][0] = json_decode($messages[1][0], true);
        unset($messages[1][0]['datetime']);
        $this->assertEquals($expected, $messages[1]);
    }

    /**
     * Extracted from Monolog\TestCase i did not get it to work by extending the class,
     * Class 'Monolog\TestCase' not found is the error i kept getting,
     * I suspect someting with the PSR-4 autoloader.
     * @return array Record
     */
    protected function getRecord($level = Logger::WARNING, $message = 'test', $context = array())
    {
        return array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );
    }

    /**
     * @expectedException Exception
     */
    public function testInstantiateAsObjectFails()
    {
        $context = new \ZMQContext();
        $publisher = $this->getMock("ZMQSocket", null, [$context, ]);
        new ZMQHandler($publisher);
    }
}
