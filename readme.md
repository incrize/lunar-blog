### Business requirements

1. There can be an unlimited number of categories
2. Categories can be disabled
3. There can be an unlimited number of posts
4. Each post can be in several categories
5. Each post can have an unlimited number of tags
6. Authors can manage their posts
7. Administrator can manage all posts and categories
8. Guests can view published posts, provided that the post has at least one active category.
9. Disabled categories should not be displayed to authors and guests.

### API requests example

#### Create category

```curl
curl -X POST 'http://127.0.0.1:8000/api/blog/categories' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
      -d '{"data":{"type":"categories", "attributes": {"title": "Lifestyle", "slug": "lifestyle", "status": "A"}}}'
```

```curl
curl -X POST 'http://127.0.0.1:8000/api/blog/categories' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
      -d '{"data":{"type":"categories", "attributes": {"title": "IT", "slug": "it", "status": "A"}}}'
```

#### Update category

```
curl -X PATCH 'http://127.0.0.1:8000/api/blog/categories/1' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
      -d '{"data":{"type":"categories", "id": "1", "attributes": {"status": "D"}}}'
```


#### Create post

```curl
curl -X POST 'http://127.0.0.1:8000/api/blog/posts' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 2|3Zj3NwFaVTOFBJI7zyPLlCVJRsq33YhYI81t8OxTbd2c47f5' \
      -d '{"data":{"type":"posts", "attributes": {"title": "How to Create JSON:API Resources", "content": "In our second blog post", "publishedAt": null}, "relationships": {"categories": {"data": [{"type": "categories", "id": "2"}]}}}}'
```

```curl
curl -X POST 'http://127.0.0.1:8000/api/blog/tags' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 2|3Zj3NwFaVTOFBJI7zyPLlCVJRsq33YhYI81t8OxTbd2c47f5' \
      -d '{"data":{"type":"tags", "attributes": {"title": "JSON-API", "postId": 1}}}'
```

```curl
curl -X POST 'http://127.0.0.1:8000/api/blog/tags' \
      -H 'Accept: application/vnd.api+json' \
      -H 'Content-Type: application/vnd.api+json' \
      -H 'Authorization: Bearer 2|3Zj3NwFaVTOFBJI7zyPLlCVJRsq33YhYI81t8OxTbd2c47f5' \
      -d '{"data":{"type":"tags", "attributes": {"title": "Laravel", "postId": 1}}}'
```

#### Update post


```curl
curl -X PATCH 'http://127.0.0.1:8000/api/blog/posts/1' \
    -H 'Accept: application/vnd.api+json' \
    -H 'Content-Type: application/vnd.api+json' \
    -H 'Authorization: Bearer 2|3Zj3NwFaVTOFBJI7zyPLlCVJRsq33YhYI81t8OxTbd2c47f5' \
    -d '{"data":{"type":"posts", "id": "1", "attributes": {"publishedAt": "2023-07-10T12:00+00:00"}}}'
```

### Attach categories to post

```curl
curl -g -X POST 'http://127.0.0.1:8000/api/blog/posts/{postId}/relationships/categories' \
  -H 'Accept: application/vnd.api+json' \
  -H 'Content-Type: application/vnd.api+json' \
  -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
  -d '{"data": [{"type":"categories", "id":"10"}]}'
```

### Detach categories from post

```curl
curl -g -X DELETE 'http://127.0.0.1:8000/api/blog/posts/{postId}/relationships/categories' \
  -H 'Accept: application/vnd.api+json' \
  -H 'Content-Type: application/vnd.api+json' \
  -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
  -d '{"data": [{"type":"categories", "id":"10"}]}'
```

### Update post categories

```curl
curl -g -X PATCH 'http://127.0.0.1:8000/api/blog/posts/{postId}/relationships/categories' \
  -H 'Accept: application/vnd.api+json' \
  -H 'Content-Type: application/vnd.api+json' \
  -H 'Authorization: Bearer 1|BJNusG6iCLFVYaWAZSJio0Rh7TpzdDbD9aZ6YdMP20d90436' \
  -d '{"data": [{"type":"categories", "id":"10"}]}'
```

#### Get posts

```curl
curl -X GET 'http://127.0.0.1:8000/api/blog/posts?include=author,tags,categories' \
    -H 'Accept: application/vnd.api+json' \
    -H 'Content-Type: application/vnd.api+json'
```

```curl
curl -g -X GET 'http://127.0.0.1:8000/api/blog/posts?include=author,tags,categories&filter[title]=API' \
    -H 'Accept: application/vnd.api+json' \
    -H 'Content-Type: application/vnd.api+json'
```

```curl
curl -g -X GET 'http://127.0.0.1:8000/api/blog/posts?include=author,tags,categories&filter[content]=blog' \
    -H 'Accept: application/vnd.api+json' \
    -H 'Content-Type: application/vnd.api+json'
```

```curl
curl -g -X GET 'http://127.0.0.1:8000/api/blog/posts?include=author,tags,categories&filter[categories][id][]=1' \
    -H 'Accept: application/vnd.api+json' \
    -H 'Content-Type: application/vnd.api+json'
```




