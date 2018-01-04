<?php
/**
 * Service to manage Private Users remote access.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
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
 * @package  Services
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Services;

use Zend\ServiceManager\ServiceLocatorInterface;
use Swissbib\VuFind\Db\Row\PuraUser;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Config\Config;
use Libadmin\Institution\InstitutionLoader;

/**
 * Class Pura.
 *
 * @category Swissbib_VuFind2
 * @package  Service
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Pura implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Config.
     *
     * @var array $config
     */
    protected $config;

    /**
     * List of all publishers whith their
     * contracts with libraries as well as
     * url and name and similar information
     *
     * @var array $publishers
     */
    protected $publishers;

    /**
     * Group Mapping
     *
     * @var Config
     */
    protected $groupMapping;

    /**
     * Groups
     *
     * @var Config
     */
    protected $groups;

    /**
     * NationalLicence constructor.
     *
     * @param object $config       Config
     * @param array  $publishers   List of Publishers
     * @param Config $groupMapping Map the institution code to a group (network)
     * @param Config $groups       The indices of the groups in the libadmin array
     */
    public function __construct(
        $config,
        array $publishers,
        Config $groupMapping,
        Config $groups
    ) {
        $this->config = $config;
        $this->publishers = $publishers;
        $this->groupMapping = $groupMapping;
        $this->groups = $groups;
    }

    /**
     * Get the list of publishers which have a contract with this library
     *
     * @param string $libraryCode The library code, for example Z01
     *
     * @return array  PublishersWithContracts with that library
     */
    public function getPublishersForALibrary($libraryCode)
    {
        $publishersWithContracts = [];
        foreach ($this->publishers as $publisher) {
            if ($this->hasContract($libraryCode, $publisher)) {
                array_push($publishersWithContracts, $publisher);
            }
        }
        return $publishersWithContracts;
    }

    /**
     * Get a PuraUser or creates a new one if is not existing in the
     * database.
     *
     * @param string $userNumber id if the pura-user table
     *
     * @return PuraUser $user
     * @throws \Exception
     */
    public function getPuraUser(
        $userNumber
    ) {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $userTable
         */
        $userTable = $this->getTable(
            '\\Swissbib\\VuFind\\Db\\Table\\PuraUser'
        );
        $user = $userTable->getUserById($userNumber);

        return $user;
    }

    /**
     * Get the VufindUser related to the PuraUser
     *
     * @param string $userNumber id if the pura-user table
     *
     * @return PuraUser $user
     * @throws \Exception
     */
    public function getVuFindUser(
        $puraUserId
    ) {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $userTable
         */
        $puraUserTable = $this->getTable(
            '\\Swissbib\\VuFind\\Db\\Table\\PuraUser'
        );
        $user = $puraUserTable->getUserById($puraUserId);

        $vuFindUserId = $user->getVufindUserId();

        $vuFindUserTable = $this->getTable('user');

        $result = $vuFindUserTable->select(['id' => $vuFindUserId])
            ->current();

        return $result;
    }

    /**
     * Retrieve serviceManager instance.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set serviceManager instance.
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocatorInterface
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get Institution Information
     *
     * @param string $libraryCode the library code (for example Z01)
     *
     * @return array Institution information
     */
    public function getInstitutionInfo($libraryCode)
    {
        $institutionLoader = new InstitutionLoader();

        $institutions = $institutionLoader->getGroupedInstitutions();

        $groupCode    = isset($this->groupMapping[$libraryCode]) ?
            $this->groupMapping[$libraryCode] : 'unknown';

        $groupKey = array_search($groupCode, $this->groups->toArray());

        $libraryCodes = array_column(
            $institutions[$groupKey]["institutions"],
            "bib_code"
        );

        $institutionKey = array_search($libraryCode, $libraryCodes);

        $institution = $institutions[$groupKey]["institutions"][$institutionKey];

        return $institution;
    }

    /**
     * Return true if a library has a contract with this publisher
     *
     * @param string $libraryCode the library code, for example Z01
     * @param array  $publisher   array of properties for a specific publisher
     *
     * @return bool
     */
    protected function hasContract($libraryCode, $publisher)
    {
        if (in_array($libraryCode, $publisher["libraries_with_contract"])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a database table object.
     *
     * @param string $table Name of table to retrieve
     *
     * @return \VuFind\Db\Table\Gateway
     */
    protected function getTable($table)
    {
        return $this->getServiceLocator()
            ->get('VuFind\DbTablePluginManager')
            ->get($table);
    }

    /**
     * Return a unique id
     *
     * @return string
     */
    public function createUniqueId()
    {
        /* To make sure it is unique, maybe we could check if the id is already
         * present and generate another one if needed
         */
        $bytes = openssl_random_pseudo_bytes(7);
        $hex   = bin2hex($bytes);

        return strtoupper($hex);
    }
}
