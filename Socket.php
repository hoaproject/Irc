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
    const ENTTYPE_ISUSER = 'isuser';

    /**
     * Entity type: ischannel.
     *
     * @const string
     */
    const ENTTYPE_ISCHANNEL = 'ischannel';

    /**
     * Host type: isserver.
     *
     * @const string
     */
    const HOSTTYPE_ISSERVER = 'isserver';

    /**
     * Host type: isnetwork.
     *
     * @const string
     */
    const HOSTTYPE_ISNETWORK = 'isnetwork';

    /**
     * Entity.
     * @var string
     */
    private $_entity;

    /**
     * Entity type.
     * @var string
     */
    private $_entityType = self::ENTTYPE_ISCHANNEL;

    /**
     * Host type.
     * @var string
     */
    private $_hostType;

    /**
     * Options list.
     * @var array
     */
    private $_options;

    /**
     * Username.
     * @var string
     */
    private $_username;

    /**
     * Password.
     * @var string
     */
    private $_password;



    /**
     * Constructor
     *
     * @param   string   $uri         Socket URI.
     * @param   boolean  $secured     Whether the connection is secured.
     * @param   string   $entity      Entity to access directly.
     * @param   string   $username    Username to log in.
     * @param   string   $password    Password to authenticate user.
     * @param   array    $flags       List of flags to caracterize the entity.
     * @param   array    $options     List of options to use for the connection.
     */
    public function __construct(
        $uri,
        $secured       = false,
        $entity        = null,
        $username      = null,
        $password      = null,
        array $flags   = null,
        array $options = []
    ) {
        parent::__construct($uri);

        $this->_secured = $secured;

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

        return new static(
            'tcp://' . $parsed['host'] . ':' . $port,
            $secure
        );
    }
}

/**
 * Register `irc://` and `ircs://` transports.
 */
HoaSocket\Transport::register('irc',  ['Hoa\Irc\Socket', 'transportFactory']);
HoaSocket\Transport::register('ircs', ['Hoa\Irc\Socket', 'transportFactory']);
