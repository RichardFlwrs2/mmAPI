<?php

namespace App\Http\Controllers\Record;

use App\Record;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class RecordController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - S H O W
    // ----------------------------------------------------------------------------------------------------- //
    public function show(Record $record)
    {
        return $this->showOne($record);
    }
}
