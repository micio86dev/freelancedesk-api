<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'company', 'email', 'phone', 'notes'])]
class Client extends ApiModel
{
}
