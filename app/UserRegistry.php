<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRegistry extends Model
{
    protected $guarded = [];

    public function getSummaryData() {
        $data = [
            'user' => $this->id
        ];

        return $data;
    }
}
