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
            'phone' => 'Field {field} must be correct phone number',
            'pattern' => 'Field {field} must match the regular expression {pattern}'
        ],
        'ru' => [
            'field' => 'Поле {field} не существует',
            'required' => 'Поле {field} обязательно',
            'string' => 'Поле {field} должно быть строкой',
            'numeric' => 'Поле {field} должно быть числом',
            'phone' => 'Поле {field} имеет не правильный формат',
            'pattern' => 'Поле {field} должно соответствовать регулярному выражению {pattern}'
        ]
    ];
}