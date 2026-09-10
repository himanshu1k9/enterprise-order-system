<?php

declare(strict_types = 1);

namespace App\Auth;

final class RolePermissions
{
    private const MAP = [
        /** Super Admin Permissions */
        UserRole::SUPER_ADMIN => [
            Permission::PRODUCT_VIEW,
            Permission::PRODUCT_CREATE,
            Permission::PRODUCT_UPDATE,
            Permission::PRODUCT_DELETE,

            Permission::ORDER_CREATE,
            Permission::ORDER_REFUND,
            Permission::ORDER_VIEW,

            Permission::USER_VIEW,
            Permission::USER_CREATE,
            Permission::USER_DELETE,
            Permission::USER_UPDATE,
        ],

        /** Admin Permissions */
        UserRole::ADMIN => [
            Permission::PRODUCT_VIEW,
            Permission::PRODUCT_CREATE,
            Permission::PRODUCT_UPDATE,
            Permission::PRODUCT_DELETE,

            Permission::ORDER_VIEW,
            Permission::ORDER_CREATE,
            Permission::ORDER_REFUND,

            Permission::USER_VIEW,
            Permission::USER_CREATE,
            Permission::USER_UPDATE,
        ],

        /** Staff Permissions */
        UserRole::STAFF => [
            Permission::PRODUCT_VIEW,
            Permission::PRODUCT_CREATE,
            Permission::PRODUCT_UPDATE,

            Permission::ORDER_VIEW,
            Permission::ORDER_CREATE,
        ],

        /** Customer Permissions */
        UserRole::CUSTOMER => [
            Permission::PRODUCT_VIEW,
            Permission::ORDER_CREATE,
            Permission::ORDER_VIEW,
        ]
    ];

    /**
     * Method to get all permissions of a role
     *
     * @param string $role
     * @return array
     */
    public static function permissionFor(string $role): array
    {
        return self::MAP[$role];
    }

    /**
     * Method to check if has permission or not
     *
     * @param string $role
     * @param string $permission
     * @return boolean
     */
    public static function has(string $role, string $permission): bool
    {
        return in_array($permission, self::permissionFor($role), true);
    }
}