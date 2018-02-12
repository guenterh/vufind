<?php
/**
 * SwitchApiServiceTest.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_NationalLicence
 * @author   Simone Cogno  <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\NationalLicence;

use ReflectionClass;
use Swissbib\Services\SwitchApi;
use VuFindTest\Unit\TestCase as VuFindTestCase;
use SwissbibTest\Bootstrap;
use Zend\ServiceManager\ServiceManager;
use Zend\Config\Config;
use Zend\Config\Reader\Ini as IniReader;

/**
 * Class SwitchApiServiceTest.
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_NationalLicence
 * @author   Simone Cogno  <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class SwitchApiServiceTest extends VuFindTestCase
{
    /**
     * Reflection class.
     *
     * @var ReflectionClass $switchApiService
     */
    protected $switchApiServiceReflected;
    /**
     * Switch api service
     *
     * @var SwitchApi $switchApiService
     */
    protected $switchApiServiceOriginal;
    /**
     * Config.
     *
     * @var array $config
     */
    protected $externalIdTest;
    /**
     * Service manager.
     *
     * @var ServiceManager $sm
     */
    protected $sm;

    /**
     * Set up service manager and National Licence Service.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();

        /* create a Mock of VuFind\Config\PluginManager to read dedicated
         * configuration files for testing
         */
        $configPM = $this->getMockBuilder('VuFind\Config\PluginManager')
            ->disableOriginalConstructor()->getMock();
        $configPM
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([$this, 'myCallback']));

        $this->switchApiServiceOriginal = new SwitchApi($configPM, $this->sm);
        $this->switchApiServiceReflected = new ReflectionClass(
            $this->switchApiServiceOriginal
        );

        $this->externalIdTest = $configPM->get('NationalLicences')
        ['SwitchApi']['external_id_test'];
    }

    /**
     * Test the unsetNationalCompliantFlag method.
     *
     * @return void
     * @throws \Exception
     */
    public function testUnsetNationalCompliantFlag()
    {
        $externalId = $this->externalIdTest;
        $isOnGroup = $this->switchApiServiceOriginal
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if (!$isOnGroup) {
            $this->switchApiServiceOriginal
                ->setNationalCompliantFlag($externalId);
        }
        self::assertEquals(
            true, $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
        $this->switchApiServiceOriginal->unsetNationalCompliantFlag($externalId);
        self::assertEquals(
            false, $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
    }

    /**
     * Test the setNationalCompliantFlag method.
     *
     * @return void
     * @throws \Exception
     */
    public function testSetNationalCompliantFlag()
    {
        $externalId = $this->externalIdTest;
        $isOnGroup = $this->switchApiServiceOriginal
            ->userIsOnNationalCompliantSwitchGroup($externalId);
        if ($isOnGroup) {
            $method = $this->switchApiServiceReflected
                ->getMethod('createSwitchUser');
            $method->setAccessible(true);
            $internalId = $method->invoke(
                $this->switchApiServiceOriginal,
                $externalId
            );

            $method = $this->switchApiServiceReflected
                ->getMethod('removeUserToNationalCompliantGroup');
            $method->setAccessible(true);
            $method->invoke($this->switchApiServiceOriginal, $internalId);
        }
        self::assertEquals(
            false,
            $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
        $this->switchApiServiceOriginal->setNationalCompliantFlag($externalId);
        self::assertEquals(
            true,
            $this->switchApiServiceOriginal
                ->userIsOnNationalCompliantSwitchGroup($externalId)
        );
    }

    /**
     * This just test if a call to the back channel endpoint did not fail.
     *
     * @return void
     */
    public function testGetUserUpdatedInformation()
    {
        //what could be done :
        //use reflection class to test the protected method
        //getNationalLicenceUserCurrentInformation

        //$res = $this->switchApiServiceOriginal
        //->getUserUpdatedInformation('L34Mbh0HJUmUM6h2Rql/DNF9oRk=',
        // 'https://eduid.ch/idp/shibboleth!https://test.swissbib.ch/
        //shibboleth!L34Mbh0HJUmUM6h2Rql/DNF9oRk=');
        //$this->unitPrint($res);
    }

    /**
     * Workaround to print in the unit test console.
     *
     * @param mixed $variable Variable
     *
     * @return void
     */
    public function unitPrint($variable)
    {
        fwrite(STDERR, print_r($variable, true));
    }

    /**
     * Get a reflection class for the SwitchApi service. This is used for call
     * several private or protected methods.
     *
     * @param SwitchApi $originalClass Original class
     *
     * @return ReflectionClass
     */
    protected function getReflectedClass($originalClass)
    {
        $class = new ReflectionClass($originalClass);

        return $class;
    }

    /**
     * Callback function for the Vufind\Config\PluginManager Mock
     *
     * @return Config
     */
    public function myCallback()
    {
        $arguments = func_get_args();
        $arg = $arguments[0];

        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/NationalLicence/fixtures/';
        $iniReader = new IniReader();

        $configNL = new Config(
            $iniReader->fromFile($path . 'NationalLicencesTest.ini')
        );
        $configUserSwitchAPI = new Config(
            $iniReader->fromFile($path . 'config.ini')
        );

        if ($arg == "NationalLicences") {
            return $configNL;
        } else if ($arg == "config") {
            return $configUserSwitchAPI;
        } else {
            return null;
        }
    }
}
