<?php
/**
 * KnowledgeCardController.php
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;
use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class KnowledgeCardController
 *
 * Provides information to be rendered in knowledge cards (light-boxes).
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class KnowledgeCardController extends AbstractDetailsController
{
    /**
     * KnowledgeCardController constructor.
     *
     * @param ServiceLocatorInterface $sm The service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->getConfig()->KnowledgeCard;
    }

    /**
     * /Page/Detail/Person/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        return parent::personAction();
    }

    /**
     * /Page/Detail/Subject/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        return parent::subjectAction();
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel     $viewModel              The view model
     * @param string        $id                     The id
     * @param ElasticSearch $driver                 The record driver
     * @param array         $bibliographicResources The bibliographic resources
     * @param array         $subjectIds             The subject ids
     * @param array         $subjects               The subjects
     *
     * @return void
     */
    protected function addData(
        ViewModel &$viewModel, string $id, ElasticSearch $driver,
        array $bibliographicResources, array $subjectIds, array $subjects
    ) {
        // Nothing to add yet
    }

}
