<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Irc;

use Hoa\Socket as HoaSocket;

/**
 * Class \Hoa\Irc\Socket.
 *
 * IRC specific socket and transports.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Socket extends HoaSocket
{
    /**
     * Entity type: isuser.
     *
     * @const string
     */
    const USER_ENTITY = 'isuser';

    /**
     * Entity type: ischannel.
     *
     * @const string
     */
    const CHANNEL_ENTITY = 'ischannel';

    /**
     * Host type: isserver.
     *
     * @const string
     */
    const SERVER_HOST = 'isserver';

    /**
     * Host type: isnetwork.
     *
     * @const string
     */
    const NETWORK_HOST = 'isnetwork';

    /**
     * Entity.
     * @var string
     */
    protected $_entity;

    /**
     * Entity type.
     * @var string
     */
    protected $_entityType = self::CHANNEL_ENTITY;

    /**
     * Host.
     * @var string
     */
    protected $_host;

    /**
     * Host type.
     * @var string
     */
    protected $_hostType;

    /**
     * Options list.
     * @var array
     */
    protected $_options;

    /**
     * Username.
     * @var string
     */
    protected $_username;

    /**
     * Password.
     * @var string
     */
    protected $_password;



    /**
     * Constructor
     *
     * @param   string   $uri         Socket URI.
     * @param   boolean  $secured     Whether the connection is secured.
     * @param   string   $entity      Entity to access directly.
     * @param   string   $username    Username to log in.
     * @param   string   $password    Password to authenticate user.
     * @param   array    $flags       List of flags to characterize the entity.
     * @param   array    $options     List of options to use for the connection.
     */
    public function __construct(
        $uri,
        $secured       = false,
        $entity        = null,
        $username      = null,
        $password      = null,
        array $flags   = [],
        array $options = []
    ) {
        parent::__construct($uri);

        $this->_secured  = $secured;
        $this->_entity   = $entity;

        if (null === $this->_entity && !empty($flags)) {
            throw new Exception("Cannot define flags without defining entity.");
        }

        $flags = array_filter($flags);
        if (count($flags) > 2) {
            throw new Exception(
                "Cannot have more than two flags [enttype, hosttype]."
            );
        }
        while (count($flags) > 0) {
            $flag = array_pop($flags);

            if (
                $flag === self::CHANNEL_ENTITY ||
                $flag === self::USER_ENTITY
            ) {
                $this->_entityType = $flag;

                continue;
            }
            if (
                $flag === self::NETWORK_HOST ||
                $flag === self::SERVER_HOST
            ) {
                $this->_hostType = $flag;

                continue;
            }

            throw new Exception('Unknown flag "%s" given.', 0, [$flag]);
        }

        $this->_username = $username;
        $this->_password = $password;
        $this->_options  = $this->parseOptions($options);

        if ($this->_entityType === self::USER_ENTITY) {
            preg_match(
                '/^(?<nick>[^!]+)(!(?<user>[^@]+)(@(?<host>.+))?)?$/',
                $this->_entity,
                $parsed
            );

            $this->_entity   = $parsed['nick'];
            $this->_username = isset($parsed['user'])?$parsed['user']:$this->_username;
            $this->_host     = isset($parsed['host'])?$parsed['host']:null;
        }

        return;
    }

    /**
     * Retrieve Irc socket username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Retrieve Irc socket password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Retrieve Irc socket entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Retrieve Irc socket entity type.
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->_entityType;
    }

    /**
     * Retrieve Irc socket host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * Retrieve Irc socket host type.
     *
     * @return string
     */
    public function getHostType()
    {
        return $this->_hostType;
    }

    /**
     * Retrieve Irc socket options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Parse given options using current context.
     * @see    https://tools.ietf.org/html/draft-butcher-irc-url-04#section-2.6
     * @param  array  $options
     * @return array
     */
    protected function parseOptions(array $options)
    {
        //When entity is a channel, only supported option is `key`.
        if ($this->_entityType === self::CHANNEL_ENTITY) {
            return array_intersect_key(
                $options,
                ['key' => true]
            );
        }

        //If there are invalid options, they are simply ignored.
        return [];
    }

    /**
     * Factory to create a valid `Hoa\Socket\Socket` object.
     *
     * @param   string  $socketUri    URI of the socket to connect to.
     * @return  void
     */
    public static function transportFactory($socketUri)
    {
        $parsed = parse_url($socketUri);

        if (false === $parsed || !isset($parsed['host'])) {
            throw new Exception(
                'URL %s seems invalid, cannot parse it.',
                0,
                $socketUri
            );
        }

        $secure =
            isset($parsed['scheme'])
                ? 'ircs' === $parsed['scheme']
                : false;

        /**
         * Regarding RFC
         * https://tools.ietf.org/html/draft-butcher-irc-url-04#section-2.4,
         * port 194 is likely to be a more “authentic” server, however at this
         * time the majority of IRC non secure servers are available on port
         * 6667.
         */
        $port =
            isset($parsed['port'])
                ? $parsed['port']
                : (true === $secure
                    ? 994
                    : 6667);

        $parsed = array_merge([
            'path'     => '',
            'fragment' => '',
            'query'    => '',
            'user'     => null,
            'pass'     => null
        ], $parsed);
        $complement =
            ltrim($parsed['path'], '/') .
            $parsed['fragment'] .
            (empty($parsed['query'])?'':'?' . $parsed['query']);

        $entity   = null;
        $flags    = [];
        $options  = [];

        $pattern  = '/^((#|%23)?(?<entity>[^,\?]+))(,(?<flags>[^\?]+))?(\?(?<options>.*))?$/';
        if (1 === preg_match($pattern, $complement, $matches)) {
            $entity = $matches['entity'];
            if (isset($matches['flags'])) {
                $flags = explode(',', $matches['flags']);
            }
            if (isset($matches['options']) && !empty($matches['options'])) {
                array_map(function ($item) use (&$options) {
                    $tmp = explode('=', $item);
                    $options[$tmp[0]] = $tmp[1];
                }, explode('&', $matches['options']));
            }
        }

        return new static(
            'tcp://' . $parsed['host'] . ':' . $port,
            $secure,
            $entity,
            $parsed['user'],
            $parsed['pass'],
            $flags,
            $options
        );
    }
}

/**
 * Register `irc://` and `ircs://` transports.
 */
HoaSocket\Transport::register('irc',  ['Hoa\Irc\Socket', 'transportFactory']);
HoaSocket\Transport::register('ircs', ['Hoa\Irc\Socket', 'transportFactory']);
