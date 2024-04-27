<?php

namespace Frame\Validator;

trait ErrorTrait
{
    private array $messages = [
        'en' => [
            'field' => 'Field {field} does not exist',
            'required' => 'Field {field} is required',
            'string' => 'Field {field} must be a string',
            'numeric' => 'Field {field} must be a number',
            'pattern' => 'Field {field} must match the regular expression {pattern}'
        ],
        'ru' => [
            'field' => 'Поле {field} не существует',
            'required' => 'Поле {field} обязательно',
            'string' => 'Поле {field} должно быть строкой',
            'numeric' => 'Поле {field} должно быть числом',
            'pattern' => 'Поле {field} должно соответствовать регулярному выражению {pattern}'
        ]
    ];
}