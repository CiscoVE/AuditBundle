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
    public function shouldSetNullAsDefaultControlUser()
    {
        $config = array( 'wg_audit' => array() );
        $processedConfig = $this->processConfiguration( $config );
        $this->assertArrayHasKey( 'control_user', $processedConfig );
        $this->assertNull( $processedConfig['control_user'] );
    }

    /**
     * @test
     */
    public function shouldAllowToSetUserClass()
    {
        $config = array( 'wg_audit' => array(
            'user' => array( 'class' => 'Symfony\Component\Security\Core\User\User' )
        ));
        $this->processConfiguration( $config );
    }
}
