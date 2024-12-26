## GraphQL projections generator for Laravel

### Installation
Require this package with composer using the following command:
```bash
composer require --dev fedir/php-graphql-projection
```

Publish configs:
```bash
artisan vendor:publish --tag=config-graphql-projection
```

### Usage
Add namespace to the build folder in your composer.json file:
```json
{
    "autoload-dev": {
        "psr-4": {
            "Build\\GraphQLProjection\\": "build/graphql-projection/"
        }
    }
}
```

You may also change namespace and build folder by config:
```
#config/graphql-projection.php

'build' => [
    'buildDir' => 'your-build',
    'namespace' => 'Your\\Namespace\\',
],
```
And then, your composer.json will look like:
```json
{
    "autoload-dev": {
        "psr-4": {
            "Your\\Namespace\\": "your-build/"
        }
    }
}
```

Generate graphql projections using the following command:
```bash
artisan graphql:projection
```

After all, you can use generated files in your tests:
```php

use Build\GraphQLProjection\Types\User;
use Build\GraphQLProjection\Types\UserBuilder;

it('build UserType by UserBuilder', function () {
    $userType = UserBuilder::newBuilder()->id(1)->build();
    
    assertInstanceOf(User::class, $userType);
    assertEquals(1, $userType->getId());
});
```
