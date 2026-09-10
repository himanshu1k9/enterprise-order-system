<?php

declare(strict_types = 1);

namespace App\Auth;

/** ==========================================
 * Final Keyword told us we don't want another
 * class extends it.
 * It's simply a centralized collection of
 * permission constants.
 * ===========================================*/
final class Permission
{
    /** Product Related Permissions */
    public const PRODUCT_VIEW = 'product.view';
    public const PRODUCT_CREATE = 'product.create';
    public const PRODUCT_UPDATE = 'product.update';
    public const PRODUCT_DELETE = 'product.delete';

    /** Order Related Permissions */
    public const ORDER_VIEW = 'order.view';
    public const ORDER_CREATE = 'order.create';
    public const ORDER_REFUND = 'order.refund';

    /** User Related Permissions */
    public const USER_VIEW = 'user.view';
    public const USER_CREATE = 'user.create';
    public const USER_UPDATE = 'user.update';
    public const USER_DELETE = 'user.delete';
}