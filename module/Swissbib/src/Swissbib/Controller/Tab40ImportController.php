<?php
/**
 * Swissbib Tab40ImportController
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Swissbib\Tab40Import\Importer as Tab40Importer;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;

/**
 * Import tab40.xxx files and convert them to label files
 * Use this controller over the command line
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Tab40ImportController extends AbstractActionController
{
    /**
     * Service manager
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceLocator = $sm;
    }

    /**
     * Import file as label data
     *
     * @throws \RuntimeException
     *
     * @return String
     */
    public function importAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException(
                'You can only use this action from a console!'
            );
        }

        $network = $request->getParam('network');
        $locale = $request->getParam('locale');
        $sourceFile = $request->getParam('source');

        $importResult = $this->getImporter()->import($network, $locale, $sourceFile);

        echo "Imported language data from tab40 file\n";
        echo "Source: $sourceFile\n";
        echo "Network: $network\n";
        echo "Locale: $locale\n";
        echo "\nResult:\n";
        echo "Written File: {$importResult->getFilePath()}\n";
        echo "Items imported: {$importResult->getRecordCount()}\n";

        return '';
    }

    /**
     * Tab40Importer
     *
     * @return Tab40Importer
     */
    protected function getImporter()
    {
        return $this->serviceLocator->get('Swissbib\Tab40Importer');
    }
}
