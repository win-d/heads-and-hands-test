<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Генерация короткой ссылки</title>
    <link href=" {{ asset('app.css') }}" rel="stylesheet">
</head>
<body>
<main>
    <h1>Генератор коротких ссылок</h1>
    <form method="post" action="{{ route('web.links.store') }}">
        @csrf
        <label for="urlField">Введите url</label>
        <input type="url" id="urlField" name="url" required>
        <button>Сгенерировать</button>

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="color-warning text-bold">{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @isset($shortUrl)
            <p><span class="text-bold">Ваша короткая ссылка</span>: <code>{{ $shortUrl }}</code></p>
        @endisset
    </form>
</main>
</body>
</html>
