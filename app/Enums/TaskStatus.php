<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
