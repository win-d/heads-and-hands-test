# Тестовое задание для Heads & Hands

**Задача**:

- Необходимо реализовать WEB сервис для сокращения ссылок и администрирование через REST API
- WEB часть для пользователей реализовать через стандартный MVC
- Сервис состоит из одной страницы на которой располагается поле для ввода ссылки и кнопки отправить, после отправки
  пользователю возвращается сокращенная ссылка
- Для администрирования ссылок админом нужно реализовать REST API методы:
    - Метод получения списка ссылок с дополнительным простым поиском (LIKE) по ссылке
    - Метод получения детальной информации о ссылке
    - Метод для добавления новой ссылки
    - Метод для блокировки ссылки

**Результат**:

- По переходу по сокращенной ссылке должно быть перенаправление на незабаненный исходный адрес.
- Токен короткой ссылки должен быть рандомным, уникальным, состоящий из цифр и букв (разного регистра) длиной 6
  символов.
- Для получения доступа к административным методам реализовать базовую авторизацию.
- Для реализации желательно использовать Laravel.

---

## Развёртывание сервиса

1. Создать файл `.env` (содержимое можно взять из `.env.example`).
2. Заполнить разделы с подключением к базе данных (адрес, пользователь, пароль, ...) и переменную `SERVICE_URL` (
   протокол и хост).
3. Выполнить миграции (`php artisan migrate`).
4. Наполнить базу тестовыми данными (`php artisan db:seed`).
5. Убедиться, что короткие ссылки формируются корректно (`php artisan test --env=local` &mdash; для локальной
   среды, `php artisan test --env=testing` &mdash; для тестовой среды, если необходимо)

## Использование web-части

1. При переходе на адрес из `SERVICE_URL` у нас появляется форма генерации короткой ссылки. Поле с ссылкой обязательно к
   заполнению, а его содержимое должно представлять url (валидация на стороне браузера).
2. После отправки формы выполняется проверка, что нам пришёл именно url (валидация на стороне сервера).
3. Если в базе уже есть _незаблокированная_ ссылка с таким url, пользователю возвращается её короткий адрес.
4. Если такой ссылки в базе нет, генерируется новая короткая ссылка и возвращается её короткий url.
5. Если короткая ссылка _не заблокирована_, то при её открытии в браузере выполняется редирект на целевой url.
6. Если же такой короткой ссылки у нас нет или она заблокирована, возвращается страница 404.

## Использование api-части

При выполнении запросов к сервису нужно добавлять токен как get-параметр. Получить его можно из таблицы `users` из
колонки `api_token` (заполняется при наполнении базы данных). Таким образом обеспечивается базовая авторизация &mdash;
мы можем выполнять запросы только от определённых аккаунтов.

### Получение ссылок

    GET
    https://{SERVICE_URL}/api/links?api_token={token}
    https://{SERVICE_URL}/api/links?search={query}&api_token={token}

В ответ получаем список ссылок, соответствующих условию (не более 50 штук). Есть возможность поиска по колонке `url` в
формате `LIKE %query%` &mdash; для этого нужно добавить get-параметр `search`.

### Информация об отдельной ссылке

    GET
    https://{SERVICE_URL}/api/links/{id}?api_token={token}

В ответ получаем информацию об отдельной ссылке или пустой JSON. Возможные коды ответа:

- 200 (OK) &mdash; если такая ссылка найдена
- 404 (Not Found) &mdash; если ссылка не найдена

### Добавление новой ссылки

    POST
    https://{SERVICE_URL}/api/links?api_token={token}

В теле запроса необходимо указать обязательный параметр `url` (целевой адрес ссылки). Также можно указать необязательный
параметр `banned` (заблокирована ссылка или нет), по умолчанию ссылка не будет считаться заблокированной. Оба параметра
проходят валидацию на стороне сервера. Если у нас уже есть короткая ссылка с такими параметрами, то новая не создаётся.

Возможные коды ответа:

- 200 (OK) &mdash; если такая ссылка уже есть в базе данных
- 201 (Created) &mdash; если короткая ссылка успешно добавлена в базу данных
- 400 (Bad Request) &mdash; если есть ошибки валидации в переданных параметрах

### Блокировка ссылки

    PATCH
    https://{SERVICE_URL}/api/links/{id}/ban?api_token={token}

Выполняется блокировка ссылки с указанным идентификатором. Тело запроса не учитывается. Возможные коды ответа:

- 204 (No Content) &mdash; если ссылка успешно заблокирована
- 404 (Not Found) &mdash; если ссылка с указанным id не была найдена
