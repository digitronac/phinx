<?php

namespace Test\Phinx\Console\Command;

use Symfony\Component\Console\Tester\CommandTester,
    Symfony\Component\Console\Output\StreamOutput,
    Phinx\Config\Config,
    Phinx\Console\Command\Migrate;

class MigrateTest extends \PHPUnit_Framework_TestCase
{
    protected $config = array();

    protected function setUp() {
        $this->config = new Config(array(
            'paths' => array(),
            'environments' => array(
                'default_migration_table' => 'phinxlog',
                'default_database' => 'development',
                'development' => array(
                    'adapter' => 'mysql',
                    'host' => 'fakehost',
                    'name' => 'development',
                    'user' => '',
                    'pass' => '',
                    'port' => 3006,
                )
            )
        ));
    }

    public function testExecute()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setConfig($this->config);
        $command->setManager($managerStub);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
        
        $this->assertRegExp('/no environment specified/', $commandTester->getDisplay());
    }
    
    public function testExecuteWithEnvironmentOption()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setConfig($this->config);
        $command->setManager($managerStub);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--environment' => 'fakeenv'));
        $this->assertRegExp('/using environment fakeenv/', $commandTester->getDisplay());
    }
    
    public function testExecuteWithEnvironmentOptionAndValidLocalConfig()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setManager($managerStub);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--environment' => 'testing'));
        
        $config = $command->getConfig();
        $this->assertRegExp('/using environment testing/', $commandTester->getDisplay());
        // user loaded from local conf
        $this->assertEquals('local-root', $config['environments']['testing']['user']);
    }
    
    public function testExecuteWithEnvironmentOptionAndValidCustomLocationLocalConfig()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setManager($managerStub);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--environment' => 'testing', '--configuration' => realpath(__DIR__ . '/../../../../phinx.yml')));
        
        $config = $command->getConfig();
        $this->assertRegExp('/using environment testing/', $commandTester->getDisplay());
        // user loaded from local conf
        $this->assertEquals('local-root', $config['environments']['testing']['user']);
    }
    
    public function testExecuteWithEnvironmentOptionAndInvalidLocalConfig()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setManager($managerStub);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--environment' => 'testing', '--local-configuration' => 'invalidconfigfile.yml'));
        
        $config = $command->getConfig();
        $this->assertRegExp('/using environment testing/', $commandTester->getDisplay());
        // user loaded from default conf
        $this->assertEquals('root', $config['environments']['testing']['user']);
    }
    
    public function testDatabaseNameSpecified()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add(new Migrate());
        
        // setup dependencies
        $output = new StreamOutput(fopen('php://memory', 'a', false));
        
        $command = $application->find('migrate');
        
        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(), array($this->config, $output));
        $managerStub->expects($this->once())
                    ->method('migrate');
        
        $command->setConfig($this->config);
        $command->setManager($managerStub);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
        $this->assertRegExp('/using database development/', $commandTester->getDisplay());
    }
}
