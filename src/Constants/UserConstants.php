<?php

namespace App\Common\Constants;

class UserConstants
{
    //User role
    public const USER_ROLE_ADMIN      = 'ROLE_ADMIN';
    public const USER_ROLE_COMMERCIAL = 'ROLE_COMMERCIAL';
    public const USER_ROLE_DELIVERY   = 'ROLE_DELIVERY';
    public const USER_ROLE_CUSTOMER   = 'ROLE_CUSTOMER';
    public const USER_ROLE_DRIVER     = 'ROLE_DRIVER';

    public const USER_ROLE_LIST   = [
        self::USER_ROLE_ADMIN      => 'Admin',
        self::USER_ROLE_COMMERCIAL => 'Commercial',
        self::USER_ROLE_DELIVERY   => 'Livreur',
        self::USER_ROLE_DRIVER     => 'Chauffeur',
        self::USER_ROLE_CUSTOMER   => 'Client',
    ] ;

    //User civility
    public const USER_CIVILITY_MR  = 'Mr';
    public const USER_CIVILITY_MIS = 'Mlle';
    public const USER_CIVILITY_MME = 'Mme';

    public const USER_CIVILITY_LIST   = [
        self::USER_CIVILITY_MR  => 'Monsieur',
        self::USER_CIVILITY_MIS => 'Mademoiselle',
        self::USER_CIVILITY_MME => 'Madame'
    ] ;

    //User status
    public const USER_STATUS_ENABLE  = 1;
    public const USER_STATUS_DISABLE = 2;

    public const USER_STATUS_LIST   = [
        self::USER_STATUS_ENABLE  => 'Active',
        self::USER_STATUS_DISABLE => 'Inactive'
    ] ;
}