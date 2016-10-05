<?php
/**
 * Table Definition for national_licence_user.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 *
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Db\Table;

use VuFind\Db\Table\Gateway;
use VuFind\Db\Table\User;
use Zend\Db\Sql\Select;

class NationalLicenceUser extends Gateway
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'national_licence_user',
            'Swissbib\VuFind\Db\Row\NationalLicenceUser'
        );
    }

    /**
     * Get user by id.
     *
     * @param int $id
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     */
    public function getUserById($id)
    {
        return $this->select(['id' => $id])
            ->current();
    }

    /**
     * Create a new National licence user row.
     *
     * @param string $persistentId Edu-id persistent id
     * @param array $fieldsValue
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser $user
     *
     * @throws \Exception
     */
    public function createNationalLicenceUserRow(
        $persistentId,
        array $fieldsValue = []
    )
    {
        if (empty($persistentId)) {
            throw new \Exception(
                'The persistent-id is mandatory for creating a National Licence User'
            );
        }

        /** @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalUser */
        $nationalUser = $this->createRow();

        $nationalUser->setPersistentId($persistentId);

        foreach ($fieldsValue as $key => $value) {
            $nationalUser->$key = $value;
        }
        /** @var User $userTable */
        $userTable = $this->getDbTable('user');

        /** @var \VuFind\Db\Row\User $user */
        $user = $userTable->getByUsername($persistentId);
        // If there is already a user registered in the system in the use table,
        // we link it to the
        // national_licence_user table.
        if ($user) {
            // Link table User to NationalLicenceUser
            $nationalUser->setUserId($user->id);
        }
        $savedUser = $nationalUser->save();
        if (empty($savedUser)) {
            throw new \Exception('Impossible to create the National Licence user.');
        }

        return $nationalUser;
    }

    /**
     * Update user fields.
     *
     * @param int $persistentId           User persistent id
     * @param array $fieldsValues         Array of fields => value to update
     * @param array $fieldsValuesRelation Array of fields of the relation table=>
     *                                    value to update
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     * @throws \Exception
     */
    public function updateRowByPersistentId(
        $persistentId,
        array $fieldsValues,
        array $fieldsValuesRelation = null
    )
    {
        $nationalLicenceUser = $this->getUserByPersistentId($persistentId);
        foreach ($fieldsValues as $key => $value) {
            if ($nationalLicenceUser->$key !== $value) {
                $nationalLicenceUser->$key = $value;
            }
        }
        if (!empty($fieldsValuesRelation)) {
            $user = $nationalLicenceUser->getRelUser();
            foreach ($fieldsValuesRelation as $key => $value) {
                if ($user->$key !== $value) {
                    $user->$key = $value;
                }
            }
            $user->save();
        }
        $nationalLicenceUser->save();

        return $nationalLicenceUser;
    }

    /**
     * Get user by persistent_id.
     *
     * @param string $persistentId
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     *
     * @throws \Exception
     */
    public function getUserByPersistentId($persistentId)
    {
        if (empty($persistentId)) {
            throw new \Exception('Cannot fetch user with empty persistent_id');
        }
        /** @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalLicenceUser */
        $nationalLicenceUser = $this->select(['persistent_id' => $persistentId])
            ->current();
        if (empty($nationalLicenceUser)) {
            return;
        }
        /** @var User $userTable */
        $userTable = $this->getDbTable('user');
        $relUser = $userTable->getByUsername(
            $nationalLicenceUser->getPersistentId()
        );
        $nationalLicenceUser->setRelUser($relUser);

        return $nationalLicenceUser;
    }

    /**
     * Get list of all National licence users with relative VuFind users.
     * @return array
     * @throws \Exception
     */
    public function getList()
    {
        /** @var User $userTable */
        $userTable = $this->getDbTable('user');

        $nationalLicenceUsers = $this->select(function (Select $select) {
            $select->where->greaterThan('id', 0);
        });
        $arr_resultSet = [];
        /** @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalLicenceUser */
        foreach ($nationalLicenceUsers as $nationalLicenceUser) {
            /** @var \VuFind\Db\Row\User $user */
            $user = $userTable->getByUsername($nationalLicenceUser->getPersistentId());
            $nationalLicenceUser->setRelUser($user);
            $arr_resultSet[] = $nationalLicenceUser;
        }

        return $arr_resultSet;
    }
}
