# Module Routing

This guide explains how routing works in Laravel Modular modules.

## 📋 Route Prefixing

**Important**: All module routes are **automatically prefixed** with the module name (lowercase).

### How It Works

When you create a module, the Service Provider automatically registers routes with a prefix:

```php
// In ModuleServiceProvider.php
Route::group([
    'middleware' => 'web',
    'namespace' => 'Modules\YourModule\\Http\\Controllers',
    'prefix' => 'yourmodule',  // ← Automatic prefix
], function (): void {
    $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
});
```

### Example

If you have a **Blog** module with this route:

```php
// modules/Blog/routes/web.php
Route::get('/', function() {
    return 'Blog home';
});

Route::get('/posts', [PostController::class, 'index']);
```

The actual URLs will be:
- ✅ `http://yourapp.com/blog/` (not `/`)
- ✅ `http://yourapp.com/blog/posts` (not `/posts`)

## 🌐 Web Routes

### Module: `modules/YourModule/routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\YourModule\Http\Controllers\YourController;

Route::get('/', [YourController::class, 'index']);
Route::get('/list', [YourController::class, 'list']);
Route::resource('posts', PostController::class);
```

### Generated URLs

For a module named **Blog**:

| Route Definition | Actual URL | Name |
|-----------------|------------|------|
| `GET /` | `/blog/` | `blog.index` |
| `GET /list` | `/blog/list` | `blog.list` |
| `GET /posts` | `/blog/posts` | `blog.posts.index` |
| `POST /posts` | `/blog/posts` | `blog.posts.store` |

## 🔌 API Routes

API routes are prefixed with `api/modulename`:

```php
// In ModuleServiceProvider.php
Route::group([
    'middleware' => 'api',
    'prefix' => 'api/yourmodule',  // ← api/modulename prefix
    'namespace' => 'Modules\YourModule\\Http\\Controllers\\Api',
], function (): void {
    $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
});
```

### Module: `modules/Blog/routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Api\PostApiController;

Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{id}', [PostApiController::class, 'show']);
```

### Generated API URLs

| Route Definition | Actual URL |
|-----------------|------------|
| `GET /posts` | `/api/blog/posts` |
| `GET /posts/{id}` | `/api/blog/posts/1` |

## 🎯 Understanding the Prefix

### Web Routes Structure

```
Module: Blog
Prefix: blog

Route in file:           Actual URL:
/                   →    /blog/
/posts              →    /blog/posts
/posts/create       →    /blog/posts/create
/posts/{id}         →    /blog/posts/{id}
```

### API Routes Structure

```
Module: Blog
Prefix: api/blog

Route in file:           Actual URL:
/posts              →    /api/blog/posts
/categories         →    /api/blog/categories
```

## 🔧 Customizing the Prefix

If you want to change the prefix, edit your module's Service Provider:

```php
// modules/YourModule/app/Providers/YourModuleServiceProvider.php

protected function registerRoutes(): void
{
    Route::group([
        'middleware' => 'web',
        'namespace' => 'Modules\YourModule\\Http\\Controllers',
        'prefix' => 'custom-prefix',  // ← Change this
    ], function (): void {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    });
}
```

### Example: Custom Prefix

```php
// Change prefix from 'blog' to 'articles'
'prefix' => 'articles',
```

Now routes will be:
- `/articles/` instead of `/blog/`
- `/articles/posts` instead of `/blog/posts`

## 🏷️ Route Naming

Routes can be named for easy reference:

```php
// modules/Blog/routes/web.php
Route::get('/', [BlogController::class, 'index'])->name('index');
Route::get('/posts', [PostController::class, 'list'])->name('posts.list');
```

### Usage in Views

```blade
<a href="{{ route('blog.index') }}">Blog Home</a>
<a href="{{ route('blog.posts.list') }}">All Posts</a>
```

### Redirect in Controllers

```php
return redirect()->route('blog.index');
return redirect()->route('blog.posts.list');
```

## 🛡️ Middleware

Modules use Laravel's middleware system:

### Web Routes (default middleware)

```php
Route::group([
    'middleware' => 'web',  // ← session, cookies, CSRF
    // ...
], function (): void {
    // Your routes
});
```

### API Routes (default middleware)

```php
Route::group([
    'middleware' => 'api',  // ← throttle, JSON responses
    // ...
], function (): void {
    // Your routes
});
```

### Custom Middleware

Add middleware to specific routes:

```php
// modules/Blog/routes/web.php
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('posts', PostController::class);
});
```

## 📝 Examples

### Example 1: Blog Module

```php
// modules/Blog/routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;
use Modules\Blog\Http\Controllers\CategoryController;

// URL: /blog/
Route::get('/', [PostController::class, 'index'])->name('index');

// URL: /blog/posts
Route::resource('posts', PostController::class);

// URL: /blog/categories
Route::resource('categories', CategoryController::class);

// URL: /blog/search
Route::get('/search', [PostController::class, 'search'])->name('search');
```

### Example 2: Shop Module

```php
// modules/Shop/routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\ProductController;
use Modules\Shop\Http\Controllers\CartController;

// URL: /shop/
Route::get('/', [ProductController::class, 'index'])->name('index');

// URL: /shop/products
Route::get('/products', [ProductController::class, 'list'])->name('products');

// URL: /shop/products/{id}
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// URL: /shop/cart (with auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
});
```

### Example 3: API Routes

```php
// modules/Blog/routes/api.php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Api\PostApiController;

// URL: /api/blog/posts
Route::get('/posts', [PostApiController::class, 'index']);

// URL: /api/blog/posts/{id}
Route::get('/posts/{id}', [PostApiController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostApiController::class, 'store']);
    Route::put('/posts/{id}', [PostApiController::class, 'update']);
    Route::delete('/posts/{id}', [PostApiController::class, 'destroy']);
});
```

## 🔍 Viewing Routes

List all routes including module routes:

```bash
# All routes
php artisan route:list

# Filter by module
php artisan route:list | grep blog
php artisan route:list | grep shop

# Show specific columns
php artisan route:list --columns=Method,URI,Name
```

## ⚠️ Common Issues

### Issue 1: Route Not Found (404)

**Problem**: Accessing `/posts` returns 404

**Solution**: Remember the prefix! Use `/blog/posts` instead

```
❌ http://yourapp.com/posts
✅ http://yourapp.com/blog/posts
```

### Issue 2: Route Conflicts

**Problem**: Multiple modules have the same route path

```php
// Module Blog
Route::get('/list', ...);  // /blog/list ✅

// Module Shop  
Route::get('/list', ...);  // /shop/list ✅
```

**Solution**: Prefixes automatically prevent conflicts!

### Issue 3: Named Route Not Found

**Problem**: `route('posts.index')` doesn't work

**Solution**: Include module prefix in route names

```php
❌ route('posts.index')
✅ route('blog.posts.index')
```

## 📚 Best Practices

1. **Use Named Routes** - Easier to maintain and refactor
   ```php
   Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
   ```

2. **Group Related Routes** - Keep code organized
   ```php
   Route::prefix('admin')->middleware('auth')->group(function () {
       // Admin routes
   });
   ```

3. **Use Resource Routes** - For RESTful resources
   ```php
   Route::resource('posts', PostController::class);
   ```

4. **Document Your Routes** - Add comments for complex routing
   ```php
   // Public blog routes
   Route::get('/', [BlogController::class, 'index']);
   
   // Admin section (requires authentication)
   Route::middleware('auth')->group(function () {
       // ...
   });
   ```

5. **Test Your Routes** - Verify they work as expected
   ```bash
   php artisan route:list | grep yourmodule
   ```

## 🔗 Related Documentation

- [Installation Guide](INSTALLATION.md)
- [Module Structure](../README.md#module-structure)
- [Creating Modules](../README.md#quick-start)
- [Laravel Routing Docs](https://laravel.com/docs/routing)

---

**Key Takeaway**: All module routes are automatically prefixed with the module name. When defining routes in `routes/web.php`, remember they'll be accessible at `/modulename/your-route`.
