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
        self::VOTE_EXCEPTION_NO  => 'Non',
        self::VOTE_EXCEPTION_YES => 'Oui',
    ];

    //Vote status
    public const VOTE_STATUS_NOT_VERIFY       = 1;
    public const VOTE_STATUS_VERIFY_NOT_VALID = 2;
    public const VOTE_STATUS_VERIFY_VALID     = 3;
    //public const VOTE_STATUS_CORRECTION_OK    = 4;

    public const VOTE_STATUS_LIST   = [
        self::VOTE_STATUS_NOT_VERIFY        => 'Pas vérifié',
        self::VOTE_STATUS_VERIFY_NOT_VALID  => 'Non validé',
        self::VOTE_STATUS_VERIFY_VALID      => 'Validé',
        //self::VOTE_STATUS_CORRECTION_OK     => 'Corrigé'
    ] ;
}