<?php


namespace App\Constants;


class Content
{
    //Role 
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_PROVIDER = 'ROLE_PROVIDER';
    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_PROVIDER => 'Fournisseur',
        self::ROLE_CUSTOMER => 'Client',
    ];
    const USER_DISABLED = 0;
    const USER_ENABLE   = 1;
    
}