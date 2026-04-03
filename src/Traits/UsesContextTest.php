<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Enums\ContextUsers;
use Illuminate\Support\Facades\Context;

it('can push a value to context', function (): void {
    ContextUsers::Happy->push('value1');
    ContextUsers::Happy->push('value2');

    expect(Context::get('happy'))->toBe(['value1', 'value2']);
});

it('can get values from context', function (): void {
    Context::push('sad', 'lonely');

    expect(ContextUsers::Sad->get())->toBe(['lonely']);
});

it('returns empty array when context key has no values', function (): void {
    expect(ContextUsers::Happy->get())->toBe([]);
});

it('can unshift a value to the front of context', function (): void {
    ContextUsers::Happy->push('second');
    ContextUsers::Happy->unshift('first');

    expect(ContextUsers::Happy->get())->toBe(['first', 'second']);
});
