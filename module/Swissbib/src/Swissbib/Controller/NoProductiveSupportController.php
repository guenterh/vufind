<?php
/**
 * Swissbib NoProductiveSupportController
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

use VuFind\Controller\AbstractBase;

/**
 * Swissbib NoProductiveSupportController
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class NoProductiveSupportController extends AbstractBase
{
    /**
     * GH: we don't want to support administrative services like upgrade and
     * install in the (productive) environment
     * Even in development and test, these services are not used by swissbib
     * because we have our own procedures
     * tailored for our purposes.
     * Once we have a better overview of the new authorization system used by
     * VuFind (RBAC) which supports granted permissions
     * for roles this perhaps a little bit rude mechanism might be replaced.
     * use the invokable controller definitions in module config to override
     * the default VuFind routes
     *
     * @return mixed
     */
    public function homeAction()
    {
        return $this->forwardTo('Error', 'home');
    }
}
