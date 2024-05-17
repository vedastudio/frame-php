<?php declare(strict_types=1);

namespace Frame\Validator;

trait ErrorTrait
{
    private array $messages = [
        'en' => [
            'field' => 'Field {field} does not exist',
            'required' => 'Field {field} is required',
            'unique' => 'Field {field} must be a unique',
            'string' => 'Field {field} must be a string',
            'numeric' => 'Field {field} must be a number',
            'phone' => 'Field {field} must be correct phone number',
            'pattern' => 'Field {field} must match the regular expression {pattern}'
        ],
        'ru' => [
            'field' => 'Поле не существует',
            'required' => 'Поле обязательно',
            'unique' => 'Значение должно быть уникальным',
            'string' => 'Введите текст',
            'numeric' => 'Введите число',
            'phone' => 'Неправильный формат номера',
            'pattern' => 'Значение не соответствует шаблону'
        ]
    ];
}