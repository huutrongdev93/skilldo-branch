<?php
namespace BranchManagement\Models;

use SkillDo\Cache\Cache;
use SkillDo\Database\Eloquent\Model;

Class Branch extends Model
{

    protected string $table = 'branchs';

    protected array $columns = [
        'area'  => ['array'],
    ];

    protected array $rules = [
        'hooks'     => [
            'columns' => 'columns_db_branch',
            'data' => 'pre_insert_branch_data',
        ]
    ];

    static function scopeWidthStop(\SkillDo\Database\Eloquent\Builder $query)
    {
        if (!$query->isWhere($query->getTable() . '.status') && !$query->isWhere('status'))
        {
            $query->where($query->getTable() . '.status', \BranchManagement\Enums\BranchStatus::WORKING->value);
        }

        return $query;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function(Branch $object, $action)
        {
            Cache::delete('branch_', true);
        });
    }
}