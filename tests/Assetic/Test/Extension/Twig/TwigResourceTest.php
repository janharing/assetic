<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Extension\Twig\TwigResource;

class TwigResourceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('\Twig\Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }
    }

    public function testInvalidTemplateNameGetContent()
    {
        $loader = $this->prophesize('\Twig\Loader\LoaderInterface');
        if (!method_exists('\Twig\Loader\LoaderInterface', 'getSourceContext')) {
            $loader->willImplement('\Twig\Loader\SourceContextLoaderInterface');
        }

        $loader->getSourceContext('asdf')->willThrow(new \Twig\Error\LoaderError(''));

        $resource = new TwigResource($loader->reveal(), 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    /**
     * @group legacy
     */
    public function testInvalidTemplateNameGetContentWithLegacyLoader()
    {
        if (!method_exists('\Twig\Loader\LoaderInterface', 'getSource')) {
            $this->markTestSkipped('This test does not make sense on Twig 2.x.');
        }

        $loader = $this->getMockBuilder('\Twig\Loader\LoaderInterface')->getMock();
        $loader->expects($this->once())
            ->method('getSource')
            ->with('asdf')
            ->will($this->throwException(new \Twig\Error\LoaderError('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    public function testInvalidTemplateNameIsFresh()
    {
        $loader = $this->getMockBuilder('\Twig\Loader\LoaderInterface')->getMock();
        $loader->expects($this->once())
            ->method('isFresh')
            ->with('asdf', 1234)
            ->will($this->throwException(new \Twig\Error\LoaderError('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertFalse($resource->isFresh(1234));
    }
}
