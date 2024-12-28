## GraphQL projections generator for Laravel

## Disclaimer: "Still in progress. Wait until release"

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

### Testing

Imagine you have some GraphQL schema:
```graphql
extend type Mutation {
    userUpdate(id: ID!, input: UserInput!): UserType!
}

input UserInput {
    name: String!
    children: [ChildInput!]!
}

input ChildInput {
    name: String!
    age: Int!
}

type UserType {
    id: ID!
    name: String!
    children: [ChildType!]!
    someValue: String
    someOtherValue(withParam: String): String
}

type ChildType {
    name: String!
    age: Int!
}
```

After generating graphql entities (by `artisan graphql:projection` command) you can use it in your tests in this way:
```php
$query = UserUpdateGraphQLQuery::newRequest()
    ->id(1)
    ->input(
        $userInput = UserInput::builder()
            ->name('John Doe')
            ->children([
                ChildInput::builder()->name('Child 1')->age(10)->build(),
                ChildInput::builder()->name('Child 2')->age(20)->build(),
            ])
            ->build()
    )
    ->build();

$projection = UserUpdateProjection::new()
    ->id()
    ->name()
    ->id()
        ->children()
        ->name()
        ->age()
    ->getParent() // return to previous type (or ->getRoot() to return to the root)
    ->someValue();
    ->someOtherValue('withParam');

$userType = GraphQLQueryExecutor::execute($query, $projection, UserType::class);

$this->assertEquals($userInput->getName(), $userType->getName());
```
