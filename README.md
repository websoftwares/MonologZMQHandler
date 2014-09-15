#MonologZMQHandler (v0.2.1)
This package lets u send your Monolog logs over a ZeroMQ (ØMQ) socket.

[![Build Status](https://api.travis-ci.org/websoftwares/MonologZMQHandler.png)](https://travis-ci.org/websoftwares/MonologZMQHandler)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/websoftwares/MonologZMQHandler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/websoftwares/MonologZMQHandler/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/websoftwares/MonologZMQHandler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/websoftwares/yo/?branch=master)
[![Dependencies Status](https://depending.in/websoftwares/MonologZMQHandler.png)](http://depending.in/websoftwares/MonologZMQHandler)

##System requirements
ZeroMQ library needs to be available on your system on Ubuntu easily install with APT package manager:
```
sudo apt-get install libzmq3 libzmq3-dev
```

The PHP ZeroMQ  extension is required follow the installation from the zeromq [guide](http://zeromq.org/bindings:php).

## Installing via Composer (recommended)

Install composer in your project:
```
curl -s http://getcomposer.org/installer | php
```

Create a composer.json file in your project root:
```php
{
    "require": {
		"websoftwares/monolog-zmq-handler": ~2.0"
    }
}
```

Install via composer
```
php composer.phar install
```

## Usage
Basic usage of the `ZMQHandler` class.

```php
use Websoftwares\Monolog\Handler\ZMQHandler;
use Monolog\Logger;

// Create ZeroMQ PUB socket
$context = new \ZMQContext();
$publisher = new \ZMQSocket($context, ZMQ::SOCKET_PUB);
$publisher->bind("tcp://*:5556");

// Create new handler class instance
// First argument is an instance of \ZMQSocket type PUSH OR PUB
// Second argument \ZMQ::MODE_*
// Thirth argument set to true wil make the first message the log channel. (multipart)
// Default operation is \ZMQ::MODE_DONTWAIT
$handler = new ZMQHandler($publisher, \ZMQ::MODE_SNDMORE, true); 

// Create new logger instance
$log = new Logger('channelName');
$log->pushHandler($handler);

// Log something
$log->addWarning("Something is going wrong...");
```
## Examples
in the examples/ folder u can find some basic examples for PUB/SUB and PUSH/PULL

PUB/SUB
```
php publisher.php
php subscriber.php
```

PUSH/PULL
```
php pusher.php
php puller.php
```

## Changelog

- v0.2.0: Allowing multiple \ZMQ::MODE_* options thx for @reenl pointing this out.
- v0.1.0: Initial 

## Testing
In the tests folder u can find several tests.

## Acknowledgement
The logging package [Monolog](https://github.com/Seldaek/monolog) developers.

## License
The [MIT](http://opensource.org/licenses/MIT "MIT") License (MIT).
