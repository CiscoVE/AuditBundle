<?php

namespace WG\AuditBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use WG\AuditBundle\DependencyInjection\WGAuditExtension;

class WGAuditExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadExtensionWithEmptyConfig()
    {
        $configs = array();
        $containerBuilder = new ContainerBuilder( new ParameterBag );
        $extension = new WGAuditExtension();
        $extension->load( $configs, $containerBuilder );
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `user.class` contains IsNotValidUserClass but it is not a valid class name.
     */
    public function throwIfUserClassIsNotValidClass()
    {
        $config = array(
            'user' => array( 'class' => 'IsNotValidUserClass' )
        );
        $configs = array( $config );
        $containerBuilder = new ContainerBuilder( new ParameterBag );
        $extension = new WGAuditExtension();
        $extension->load( $configs, $containerBuilder );
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `user.property` contains foo but the class Symfony\Component\Security\Core\User\User does not have a getter method for that property.
     */
    public function throwIfUserPropertyDoesNotHaveGetter()
    {
        $config = array(
            'user' => array( 'class' => 'Symfony\Component\Security\Core\User\User', 'property' => 'foo' )
        );
        $configs = array( $config );
        $containerBuilder = new ContainerBuilder( new ParameterBag );
        $extension = new WGAuditExtension();
        $extension->load( $configs, $containerBuilder );
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `audit_reference.class` contains IsNotValidAuditReferenceClass but it is not a valid class name.
     */
    public function throwIfAuditReferenceClassIsNotValidClass()
    {
        $config = array(
            'audit_reference' => array( 'class' => 'IsNotValidAuditReferenceClass' )
        );
        $configs = array( $config );
        $containerBuilder = new ContainerBuilder( new ParameterBag );
        $extension = new WGAuditExtension();
        $extension->load( $configs, $containerBuilder );
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `audit_reference.property` contains foo but the class Symfony\Component\Security\Core\User\User does not have a getter method for that property.
     */
    public function throwIfAuditReferencePropertyDoesNotHaveGetter()
    {
        $config = array(
            'audit_reference' => array( 'class' => 'Symfony\Component\Security\Core\User\User', 'property' => 'foo' )
        );
        $configs = array( $config );
        $containerBuilder = new ContainerBuilder( new ParameterBag );
        $extension = new WGAuditExtension();
        $extension->load( $configs, $containerBuilder );
    }
}