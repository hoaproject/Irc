<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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
 * Class \Hoa\Irc\Node.
 *
 * Describe a IRC node.
 */
class Node extends HoaSocket\Node
{
    /**
     * Whether this is basically the first message.
     *
     * @var bool
     */
    protected $_joined   = false;

    /**
     * Username.
     *
     * @var ?string
     */
    protected $_username = null;

    /**
     * Channel.
     *
     * @var ?string
     */
    protected $_channel  = null;



    /**
     * Whether the client has already joined a channel or not.
     */
    public function setJoined(bool $joined): bool
    {
        $old           = $this->_joined;
        $this->_joined = $joined;

        return $old;
    }

    /**
     * Whether the client has already joined a channel or not.
     */
    public function hasJoined(): bool
    {
        return $this->_joined;
    }

    /**
     * Set username.
     */
    public function setUsername(string $username): ?string
    {
        $old             = $this->_username;
        $this->_username = $username;

        return $old;
    }

    /**
     * Get username.
     */
    public function getUsername(): ?string
    {
        return $this->_username;
    }

    /**
     * Set current channel.
     */
    public function setChannel(string $channel): ?string
    {
        $old            = $this->_channel;
        $this->_channel = $channel;

        return $old;
    }

    /**
     * Get current channel.
     */
    public function getChannel(): ?string
    {
        return $this->_channel;
    }
}
