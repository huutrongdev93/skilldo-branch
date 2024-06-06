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

    static function delete($branchID = 0): array|false
    {
        $branchID = (int)Str::clear($branchID);

        if($branchID == 0) return false;

        $model = model(static::$table);

        $branch  = static::get($branchID);

        if(have_posts($branch)) {

            do_action('delete_branch', $branchID);

            if($model->delete(Qr::set($branchID))) {

                do_action('delete_branch_success', $branchID);

                return [$branchID];
            }
        }

        return false;
    }

    static function deleteList($branchID = []) {

        if(have_posts($branchID)) {

            $model   = model(static::$table);

            if($model->delete(Qr::set()->whereIn('id', $branchID))) {

                do_action('delete_branch_list_trash_success', $branchID);

                return $branchID;
            }
        }

        return false;
    }
}