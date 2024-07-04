<?php
Class Branch extends \SkillDo\Model\Model {

    static string $table = 'branchs';

    static array $columns = [
        'area'      => ['array'],
    ];

    static array $rules = [
        'created'   => true,
        'updated'   => true,
        'hooks'     => [
            'columns' => 'columns_db_branch',
            'data' => 'pre_insert_branch_data',
        ]
    ];

    static function deleteList($branchID = []) {

        if(have_posts($branchID)) {

            $model   = model(static::$table);

            if($model::whereIn('id', $branchID)->remove()) {

                do_action('delete_branch_list_trash_success', $branchID);

                return $branchID;
            }
        }

        return false;
    }
}