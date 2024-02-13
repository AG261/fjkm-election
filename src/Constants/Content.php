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
    
    const VOTE_EXCEPTION_NO = 0;
    const VOTE_EXCEPTION_YES = 1;

    const VOTE_EXCEPTIONS = [
        self::VOTE_EXCEPTION_NO => 'Non',
        self::VOTE_EXCEPTION_YES => 'Oui',
    ];
}