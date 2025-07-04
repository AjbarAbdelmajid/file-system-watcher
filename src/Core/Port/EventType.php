<?php
namespace App\Core\Port;

enum EventType: string
{
    case CREATE = 'create';
    case MODIFY = 'modify';
    case DELETE = 'delete';
}