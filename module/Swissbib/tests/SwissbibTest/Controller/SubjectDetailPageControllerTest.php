<?php
/**
 * SubjectDetailPageControllerTest.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  SwissbibTest\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibTest\Controller;

use Swissbib\Controller\SubjectDetailPageController;
use VuFindTest\Unit\TestCase as VuFindTestCase;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SubjectDetailPageControllerTest
 *
 * @category VuFind
 * @package  SwissbibTest\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class SubjectDetailPageControllerTest extends VuFindTestCase
{
    /**
     * @var SubjectDetailPageController $cut
     */
    private $cut;

    protected function setUp()
    {
        parent::setUp();
        $config = new Config(["config" => new Config(["DetailPage" => ""])]);
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator->method("get")->willReturn($config);
        $this->cut = new SubjectDetailPageController($serviceLocator);
    }

    public function testAddData()
    {
        $method = self::getMethod('addData');
        $actual = $method->invokeArgs($this->cut, []);

        //$this->assertEquals("", $actual);
    }
}
