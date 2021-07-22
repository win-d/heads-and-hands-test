<?php

namespace Tests\Unit;

use App\Models\ShortLink;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование класса модели для коротких ссылок
 */
class ShortLinkTest extends TestCase
{
    /**
     * Тестирование правильности формирования короткой ссылки
     *
     * @return void
     */
    public function testGetShortUrl(): void
    {
        $token = Str::random(ShortLink::TOKEN_LENGTH);
        $link = new ShortLink([
            'token' => $token,
        ]);
        $shortUrl = $link->getShortUrl();

        $this->assertStringStartsWith('http', $shortUrl, 'Invalid SERVICE_URL'); // Ссылка начинается с http/https
        $this->assertStringEndsWith($token, $shortUrl); // Заканчивается на токен, после которого ничего нет
    }
}
