<?php

namespace App\Http\Requests;

trait CommonRules
{

    protected function itemRules()
    {
        return [
            'receive_items' => [
                'required',
                Rule::in(['supplier', 'description'])
            ]
        ];

        
    }
}
