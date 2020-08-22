# php File Cacher
This class helps you to use cache system in your php projects and develop smart projects with cache

```
FileCacher::put("key", "value");
FileCacher::put("key", "value", 60);

$data = FileCacher::get("key", function () {
    return "default value";
});

$data = FileCacher::remember("key", function () {
    return "default value";
}, 60);

FileCacher::pull("key");

FileCacher::forget("key");

FileCacher::has("key");
```

## how to use
you can use this class with composer package manager or include in your php files

```
include "FileCacher.php";
```

You do not need to create an instance to use this class

### cache directory
by default all caches save in ``storage/cache`` directory in your project if you need to save caches in custom directory you can set your custom path directory for example

```
FileCacher::setCacheDir("data/cache");
```

### put cache
``put function`` is responsible for caching
In general, if you do not specify an expiration date, the cache will remain closed forever and will not expire
If you do not want the cache you are saving to expire, do the following

```
FileCacher::put("key", "value");
```

the value can be in the form of an array that is stored as json
If you want the cache you are saving to expire, do the following

```
FileCacher::put("key", "value", 60); // set expire time as seconds
```

### get cache data
``get function`` for get saved cache data for example
```
FileCacher::get("key");
```
the get function return saved value
If you need to return the default value if there is no cache, you can do the following

```
FileCacher::get("key", "default");
```

the default value can be array or callable function
If you need time processing to get the data, you can write the default value as a callable function.

```
FileCacher::get("key", function () {
    return "default value";
});
```

### remember
``remember function`` helps you to receive data if you have it saved as a save If you do not have a preset value, you can also send a parameter as the expiration date in seconds like put function

```
FileCacher::remember("key", "value");

FileCacher::remember("key", "value", $expire_seconds);

FileCacher::remember("key", function () {
    return "value";
}, $expire_seconds);
```

### pull
``pull function`` returns the value from the cache and removes that cache

```
FileCacher::pull("key");

FileCacher::pull("key", "default");

FileCacher::pull("key", function () {
    return "default";
});
```
### forgot

``forgot function`` removes a cache

```
FileCacher::forget("key");
```

### has
``has function`` for check the existence of a cache

```
FileCacher::has("key");
```
