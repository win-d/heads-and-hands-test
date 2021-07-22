<?php

namespace Database\Seeders;

use App\Models\ShortLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        $this->seedShortLinks();
    }

    /**
     * Наполняет данными таблицу коротких ссылок
     *
     * @return void
     */
    protected function seedShortLinks(): void
    {
        $now = Carbon::now();
        $links = [
            [
                'token' => Str::random(ShortLink::TOKEN_LENGTH),
                'url' => 'https://google.com',
                'banned' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'token' => Str::random(ShortLink::TOKEN_LENGTH),
                'url' => 'https://ya.ru',
                'banned' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'token' => Str::random(ShortLink::TOKEN_LENGTH),
                'url' => 'https://bing.com',
                'banned' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('short_links')->insert($links);
    }
}
