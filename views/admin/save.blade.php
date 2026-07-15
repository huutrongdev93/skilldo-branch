{!!
    Admin::partial('resources/page-default/page-save', [
        'module'  => 'branch',
        'model' => \BranchManagement\Models\Branch::class,
        'object' => $object ?? null,
    ]);
!!}