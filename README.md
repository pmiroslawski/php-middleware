# Just Another PHP Middleware Stack Implementation 

## Installation
```
    composer req pmiroslawski/php-middleware
```


## Example of use

```
<?php

    use Bit9\Middleware\Dispatcher;
    use Bit9\Middleware\Letter\Envelope;

    // 1. create a middleware dispatcher 
    $dispatcher = new Dispatcher([
        new DummyMiddleware(),         // must implement Bit9\Middleware\Core\MiddlewareInterface
        new Dummy2Middleware(),        // must implement Bit9\Middleware\Core\MiddlewareInterface
    ]);

    // 2. preapare a message to dispatch 
    // As a message you can pass any object of any type and a message might be extended by "stamps" optionally
    // Stamps - as extra data requires optionally by some middlewares
    $message = new Envelope(new Input('test'), [
        new Stamp1(),               // must implement Bit9\Middleware\Letter\Stamp\StampInterface
        new Stamp2()                // must implement Bit9\Middleware\Letter\Stamp\StampInterface
    ]);

    // 3. execute middlewares
    $dispatcher->dispatch($message);
```