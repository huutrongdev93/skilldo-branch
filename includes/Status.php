<?php
namespace Branch;

use Illuminate\Support\Collection;

enum Status: string
{
    case working      = 'working'; //hoàn thành
    case stop       = 'stop'; //hoàn thành

    public function label(): string
    {
        return match ($this) {
            self::working  => trans('Đang sử dụng'),
            self::stop  => trans('Ngưng sử dụng'),
            default => null,
        };
    }

    public function color(): string
    {
        return match ($this)
        {
            self::working   => '#186caa',
            self::stop   => '#fdbd41',
            default => null,
        };
    }

    public function badge(): string
    {
        return match ($this)
        {
            self::working   => 'green',
            self::stop   => 'red',
            default => null,
        };
    }

    static function options(): Collection
    {
        return new Collection(array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases()));
    }

    static function has(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }
}