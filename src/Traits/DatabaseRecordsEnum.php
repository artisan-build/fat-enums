<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Traits;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait DatabaseRecordsEnum
{
    public function get(): Model
    {
        if (! $this instanceof BackedEnum) {
            throw new \Exception('DatabaseRecords trait can only be used with backed enums.');
        }

        $rc = new ReflectionClass($this);
        if (! $rc->hasConstant('ModelName')) {
            throw new \Exception('ModelName constant must be defined in the enum.');
        }

        /** @var class-string<Model> $model */
        $model = $rc->getConstant('ModelName');

        if (! is_string($model)) {
            throw new \Exception('ModelName constant must be a string.');
        }

        if (! class_exists($model)) {
            throw new \Exception('ModelName constant must be a valid class.');
        }

        if (! is_subclass_of($model, Model::class)) {
            throw new \Exception('ModelName constant must be a subclass of '.Model::class);
        }

        return $model::findOrFail($this->value);
    }
}
