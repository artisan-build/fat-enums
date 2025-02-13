<p align="center"><img src="https://github.com/artisan-build/fat-enums/raw/HEAD/art/fat-enum-elephant.png" width="75%" alt="Fat Enums PHP Package Logo"></p>

> [!WARNING]  
> This package is currently under active development, and we have not yet released a major version. Once a 0.* version
> has been tagged, we strongly recommend locking your application to a specific working version because we might make
> breaking changes even in patch releases until we've tagged 1.0.

## Installation

`composer require artisan-build/fat-enums`

## Configuration

## Usage

### State Machines

#### Overview

```php
use ArtisanBuild\FatEnums\StateMachine\CanTransitionTo;
use ArtisanBuild\FatEnums\StateMachine\CanTransitionToSelf;
use ArtisanBuild\FatEnums\StateMachine\FinalState;
use ArtisanBuild\FatEnums\StateMachine\IsStateMachine;
use ArtisanBuild\FatEnums\StateMachine\StateMachine;

enum OrderStatus: string implements StateMachine
{
    use IsStateMachine;

    public const DEFAULT = self::Pending;

    #[CanTransitionTo([self::Processing])]
    case Pending = 'pending';

    #[CanTransitionToSelf]
    #[CanTransitionTo([self::Shipped])]
    case Processing = 'processing';

    #[CanTransitionTo([self::Delivered])]
    case Shipped = 'shipped';

    #[FinalState]
    case Delivered = 'delivered';
}
```

By using the `IsStateMachine` trait in your backed or unbacked enum, you can use the `canTransitionTo` and `canTransitionFrom` methods to check if a transition is valid.

In order to define the valid transitions, use the `CanTransitionTo`, `CanTransitionToSelf`, and/or `FinalState` attributes. `CanTransitionToSelf` and `FinalState` accept **no** arguments. `CanTransitionTo` accepts a single argument, which is an array of valid destination enum cases.

When defining your state machine, you should also define a `DEFAULT` constant that will be used as the initial state.

#### Usage (Standard Class)

```php
use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;
use HireThunk\Verbs\State;

class OrderState extends State
{
    use HasStateMachine;

    public private(set) OrderStatus $status = OrderStatus::DEFAULT {
        set(OrderStatus $status) {
            $this->status = $this->status->transitionTo($status);
        }
    }
}
```

(PHP 8.4+) The `private(set)` modifier is used to create a private property with a public getter and a private setter. The setter will validate the transition automatically and throw an `InvalidStateTransition` exception if it's not valid.

(PHP 8.3) The `private(set)` modifier is not available, so you would instead create a `setStatus` method to set the value on a protected property, and a `getStatus` method to get the value.

#### Usage (Laravel Eloquent Model)

```php
use ArtisanBuild\FatEnums\StateMachine\ModelHasStateMachine;

class Order extends Model
{
    use ModelHasStateMachine;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected $attributes = [
        'status' => OrderStatus::DEFAULT,
    ];

    protected array $state_machines = [
        'status',
    ];
}
```

The `ModelHasStateMachine` trait registers an `updated` model lifecycle hook that will validate the transitions of any properties that have been defined in the `$state_machines` property. If a transition is invalid, an `InvalidStateTransition` exception will be thrown.

In order for this all to work, properties that are defined as state machines must also be cast to to its proper enum type.

It's also good practice to set the default value of the property to the `DEFAULT` constant of the state machine enum in your model's `$attributes` property.

The `ModelHasStateMachine` trait also uses the `HasStateMachine` trait, so all of those methods are also available in your model.

#### Testing

The `HasStateMachine` trait provides a `serializeStateMachine` method that will return an array of the state machine's full configuration. This can be used to generate test data that will exercise all possible transitions, or to validate that your state machine is configured correctly compared to known good snapshots.

## Memberware

This package is part of our internal toolkit and is optimized for our own purposes. We do not accept issues or PRs
in this repository. 

