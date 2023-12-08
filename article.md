## LunarPHP<a id="lunarphp"></a>

[Lunar](https://github.com/lunarphp/lunar) - популярное eCommerce решение с открытым кодом, поставляется как Laravel пакет.
API Lunar находится в стадии разработки, его нет в релизах, но реализацию уже можно [изучить](https://github.com/lunarphp/lunar/tree/feature/api/packages/api).
API построен по спецификации [JSON:API](https://jsonapi.org/), используется библиотека [laravel-json-api/laravel](https://github.com/laravel-json-api/laravel).

Процесс разработки API выглядит следующим образом:
1. Создание моделей и связей
2. Создание схем ресурсов
3. Создание правил валидации для ресурсов
4. Создание политик для контроля доступа
5. Создание API сервера и его регистрация
6. Реализация бизнес требований


### Создание моделей<a id="создание-моделей"></a>

Модели в Lunar - это Eloquent модели Laravel.
Lunar не добавляет никаких специфичных требований к созданию моделей, поэтому этот процесс соответствует [официальной документации Laravel](https://laravel.com/docs/10.x/eloquent).
В результате получилось 3 модели: [Category](https://github.com/incrize/lunar-blog/blob/main/src/Models/Category.php), [Post](https://github.com/incrize/lunar-blog/blob/main/src/Models/Post.php), [PostTag](https://github.com/incrize/lunar-blog/blob/main/src/Models/PostTag.php).
В качестве модели отвечающей за автора и модератора используется модель `User`, которая создается автоматически при создании дефолтного Laravel проекта.


### Создание схем ресурсов<a id="создание-схем-ресурсов"></a>

Схемы - это специфика [laravel-json-api](https://laraveljsonapi.io/docs/3.0/schemas/).
Схема предназначена для описания API ресурса и содержит в себе как минимум:
- Список полей-атрибутов доступных в API ресурса
- Список полей-связей доступных в API ресурса
- Список возможных фильтров в API ресурса
- Настройки постраничной навигации и сортировки в API ресурса

Для каждой модели необходимо описать свою схему: [CategorySchema](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/CategorySchema.php), [PostSchema](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/PostSchema.php), [TagSchema](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/TagSchema.php), [UserSchema](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/UserSchema.php).
Сокращенный вариант схемы для поста выглядит следующим образом:

```php
    // src/JsonApi/Posts/PostSchema.php
    class PostSchema extends Schema
    {
       public static string $model = Post::class;

       public function fields(): array
       {
           return [
               ID::make(),
               Str::make('title')->sortable(),
               Str::make('content'),
               DateTime::make('createdAt')->sortable()->readOnly(),
               DateTime::make('updatedAt')->sortable()->readOnly(),
               DateTime::make('publishedAt')->sortable(),
               BelongsTo::make('author')->type('users')->readOnly(),
               HasMany::make('tags')->type('tags')->deleteDetachedModels(),
               BelongsToMany::make('categories')->type('categories')
           ];
       }

       public function filters(): array
       {
           return [
               WhereIdIn::make($this),
               Where::make('title'),
               ContainsFilter::make('content'),
               WhereHas::make($this, 'categories')
           ];
       }

       public function pagination(): ?Paginator
       {
           return PagePagination::make();
       }
    }
```

Используя эту схему, библиотека [laravel-json-api](https://laraveljsonapi.io/docs/3.0/schemas/) реализует CRUD операции над ресурсами. Ограничить возможные операции можно в момент регистрации API сервера через настройки [роутинга](https://laraveljsonapi.io/docs/3.0/routing/#partial-resource-routes).


### Создание правил валидации для ресурсов<a id="создание-правил-валидации-для-ресурсов"></a>

Библиотека laravel-json-api использует механизмы [валидации Laravel](https://laravel.com/docs/10.x/validation), поддерживает все доступные правила валидации и реализует собственные правила валидации, например для валидации связей. Правила валидации должны быть описаны в "Request" классах.
"Request" классы laravel-json-api следуют правилам описания ["Request" классам в Laravel](https://laravel.com/docs/10.x/validation#form-request-validation).
Для каждого ресурса свой "Request" класс: [CategoryRequest](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/CategoryRequest.php), [PostRequest](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/PostRequest.php), [TagRequest](https://github.com/incrize/lunar-blog/blob/main/src/JsonApi/Posts/TagRequest.php).
Сокращенный вариант для поста выглядит следующим образом:

```php

    // src/JsonApi/Posts/PostRequest.php
    class PostRequest extends ResourceRequest
    {
       public function rules(): array
       {
           $post = $this->model();

           return [
               'title'           => [$post ? 'filled' : 'required', 'string'],
               'content'         => [$post ? 'filled' : 'required', 'string'],
               'publishedAt'     => ['nullable', JsonApiRule::dateTime()],
               'tags'            => [JsonApiRule::toMany()],
               'categories'      => [$post ? null : 'required', JsonApiRule::toMany()],
               'categories.*.id' => [Rule::exists(Category::class)->where('status', CategoryStatus::ACTIVE)],
           ];
       }
    }

```

[laravel-json-api](https://laraveljsonapi.io/docs/3.0/schemas/) ожидает, что "Request" класс будет расположен в том же пространстве имен что и схема ресурса, а именование будет следовать правилу: \<ResourceType> + Request.


### Создание политик для контроля доступа<a id="создание-политик-для-контроля-доступа"></a>

Для проверки прав laravel-json-api использует Laravel [политики](https://laravel.com/docs/10.x/authorization#writing-policies).
Кроме реализации стандартных методов: `create`, `update`, `view`, `viewAny`, `delete`, laravel-json-api ожидает методы для [проверки прав связей](https://laraveljsonapi.io/docs/3.0/requests/authorization.html#relationship-authorization).
Для каждого ресурса реализована своя политика: [CategoryPolicy](https://github.com/incrize/lunar-blog/blob/main/src/Policies/CategoryPolicy.php), [PostPolicy](https://github.com/incrize/lunar-blog/blob/main/src/Policies/PostPolicy.php), [PostTagPolicy](https://github.com/incrize/lunar-blog/blob/main/src/Policies/PostTagPolicy.php).
Сокращенный вариант политики для поста выглядит следующим образом:

```php
    // src/Policies/PostPolicy.php
    class PostPolicy
    {
       use HandlesAuthorization;

       public function create(User $user)
       {
           return $user->can('blog-post:create') || $user->can('blog-post:manage');
       }

       public function update(User $user, Post $post)
       {
           return ($user->is($post->author) && $user->can('blog-post:update')) || $user->can('blog-post:manage');
       }

       public function updateCategories(User $user, Post $post)
       {
           return $this->update($user, $post);
       }

       public function attachCategories(User $user, Post $post)
       {
           return $this->update($user, $post);
       }

       public function detachCategories(User $user, Post $post)
       {
           return $this->update($user, $post);
       }

       public function viewAny(?User $user)
       {
           return true;
       }

       public function view(?User $user, Post $post)
       {
           if ($post->published_at) {
               return true;
           }

           return $user && $user->is($post->author);
       }

       public function viewCategories(?User $user, Post $post)
       {
           return $this->view($user, $post);
       }

       public function delete(User $user, Post $post)
       {
           return $this->update($user, $post);
       }
    }

```

laravel-json-api вызывает методы политики через [Gate](https://laravel.com/docs/10.x/authorization#gates) механизм Laravel, поэтому политики должны быть объявлены в соответствии с [правилами именования политики в Laravel](https://laravel.com/docs/10.x/authorization#creating-policies).


### Создание API сервера и его регистрация<a id="создание-api-сервера-и-его-регистрация"></a>

Под сервером laravel-json-api подразумевает некоторый API со своим набором ресурсов.
Типичный пример использования - это разные версии API, где каждая версия будет представлена в виде API сервера.
API сервер для блога выглядит следующим образом:

```php
    // src/JsonApi/BlogServer.php
    class BlogServer extends Server
    {
       protected string $baseUri = '/api/blog';

       public function serving(): void
       {
           Auth::shouldUse('sanctum');

           Post::creating(static function (Post $post): void {
               $post->author()->associate(Auth::user());
           });
       }

       protected function allSchemas(): array
       {
           return [
               PostSchema::class,
               CategorySchema::class,
               TagSchema::class,
               UserSchema::class
           ];
       }
    }

```

Регистрация сервера выполняется в сервис провайдере:

```php
    // src/BlogServiceProvider.php
    $this->app['config']->set('jsonapi.servers', array_merge(
       (array) $this->app['config']->get('jsonapi.servers'),
       ['blog' => BlogServer::class]
    ));

    $this->callAfterResolving(Router::class, function () {
       JsonApiRoute::server('blog')->prefix('api/blog')->resources(function ($server) {
           $server->resource('posts', JsonApiController::class)
               ->relationships(function (Relationships $relations) {
                   $relations->hasOne('author')->readOnly();
                   $relations->hasMany('categories');
                   $relations->hasMany('tags');
               });
           $server->resource('categories', JsonApiController::class);
           $server->resource('tags', JsonApiController::class);
       });
    });
```

На этом этапе в приложении будут зарегистрированы все необходимые роуты для выполнения CRUD операций в API. Дальнейшие шаги направлены на добавление специфичных бизнес требований.


### Реализация бизнес требований<a id="реализация-бизнес-требований"></a>

1. Категории постов могут быть деактивированы.
Реализовано за счет модели `Category`, каждая категория имеет статус: ACTIVE или DISABLED.

2. Неактивные категории не должны быть доступны для гостей и авторов.
Для выполнения этого требования необходимо реализовать дополнительные обработчики в [CategorySchema](https://github.com/incrize/lunar-blog/blob/e6b276b80f91f9f4f8cb2430abfaeb143cf13aa7/src/JsonApi/Posts/CategorySchema.php#L68-L106), которые будут добавлять дополнительные условия в Builder SQL запросов:

```php
    // src/JsonApi/Posts/CategorySchema.php
    public function indexQuery(?Request $request, Builder $query): Builder
    {
       /** @var \App\Models\User|null $user */
       $user = optional($request)->user();

       if (!$user || $user->cannot('blog-category:manage')) {
           $query->where('status', '=', CategoryStatus::ACTIVE);
       }
       return $query;
    }

    public function relatableQuery(?Request $request, Relation $query): Relation
    {
       /** @var \App\Models\User|null $user */
       $user = optional($request)->user();

       if (!$user || !$user->cannot('blog-category:manage')) {
           $query->where('status', '=', CategoryStatus::ACTIVE);
       }

       return $query;
    }

```
Кроме этого необходимо добавить правило валидации при создании поста в [PostRequest](https://github.com/incrize/lunar-blog/blob/e6b276b80f91f9f4f8cb2430abfaeb143cf13aa7/src/JsonApi/Posts/PostRequest.php#L30), запрещающее выбор неактивной категории:

```php
    // src/JsonApi/Posts/PostRequest.php
    'categories.*.id' => [Rule::exists(Category::class)->where('status', CategoryStatus::ACTIVE)],
```

3. Посты могут быть в нескольких категориях.
Реализовано за счет модели `Post`, `Category` и промежуточной таблицы `blog\_post\_categories`.

4. Посты могут иметь неограниченное кол-во тегов
Реализовано за счет модели `Post` и `PostTag`.

5. Гости могут видеть все опубликованные посты, которые находятся хотя бы в одной активной категории
Для выполнения этого требования необходимо реализовать дополнительный обработчик в [PostSchema](https://github.com/incrize/lunar-blog/blob/e6b276b80f91f9f4f8cb2430abfaeb143cf13aa7/src/JsonApi/Posts/PostSchema.php#L87-L108):

```php
    // src/JsonApi/Posts/PostSchema.php
    public function indexQuery(?Request $request, Builder $query): Builder
    {
       $query->whereHas('categories', function (Builder $q) {
           $q->where('status', '=', CategoryStatus::ACTIVE);
       });

       return $query->whereNotNull('published_at');
    }

```
6. Авторы могут видеть все свои посты
Для выполнения этого требования необходимо расширить метод `indexQuery` в [PostSchema](https://github.com/incrize/lunar-blog/blob/e6b276b80f91f9f4f8cb2430abfaeb143cf13aa7/src/JsonApi/Posts/PostSchema.php#L87-L108):

```php
    // src/JsonApi/Posts/PostSchema.php
    public function indexQuery(?Request $request, Builder $query): Builder
    {
       if ($user = optional($request)->user()) {
           return $query->where(function (Builder $q) use ($user) {
               return $q->whereNotNull('published_at')->orWhere('author_id', $user->getKey());
           });
       }

       $query->whereHas('categories', function (Builder $q) {
           $q->where('status', '=', CategoryStatus::ACTIVE);
       });

       return $query->whereNotNull('published_at');
    }
```

### Итоги<a id="итоги"></a>

API документация: [API docs](https://www.postman.com/telecoms-explorer-89845901/workspace/laravel-simple-blog-api/api/2e5edf32-d141-41c0-bf18-bc4532fdf875/definition/93820df1-2420-4036-9846-09dedff28b81?version=257660a5-e086-48f1-94df-c66bc1e88a07\&view=documentation)

Пример API запросов на создание поста с тегами:

```curl
curl -X POST 'http\://example.com/api/blog/posts' \\
   -H 'Accept: application/vnd.api+json' \\
   -H 'Content-Type: application/vnd.api+json' \\
   -H 'Authorization: Bearer \*\*\*\*\*\*\*\*\*' \\
   -d '{"data":{"type":"posts", "attributes": {"title": "How to Create JSON:API Resources", "content": "In our second blog post", "publishedAt": null}, "relationships": {"categories": {"data": \[{"type": "categories", "id": "1"}]}}}}'
```

```curl
curl -X POST 'http\://example.com/api/blog/tags' \\
   -H 'Accept: application/vnd.api+json' \\
   -H 'Content-Type: application/vnd.api+json' \\
   -H 'Authorization: Bearer \*\*\*\*\*\*\*\*\*' \\
   -d '{"data":{"type":"tags", "attributes": {"title": "JSON-API", "postId": 1}}}'
```

```curl
curl -X POST 'http\://example.com/api/blog/tags' \\
   -H 'Accept: application/vnd.api+json' \\
   -H 'Content-Type: application/vnd.api+json' \\
   -H 'Authorization: Bearer \*\*\*\*\*\*\*\*\*' \\
   -d '{"data":{"type":"tags", "attributes": {"title": "Laravel", "postId": 1}}}'
```

Библиотека [laravel-json-api/laravel](https://github.com/laravel-json-api/laravel) предоставляет удобный инструментарий для создания API по спецификации JSON-API, хорошо интегрирована с Laravel, отдельно стоит выделить:
- Стандартизация работы со связями
- Решение проблем N+1 запросов для связей

Проблемы, которые кажутся критичными:
1. Отсутствует модульность API. Например, нет возможности разработать модуль “Изображения для постов” без изменения кода модуля “Блог”.
2. Описание ограничений модели и соблюдение инвариантов реализовано в слое реализации API. Например, для асинхронного API создания поста, где сам процесс создания будет выполняться вне контекста API, придется каким-то образом переиспользовать правила валидации.
3. Отсутствуют вложенные мутации. Нет возможности атомарно создать пост вместе с тегами, только сначала пост, а затем отдельного его теги. Это накладывает больше ответственности на клиентов API, которые должны соблюдать последовательность вызовов. 
4. Отсутствует автодокументация API. Документацию придется описывать вручную.
5. Ограниченная работа со связями. Например, нет возможности в рамках одного запроса создать пост и разместить его во всех категориях, где имя категории начинается с “IT”. Специфичный пример, но поддержка подобных возможностей может упростить интеграцию с внешними сервисами.
6. Отсутствует кэширование.
