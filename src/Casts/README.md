# Enum Bitmask Casts

This package provides Laravel Eloquent casts for working with enums as bitmasks. It includes both collection-based and array-based casts, with nullable and non-nullable variants.

## Why Use Bitmasks?

At first glance, storing multiple enum values as a single integer might seem complex compared to alternatives like JSON or arrays. However, bitmasks offer significant advantages:

1. **Performance**: Bitmasks are incredibly efficient for both storage and querying. Each permission/flag takes exactly one bit, making them much smaller than JSON or array storage. This means faster database operations and less memory usage.

2. **Querying**: Bitmasks enable powerful and fast database queries. You can easily find all records with specific permissions using simple bitwise operations:
   ```sql
   -- Find all users with READ permission
   SELECT * FROM users WHERE permissions & 1 = 1;
   
   -- Find all users with both READ and WRITE permissions
   SELECT * FROM users WHERE permissions & 3 = 3;
   ```

3. **Indexing**: Unlike JSON or array fields, bitmask columns can be indexed efficiently. This means your permission checks can be as fast as checking a single integer column.

4. **Atomic Operations**: Bitmasks allow for atomic updates of multiple flags in a single operation. No need to load, modify, and save an entire array or JSON object.

5. **Type Safety**: By using enums, we get compile-time type checking and IDE support, while still maintaining the performance benefits of bitmasks.

This package makes it easy to work with bitmasks in a Laravel-friendly way, giving you all these benefits while maintaining a clean, object-oriented interface.

## Available Casts

### Collection-based Casts

- `AsEnumCollectionBitmask` - Stores multiple enum values as a single bitmask integer (non-nullable)
- `AsNullableEnumCollectionBitmask` - Stores multiple enum values as a single bitmask integer (nullable)

### Array-based Casts

- `AsEnumArrayObjectBitmask` - Stores an array of enum values as a single bitmask integer (non-nullable)
- `AsNullableEnumArrayObjectBitmask` - Stores an array of enum values as a single bitmask integer (nullable)

## Usage

### Defining Your Enum

Your enum must be a `BackedEnum` with integer values. For both collection-based and array-based casts, use bit shifting to define powers of 2:

```php
enum PermissionEnum: int
{
    case READ   = 0x1 << 0;  // 1
    case WRITE  = 0x1 << 1;  // 2
    case DELETE = 0x1 << 2;  // 4
    case ADMIN  = 0x1 << 3;  // 8
}
```

This makes it easier to add new permissions without having to calculate the next power of 2. Not every bit has be get used - you can leave gaps for visual separation, or to reserve spots for related future data.

### Using the Casts

There are two ways to define casts in your model:

1. Using the `$casts` property (requires string concatenation):
```php
use App\Casts\AsEnumCollectionBitmask;
use App\Casts\AsNullableEnumCollectionBitmask;
use App\Casts\AsEnumArrayObjectBitmask;
use App\Casts\AsNullableEnumArrayObjectBitmask;

class User extends Model
{
    protected $casts = [
        // Collection-based casts
        'permissions' => AsEnumCollectionBitmask::class . ':' . PermissionEnum::class,
        'optional_permissions' => AsNullableEnumCollectionBitmask::class . ':' . PermissionEnum::class,
        
        // Array-based casts
        'roles' => AsEnumArrayObjectBitmask::class . ':' . PermissionEnum::class,
        'optional_roles' => AsNullableEnumArrayObjectBitmask::class . ':' . PermissionEnum::class,
    ];
}
```

2. Using the `casts()` method (allows using the `of()` static method):
```php
use App\Casts\AsEnumCollectionBitmask;
use App\Casts\AsNullableEnumCollectionBitmask;
use App\Casts\AsEnumArrayObjectBitmask;
use App\Casts\AsNullableEnumArrayObjectBitmask;

class User extends Model
{
    protected function casts(): array
    {
        return [
            // Collection-based casts
            'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
            'optional_permissions' => AsNullableEnumCollectionBitmask::of(PermissionEnum::class),
            
            // Array-based casts
            'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
            'optional_roles' => AsNullableEnumArrayObjectBitmask::of(PermissionEnum::class),
        ];
    }
}
```

The difference between these approaches is due to how PHP handles static method calls in property declarations. The `$casts` property must be defined as a static value, so we can't use method calls like `of()`. Instead, we use string concatenation to achieve the same result. The `casts()` method, being a function, allows us to use the more readable `of()` static method.

### Working with Collection-based Casts

```php
// Setting multiple permissions
$user->permissions = collect([PermissionEnum::READ, PermissionEnum::WRITE]);
// Stores 3 in the database (1 | 2)
$user->save(); // Don't forget to save the model!

// Getting permissions
$permissions = $user->permissions;
// Returns a collection with READ and WRITE cases

// Checking for specific permissions
if ($user->permissions->contains(PermissionEnum::READ)) {
    // User has read permission
}

// Adding a permission
$user->permissions = $user->permissions->push(PermissionEnum::DELETE);
// Stores 7 in the database (1 | 2 | 4)
$user->save(); // Don't forget to save the model!

// Removing a permission
$user->permissions = $user->permissions->filter(fn ($p) => $p !== PermissionEnum::WRITE);
// Stores 5 in the database (1 | 4)
$user->save(); // Don't forget to save the model!
```

### Working with Array-based Casts

```php
// Setting multiple roles
$user->roles = [PermissionEnum::READ, PermissionEnum::WRITE];
// Stores 3 in the database (1 | 2)
$user->save(); // Don't forget to save the model!

// Getting roles
$roles = $user->roles;
// Returns an array with READ and WRITE cases

// Checking for specific roles
if (in_array(PermissionEnum::READ, $user->roles)) {
    // User has read role
}

// Adding a role
$user->roles = array_merge($user->roles, [PermissionEnum::DELETE]);
// Stores 7 in the database (1 | 2 | 4)
$user->save(); // Don't forget to save the model!

// Removing a role
$user->roles = array_filter($user->roles, fn ($r) => $r !== PermissionEnum::WRITE);
// Stores 5 in the database (1 | 4)
$user->save(); // Don't forget to save the model!
```

### Important Note About Persistence

When modifying collections or arrays of enum values, remember that you must explicitly call `save()` on the model to persist the changes to the database. The changes are only stored in memory until you save the model:

```php
$user->permissions = collect([PermissionEnum::READ, PermissionEnum::WRITE]);
$user->save(); // Changes are now persisted to the database
```

### Nullable vs Non-nullable

- Non-nullable casts (`AsEnumCollectionBitmask`, `AsEnumArrayObjectBitmask`):
  - Default to empty collection/array when null is provided
  - Always return a value (empty collection/array or default enum cases)

- Nullable casts (`AsNullableEnumCollectionBitmask`, `AsNullableEnumArrayObjectBitmask`):
  - Allow null values in the database
  - Return null when the database value is null

## Best Practices

1. Use collection-based casts when you need to store multiple enum values efficiently in a single column and want to work with Laravel Collections
2. Use array-based casts when you need to store multiple enum values efficiently in a single column and prefer working with PHP arrays
3. Use nullable casts when you need to distinguish between "no value" and "explicitly no value"
4. Use bit shifting (`0x1 << n`) to define enum values for both collection-based and array-based casts
5. Consider using constants to make bit positions more readable and maintainable
6. Consider using descriptive enum case names that clearly indicate their purpose
7. Always call `save()` on the model after modifying collections or arrays of enum values
8. Prefer using the `casts()` method over the `$casts` property when possible, as it allows for more readable cast definitions using the `of()` static method 