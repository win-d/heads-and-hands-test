<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Класс для обработки web-запросов по сокращённым ссылкам
 */
class ShortLinkController extends Controller
{
    /**
     * Показывает страницу добавления короткой ссылки
     *
     * @return View
     */
    public function index(): View
    {
        return view('index');
    }

    /**
     * Генеририует и сохраняет короткую ссылку
     *
     * @param Request $request - HTTP-запрос
     * @return View
     */
    public function store(Request $request): View
    {
        $request->validate([
            'url' => ['required', 'url'],
        ]);

        $url = $request->get('url');
        /** @var ShortLink $link */
        $link = ShortLink::where('url', $url)->where('banned', false)->first();

        /*
         * Если у нас уже есть незаблокированная ссылка с таким url, то нет нужды генерировать новую.
         * Вместо этого возвращаем существующую.
         * */
        if ($link !== null) {
            $shortUrl = $link->getShortUrl();
            return view('index')->with(['shortUrl' => $shortUrl]);
        }

        // Генерируем короткую ссылку
        $token = Str::random(ShortLink::TOKEN_LENGTH);
        $link = new ShortLink([
            'token' => $token,
            'url' => $url,
            'banned' => false,
        ]);

        $link->save();
        $shortUrl = $link->getShortUrl();

        return view('index')->with(['shortUrl' => $shortUrl]);
    }

    /**
     * Выполняет редирект по короткой ссылке
     *
     * @param string $token токен короткой ссылки
     * @return RedirectResponse
     */
    public function redirect(string $token): RedirectResponse
    {
        // Если длина токена не соответствует заданной, выводим ошибку
        if (strlen($token) !== ShortLink::TOKEN_LENGTH) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $link = ShortLink::where('token', $token)->first();

        // Если ссылки с таким токеном у нас нет или же она заблокирована, выводим ошибку
        if ($link === null || $link->banned) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return redirect($link->url);
    }
}
