<?php

namespace App\Enum;


enum StateEnum: string
{
    case VALITED = 'validé';
    case VALITED_COLLABORATOR_POSTOP = 'validé-collaborateur-postop';
    case VALITED_MANAGER = 'validé-manager';
    case VALITED_MANAGER_POSTOP = 'validé-manager-postop';
    case VALITED_HR = 'validé-rh';
    case VALITED_HR_POSTOP = 'validé-rh-postop';
    case REFUSED_MANAGER = 'refus-manager';
    case REFUSED_HR = 'refus-rh';
    case REFUSED = 'refus';
    case PENDING_MANAGER_VALIDATION = 'attente-manager';
    case PENDING_HR_VALIDATION = 'attente-rh';
    case REOPEN = 'réouvert';
    case REOPEN_MANAGER_POSTOP = 'réouvert-manager-postop';
}