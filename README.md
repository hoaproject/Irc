![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Irc ![state](http://central.hoa-project.net/State/Irc)

This library allows to write an IRC client, and interact through listeners and
simple methods.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/irc`](https://packagist.org/packages/hoa/irc):

```json
{
    "require": {
        "hoa/irc": "~0.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

We propose a quick overview of a simple client that joins a channel and
interacts to mentions. Next, we will enhance this client with a WebSocket server
to receive external messages.

### Interact to mentions

The `Hoa\Irc\Client` proposes the following listeners: `open`, `join`,
`message`, `private-message`, `mention`, `other-message`, `ping`, `kick`,
`invite` and `error`.

In order to connect to an IRC server, we have to use a socket client, such as:

```php
$uri    = 'tcp://chat.freenode.org:6667';
$client = new Hoa\Irc\Client(new Hoa\Socket\Client($uri));
```

It's also possible to use the socket URI directly in the constructor:

```php
$client = new Hoa\Irc\Client('tcp://chat.freenode.org:6667');
```

Then, we attach our listeners. When the connexion will be opened, we will join a
channel, for example `#hoaproject` with the `Gordon` username:

```php
$client->on('open', function (Hoa\Event\Bucket $bucket) {
    $bucket->getSource()->join('Gordon', '#hoaproject');

    return;
});
```

Next, when someone will mention `Gordon`, we will answer `What?`:

```php
$client->on('mention', function (Hoa\Event\Bucket $bucket) {
    $data    = $bucket->getData();
    $message = $data['message']; // do something with that.

    $bucket->getSource()->say(
        $data['from']['nick'] . ': What?'
    );

    return;
});
```

Finally, to run the client:

```php
$client->run();
```

### Include a WebSocket server

We can add a WebSocket server to receive external messages we will forward to
the IRC client. Thus, the beginning of our program will look like:

```php
$ircUri = 'tcp://chat.freenode.org:6667';
$wsUri  = 'tcp://127.0.0.1:8889';

$group  = new Hoa\Socket\Connection\Group();
$client = new Hoa\Irc\Client(new Hoa\Socket\Client($ircUri));
$server = new Hoa\Websocket\Server(new Hoa\Socket\Server($wsUri));

$group[] = $server;
$group[] = $client;
```

Then, we will forward all messages received by the WebSocket server to the IRC
client:

```php
$server->on('message', function (Hoa\Event\Bucket $bucket) use ($client) {
    $data = $bucket->getData();
    $client->say($data['message']);

    return;
});
```

Finally, to run both the IRC client and WebSocket server:

```php
$group->run();
```

To send a message to the WebSocket server, we can use a WebSocket client in CLI:

```sh
$ echo 'foobar' | hoa websocket:client -s 127.0.0.1:8889
```

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
