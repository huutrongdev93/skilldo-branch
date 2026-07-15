<?php
namespace BranchManagement\Enums;

use Illuminate\Support\Collection;

enum BranchStatus: string
{
    case WORKING      = 'working'; //hoàn thành

    case STOP       = 'stop'; //hoàn thành

    public function label(): string
    {
        return match ($this)
        {
            self::WORKING  => trans('Đang sử dụng'),
            self::STOP  => trans('Ngưng sử dụng'),
            default => null,
        };
    }

    public function color(): string
    {
        return match ($this)
        {
            self::WORKING   => '#186caa',
            self::STOP   => '#fdbd41',
            default => null,
        };
    }

    public function badge(): string
    {
        return match ($this)
        {
            self::WORKING   => 'green',
            self::STOP   => 'red',
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