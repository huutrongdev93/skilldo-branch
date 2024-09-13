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
}