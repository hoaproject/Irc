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

namespace Hoa\Irc\Test\Unit;

use Hoa\Irc\Socket as SUT;
use Hoa\Socket as HoaSocket;
use Hoa\Test;

/**
 * Class \Hoa\Irc\Test\Unit\Socket.
 *
 * Test suite for the socket class.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Socket extends Test\Unit\Suite
{
    public function case_is_a_socket()
    {
        $this
            ->when($result = new SUT('tcp://hoa-project:net:8889'))
            ->then
                ->object($result)
                    ->isInstanceOf('Hoa\Socket\Socket');
    }

    public function case_constructor()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $entity   = 'channel',
                $username = 'user',
                $password = 'pass',
                $flags    = [SUT::CHANNEL_ENTITY, SUT::NETWORK_HOST],
                $options  = ['option1' => 'value', 'option2' => 0]
            )
            ->when($result = new SUT(
                $uri,
                $secured,
                $entity,
                $username,
                $password,
                $flags,
                $options
            ))
            ->then
                ->integer($result->getAddressType())
                    ->isEqualTo(SUT::ADDRESS_DOMAIN)
                ->string($result->getTransport())
                    ->isEqualTo('tcp')
                ->string($result->getAddress())
                    ->isEqualTo('hoa-project.net')
                ->integer($result->getPort())
                    ->isEqualTo(8889)
                ->boolean($result->isSecured())
                    ->isTrue()
                ->string($result->getEntity())
                    ->isEqualTo($entity)
                ->string($result->getUsername())
                    ->isEqualTo($username)
                ->string($result->getPassword())
                    ->isEqualTo($password)
                ->string($result->getEntityType())
                    ->isEqualTo(SUT::CHANNEL_ENTITY)
                ->string($result->getHostType())
                    ->isEqualTo(SUT::NETWORK_HOST)
                ->array($result->getOptions())
                    ->string['option1']->isEqualTo('value')
                    ->integer['option2']->isEqualTo(0);
    }

    public function case_get_entity()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $entity   = 'entity',
                $socket   = new SUT($uri, $secured, $entity)
            )
            ->when($result = $socket->getEntity())
            ->then
                ->string($result)
                    ->isEqualTo($entity);
    }

    public function case_get_username()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $username = 'user',
                $socket   = new SUT($uri, $secured, null, $username)
            )
            ->when($result = $socket->getUsername())
            ->then
                ->string($result)
                    ->isEqualTo($username);
    }

    public function case_get_password()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $password = 'password',
                $socket   = new SUT($uri, $secured, null, null, $password)
            )
            ->when($result = $socket->getPassword())
            ->then
                ->string($result)
                    ->isEqualTo($password);
    }

    public function case_get_entity_type_and_empty_host_type()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $entity   = 'entity',
                $flags    = [SUT::USER_ENTITY],
                $socket   = new SUT($uri, $secured, $entity, null, null, $flags)
            )
            ->then
                ->string($socket->getEntityType())
                    ->isEqualTo(SUT::USER_ENTITY)
                ->variable($socket->getHostType())
                    ->isNull();
    }

    public function case_get_host_type_and_empty_entity_type()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $entity   = 'entity',
                $flags    = [SUT::NETWORK_HOST],
                $socket   = new SUT($uri, $secured, $entity, null, null, $flags)
            )
            ->then
                ->string($socket->getHostType())
                    ->isEqualTo(SUT::NETWORK_HOST)
                ->string($socket->getEntityType())
                    ->isEqualTo(SUT::CHANNEL_ENTITY);
    }

    public function case_get_entity_and_host_type()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $entity   = 'entity',
                $flags    = [SUT::SERVER_HOST, SUT::USER_ENTITY],
                $socket   = new SUT($uri, $secured, $entity, null, null, $flags)
            )
            ->then
                ->string($socket->getHostType())
                    ->isEqualTo(SUT::SERVER_HOST)
                ->string($socket->getEntityType())
                    ->isEqualTo(SUT::USER_ENTITY);
    }

    public function case_get_options()
    {
        $this
            ->given(
                $uri      = 'tcp://hoa-project.net:8889',
                $secured  = true,
                $options  = ['option1' => 0, 'option2' => 'value'],
                $socket   = new SUT(
                    $uri, $secured, null, null, null, [], $options
                )
            )
            ->when($result = $socket->getOptions())
            ->then
                ->array($result)
                    ->integer['option1']->isEqualTo(0)
                    ->string['option2']->isEqualTo('value');
    }

    public function case_is_irc_transport_registered()
    {
        $this->_case_is_transport_registered('irc');
    }

    public function case_is_ircs_transport_registered()
    {
        $this->_case_is_transport_registered('ircs');
    }

    protected function _case_is_transport_registered($transport)
    {
        return
            $this
                ->when($result = HoaSocket\Transport::exists($transport))
                ->then
                    ->boolean($result)
                        ->isTrue();
    }

    public function case_transport_factory_invalid_URI()
    {
        $this
            ->exception(function () {
                SUT::transportFactory('foo');
            })
                ->isInstanceOf('Hoa\Irc\Exception');
    }

    public function case_transport_unsecured_domain_with_port_with_channel()
    {
        $this->_case_transport_factory(
            'irc://hoa-project.net/foobar,isserver',
            [
                'type'       => SUT::ADDRESS_DOMAIN,
                'address'    => 'hoa-project.net',
                'port'       => 6667,
                'secured'    => false,
                'entity'     => 'foobar',
                'entityType' => SUT::CHANNEL_ENTITY,
                'hostType'   => SUT::SERVER_HOST
            ]
        );
    }

    public function case_transport_unsecured_domain_with_fragment_channel()
    {
        $this->_case_transport_factory(
            'irc://hoa-project.net/#foobar,isnetwork?key=abcd',
            [
                'type'       => SUT::ADDRESS_DOMAIN,
                'address'    => 'hoa-project.net',
                'port'       => 6667,
                'secured'    => false,
                'entity'     => 'foobar',
                'entityType' => SUT::CHANNEL_ENTITY,
                'hostType'   => SUT::NETWORK_HOST,
                'options'    => ['key' => 'abcd']
            ]
        );
    }

    public function case_transport_unsecured_domain_with_encoded_channel()
    {
        $this->_case_transport_factory(
            'irc://user@hoa-project.net/%23foobar',
            [
                'type'       => SUT::ADDRESS_DOMAIN,
                'address'    => 'hoa-project.net',
                'port'       => 6667,
                'secured'    => false,
                'entity'     => 'foobar',
                'entityType' => SUT::CHANNEL_ENTITY,
                'username'   => 'user',
                'password'   => null
            ]
        );
    }

    public function case_transport_unsecured_domain_with_isuser_enttype()
    {
        $this->_case_transport_factory(
            'irc://user:pass@hoa-project.net/foobar,isuser?option=value',
            [
                'type'       => SUT::ADDRESS_DOMAIN,
                'address'    => 'hoa-project.net',
                'port'       => 6667,
                'secured'    => false,
                'entity'     => 'foobar',
                'entityType' => SUT::USER_ENTITY,
                'options'    => ['option' => 'value'],
                'username'   => 'user',
                'password'   => 'pass'
            ]
        );
    }

    protected function _case_transport_factory($uri, array $expect)
    {
        if (!isset($expect['options'])) {
            $expect['options'] = [];
        }
        if (!isset($expect['entityType'])) {
            $expect['entityType'] = SUT::CHANNEL_ENTITY;
        }
        if (!isset($expect['hostType'])) {
            $expect['hostType'] = null;
        }
        if (!isset($expect['entity'])) {
            $expect['entity'] = null;
        }
        if (!isset($expect['username'])) {
            $expect['username'] = null;
        }
        if (!isset($expect['password'])) {
            $expect['password'] = null;
        }

        return
            $this
                ->when($result = SUT::transportFactory($uri))
                ->then
                    ->object($result)
                        ->isInstanceOf(SUT::class)
                    ->integer($result->getAddressType())
                        ->isEqualTo($expect['type'])
                    ->string($result->getTransport())
                        ->isEqualTo('tcp')
                    ->string($result->getAddress())
                        ->isEqualTo($expect['address'])
                    ->integer($result->getPort())
                        ->isEqualTo($expect['port'])
                    ->boolean($result->isSecured())
                        ->isEqualTo($expect['secured'])
                    ->variable($result->getEntity())
                        ->isEqualTo($expect['entity'])
                    ->variable($result->getUsername())
                        ->isEqualTo($expect['username'])
                    ->variable($result->getPassword())
                        ->isEqualTo($expect['password'])
                    ->variable($result->getEntityType())
                        ->isEqualTo($expect['entityType'])
                    ->variable($result->getHostType())
                        ->isEqualTo($expect['hostType'])
                    ->array($result->getOptions())
                        ->isEqualTo($expect['options'])
            ;
    }
}
