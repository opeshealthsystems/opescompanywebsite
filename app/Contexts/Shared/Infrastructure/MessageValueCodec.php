<?php

namespace App\Contexts\Shared\Infrastructure;

use Illuminate\Database\Eloquent\Model;

/** Store constructor primitives and model references, never serialized PHP object graphs. */
final class MessageValueCodec
{
    public static function encode(mixed $value): mixed
    {
        if ($value instanceof Model && str_starts_with(get_class($value), 'App\\Models\\')) return ['@model' => get_class($value), 'id' => $value->getKey()];
        if ($value instanceof \DateTimeInterface) return ['@date' => $value->format(DATE_ATOM)];
        if ($value instanceof \BackedEnum && str_starts_with(get_class($value), 'App\\Enums\\')) return ['@enum' => get_class($value), 'value' => $value->value];
        if (is_array($value)) return array_map(self::encode(...), $value);
        if (is_scalar($value) || $value === null) return $value;
        throw new \LogicException('Unsupported durable message constructor value');
    }

    public static function decode(mixed $value): mixed
    {
        if (!is_array($value)) return $value;
        if (isset($value['@model'])) {
            $class = $value['@model'];
            if (!str_starts_with($class, 'App\\Models\\') || !is_subclass_of($class, Model::class)) throw new \LogicException('Unsupported model reference');
            return $class::findOrFail($value['id']);
        }
        if (isset($value['@date'])) return \Carbon\Carbon::parse($value['@date']);
        if (isset($value['@enum'])) {
            if (!str_starts_with($value['@enum'], 'App\\Enums\\')) throw new \LogicException('Unsupported enum');
            return $value['@enum']::from($value['value']);
        }
        return array_map(self::decode(...), $value);
    }

    public static function message(object $message): array
    {
        $class = get_class($message);
        if (!str_starts_with($class, 'App\\Mail\\') && !str_starts_with($class, 'App\\Notifications\\')) throw new \LogicException('Unsupported durable message');
        $reflection = new \ReflectionClass($message); $arguments = [];
        foreach ($reflection->getConstructor()?->getParameters() ?? [] as $parameter) {
            $name = $parameter->getName();
            if ($reflection->hasProperty($name)) $arguments[] = self::encode($reflection->getProperty($name)->getValue($message));
            elseif ($parameter->isDefaultValueAvailable()) $arguments[] = self::encode($parameter->getDefaultValue());
            else throw new \LogicException('Message constructor requires a durable adapter: '.$class);
        }
        return ['class' => $class, 'arguments' => $arguments];
    }

    public static function restore(array $payload): object
    {
        $class = $payload['class'];
        if ((!str_starts_with($class, 'App\\Mail\\') || !is_subclass_of($class, \Illuminate\Mail\Mailable::class))
            && (!str_starts_with($class, 'App\\Notifications\\') || !is_subclass_of($class, \Illuminate\Notifications\Notification::class))) throw new \LogicException('Unsupported durable message class');
        return new $class(...array_map(self::decode(...), $payload['arguments']));
    }
}
