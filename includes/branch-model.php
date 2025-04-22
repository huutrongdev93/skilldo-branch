<?php
Class Branch extends \SkillDo\Model\Model {

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

    protected bool $widthStop = false;

    public function setWidthStop($widthStop = false): static
    {
        $this->widthStop = $widthStop;
        return $this;
    }

    static function widthStop(?Qr $query = null): static
    {
        $model = new static;

        if($query instanceof Qr)
        {
            $model->setQuery($query);
        }

        return $model->setWidthStop(true);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::setQueryBuilding(function (Branch $object, Qr $query) {
            if (!$object->widthStop) {
                if (!$query->isWhere($object->getTable() . '.status') && !$query->isWhere('status')) {
                    $query->where($object->getTable() . '.status', \Branch\Status::working->value);
                }
            }

            $object->setWidthStop();
        });

        static::saved(function(Branch $object, $action)
        {
            \SkillDo\Cache::delete('branch_', true);
        });
    }
}