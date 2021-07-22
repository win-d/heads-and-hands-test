<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Класс для обработки api-запросов по сокращённым ссылкам
 */
class ShortLinkController extends Controller
{
    /**
     * @var int максимальное кол-во ссылок в результирующем списке ссылок
     */
    const LINKS_LIMIT = 50;

    /**
     * Возвращает массив ссылок
     *
     * @param Request $request http-запрос
     * @return Collection массив записей, подходящих под условие
     */
    public function index(Request $request): Collection
    {
        if ($request->has('search')) {
            $query = $request->get('search');
            return ShortLink::where('url', 'like', '%' . $query . '%')->limit(static::LINKS_LIMIT)->get();
        }

        return ShortLink::orderBy('id', 'desc')->limit(static::LINKS_LIMIT)->get();
    }

    /**
     * Возвращает информацию по записи с указанным id
     *
     * @param int $id идентфикатор короткой ссылки
     * @return JsonResponse http-ответ с json-содержимым
     */
    public function show(int $id): JsonResponse
    {
        $link = ShortLink::find($id);

        if ($link === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($link);
    }

    /**
     * Создаёт новую короткую ссылку
     *
     * @param Request $request http-запрос
     * @return Response http-ответ
     */
    public function store(Request $request): Response
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'url' => ['required', 'url'],
            'banned' => ['boolean'],
        ]);

        // Если есть ошибки при валидации, возвращаем их
        if ($validator->fails()) {
            return new Response($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $url = $params['url'];
        $banned = $params['banned'] ?? false;
        $link = ShortLink::where('url', $url)->where('banned', $banned)->first();

        // Если ссылка с такими параметрами уже существует, то нам незачем создавать ещё одну такую же
        if ($link !== null) {
            return new Response($link);
        }

        $token = Str::random(ShortLink::TOKEN_LENGTH);
        $link = ShortLink::create([
            'token' => $token,
            'url' => $url,
            'banned' => $banned,
        ]);

        return new Response($link, Response::HTTP_CREATED);
    }

    /**
     * Добавляет блокировку для ссылки
     *
     * @param int id идентификатор короткой ссылки
     * @return JsonResponse http-ответ в виде json
     */
    public function ban(int $id): JsonResponse
    {
        $link = ShortLink::find($id);

        if ($link === null) {
            return new JsonResponse($link, Response::HTTP_NOT_FOUND);
        }

        $link->banned = true;
        $link->save();

        return new JsonResponse($link, Response::HTTP_NO_CONTENT);
    }
}
