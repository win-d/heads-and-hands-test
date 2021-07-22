<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Класс модели для коротких ссылок
 */
class ShortLink extends Model
{
    /**
     * @var int кол-во символов в токене для генерации короткой ссылки
     */
    const TOKEN_LENGTH = 6;

    /**
     * @var array $fillable - поля, доступные для массового заполнения
     */
    protected $fillable = [
        'token',
        'url',
        'banned',
    ];

    /**
     * Возвращает короткий адрес ссылки
     *
     * @return string адрес страницы в сокращённом виде
     */
    public function getShortUrl(): string
    {
        $serviceUrl = env('SERVICE_URL', '');

        return $serviceUrl . '/' . $this->token;
    }
}
