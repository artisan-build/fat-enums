<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Providers\FatEnumsServiceProvider;
use ArtisanBuild\FatEnums\Tests\Fixtures\ModelBackedTestEnum;
use ArtisanBuild\FatEnums\Traits\DatabaseRecordsEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('can get a model from a backed enum', function (): void {
    $model = ModelBackedTestEnum::Foo->get();
    expect($model->name)->toBe('Foo');
});

it('throws an exception if the model is not found', function (): void {
    expect(ModelBackedTestEnum::Baz->get(...))
        ->toThrow(ModelNotFoundException::class);
});

it('throws an exception if the enum is not backed', function (): void {
    expect(UnbackedTestEnum::Foo->get(...))
        ->toThrow(Exception::class, 'DatabaseRecords trait can only be used with backed enums.');
});

it('throws an exception if the model name constant is not defined', function (): void {
    expect(NoConstantTestEnum::Foo->get(...))
        ->toThrow(Exception::class, 'ModelName constant must be defined in the enum.');
});

it('throws an exception if the model name constant is not a string', function (): void {
    expect(NotStringConstantTestEnum::Foo->get(...))
        ->toThrow(Exception::class, 'ModelName constant must be a string.');
});

it('throws an exception if the model name constant is not a valid class', function (): void {
    expect(NotClassConstantTestEnum::Foo->get(...))
        ->toThrow(Exception::class, 'ModelName constant must be a valid class.');
});

it('throws an exception if the model name constant is not a subclass of Model', function (): void {
    expect(NotModelConstantTestEnum::Foo->get(...))
        ->toThrow(Exception::class, 'ModelName constant must be a subclass of Illuminate\Database\Eloquent\Model');
});

enum UnbackedTestEnum
{
    use DatabaseRecordsEnum;
    case Foo;
}

enum NoConstantTestEnum: int
{
    use DatabaseRecordsEnum;
    case Foo = 1111;
}

enum NotStringConstantTestEnum: int
{
    use DatabaseRecordsEnum;
    case Foo = 1111;

    public const ModelName = 1111;
}

enum NotClassConstantTestEnum: int
{
    use DatabaseRecordsEnum;
    case Foo = 1111;

    public const ModelName = 'NotAClass';
}

enum NotModelConstantTestEnum: int
{
    use DatabaseRecordsEnum;
    case Foo = 1111;

    public const ModelName = FatEnumsServiceProvider::class;
}
