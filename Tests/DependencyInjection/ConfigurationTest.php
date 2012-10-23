<?php

namespace WG\AuditBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;

use WG\AuditBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected function processConfiguration( array $configs )
    {
        $configuration = new Configuration();
        $processor = new Processor();
        return $processor->processConfiguration( $configuration, $configs );
    }
    
    /**
     * @test
     */
    public function shouldAllowUsageWithoutAnyConfiguration()
    {
        $this->processConfiguration( array() );
    }

    /**
     * @test
     */
    public function shouldAllowSettingScalarControlUser()
    {
        $config = array( 'wg_audit' => array(
            'control_user' => true
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     */
    public function shouldSetFalseAsDefaultControlUser()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'control_user', $processedConfig );
        $this->assertFalse( $processedConfig['control_user'] );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid value "foo" for option `control_user`.
     */
    public function throwIfControlUserSettingNotBoolean()
    {
        $config = array( 'wg_audit' => array(
            'control_user' => 'foo'
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "wg_audit.control_user". Expected scalar, but got array.
     */
    public function throwIfControlUserNotScalar()
    {
        $config = array( 'wg_audit' => array(
            'control_user' => array()
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     */
    public function shouldSetNullAsDefaultUserClass()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'user', $processedConfig );
        $this->assertArrayHasKey( 'class', $processedConfig['user'] );
        $this->assertNull( $processedConfig['user']['class'] );
    }

    /**
     * @test
     */
    public function shouldSetIdAsDefaultUserProperty()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'user', $processedConfig );
        $this->assertArrayHasKey( 'property', $processedConfig['user'] );
        $this->assertEquals( 'id', $processedConfig['user']['property'] );
    }

    /**
     * @test
     */
    public function shouldAllowToSetScalarUserClass()
    {
        $config = array( 'wg_audit' => array(
            'user' => array( 'class' => 'Symfony\Component\Security\Core\User\User' )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     */
    public function shouldAllowToSetScalarUserProperty()
    {
        $config = array( 'wg_audit' => array(
            'user' => array( 'property' => 'uniqueId' )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "wg_audit.user.class". Expected scalar, but got array.
     */
    public function throwIfUserClassNotScalar()
    {
        $config = array( 'wg_audit' => array(
            'user' => array( 'class' => array() )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "wg_audit.user.property". Expected scalar, but got array.
     */
    public function throwIfUserPropertyNotScalar()
    {
        $config = array( 'wg_audit' => array(
            'user' => array( 'property' => array() )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     */
    public function shouldSetNullAsDefaultAuditReferenceClass()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'audit_reference', $processedConfig );
        $this->assertArrayHasKey( 'class', $processedConfig['audit_reference'] );
        $this->assertNull( $processedConfig['audit_reference']['class'] );
    }

    /**
     * @test
     */
    public function shouldSetIdAsDefaultAuditReferenceProperty()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'audit_reference', $processedConfig );
        $this->assertArrayHasKey( 'property', $processedConfig['audit_reference'] );
        $this->assertEquals( 'id', $processedConfig['audit_reference']['property'] );
    }

    /**
     * @test
     */
    public function shouldAllowToSetScalarAuditReferenceClass()
    {
        $config = array( 'wg_audit' => array(
            'audit_reference' => array( 'class' => 'Symfony\Component\Security\Core\User\User' )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     */
    public function shouldAllowToSetScalarAuditReferenceProperty()
    {
        $config = array( 'wg_audit' => array(
            'audit_reference' => array( 'property' => 'uniqueId' )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "wg_audit.audit_reference.class". Expected scalar, but got array.
     */
    public function throwIfAuditReferenceClassNotScalar()
    {
        $config = array( 'wg_audit' => array(
            'audit_reference' => array( 'class' => array() )
        ));
        $this->processConfiguration( $config );
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "wg_audit.audit_reference.property". Expected scalar, but got array.
     */
    public function throwIfAuditReferencePropertyNotScalar()
    {
        $config = array( 'wg_audit' => array(
            'audit_reference' => array( 'property' => array() )
        ));
        $this->processConfiguration( $config );
    }
}
