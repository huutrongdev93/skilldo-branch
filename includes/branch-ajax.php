<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

class AdminBranchAjax {
    static function add(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_add')) {
                response()->error(trans('branch.ajax.error.permission'));
            }

            $validate = $request->validate([
                'branch.name' => Rule::make(trans('branch.field.name'))->notEmpty(),
                'branch.address' => Rule::make(trans('address'))->notEmpty(),
                'branch.phone' => Rule::make(trans('phone'))->notEmpty(),
            ]);

            if ($validate->fails()) {
                response()->error($validate->errors());
            }

            $branch = $request->input('branch');

            $error = apply_filters('admin_branch_save_validation', [], $branch);

            if(is_skd_error($error)) {

                response()->error($error);
            }

            $error = Branch::insert($branch);

            if(is_skd_error($error)) {

                response()->error($error);
            }

            response()->success(trans('ajax.add.success'));
        }

        response()->error(trans('ajax.add.error'));
    }

    static function save(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_edit')) {
                response()->error(trans('branch.ajax.error.permission'));
            }

            $validate = $request->validate([
                'branch.id' => Rule::make('id chi nhánh')->notEmpty(),
                'branch.name' => Rule::make(trans('branch.field.name'))->notEmpty(),
                'branch.address' => Rule::make(trans('general.address'))->notEmpty(),
                'branch.phone' => Rule::make(trans('general.phone'))->notEmpty(),
            ]);

            if ($validate->fails()) {
                response()->error($validate->errors());
            }

            $branch = $request->input('branch');

            $branch_old = Branch::get($branch['id']);

            if(!have_posts($branch_old)) {

                response()->error(trans('branch.ajax.error.notFound'));
            }

            $error = apply_filters('admin_branch_save_validation', [], $branch, $branch_old);

            if(is_skd_error($error)) {

                response()->error($error);
            }

            $error = Branch::insert($branch, $branch_old);

            if(!is_skd_error($error)) {

                response()->error($error);
            }

            if(isset($branch['isDefault']) && $branch['isDefault'] == 1) {

                Branch::where('id', '<>', $error)->update(['isDefault' => 0]);
            }

            response()->success(trans('ajax.save.success'));
        }

        response()->error(trans('ajax.save.error'));
    }

    static function status(\SkillDo\Http\Request $request): void
    {
        $validate = $request->validate([
            'id' => Rule::make('Id chi nhánh')->notEmpty()->integer()->min(1),
            'status' => Rule::make('Trạng thái')->notEmpty()->in(array_column(\Branch\Status::cases(), 'value')),
        ]);

        if ($validate->fails()) {
            response()->error($validate->errors());
        }

        $id = (int)$request->input('id');

        $object = Branch::widthStop()->whereKey($id)->first();

        if(!have_posts($object)) {
            response()->error('Chi nhánh không tồn tại');
        }

        $status = Str::clear($request->input('status'));

        if($status == $object->status)
        {
            response()->error('Trạng thái Chi nhánh không thay đổi');
        }

        if($status == \Branch\Status::stop->value && $object->isDefault == 1)
        {
            response()->error('Không thể ngừng sử dụng chi nhánh mặc định');
        }

        $object->status = $status;

        $object->save();

        response()->success(trans('ajax.update.success'), \SkillDo\Table\Columns\ColumnBadge::make('status', [], [])
            ->value($object->status)
            ->color(fn (string $state): string => \Branch\Status::tryFrom($state)->badge())
            ->label(fn (string $state): string => \Branch\Status::tryFrom($state)->label())
            ->attributes(fn ($item): array => [
                'data-id' => $object->id,
                'data-status' => $object->status,
            ])
            ->class(['js_branch_btn_status'])->view());
    }

    static function default(\SkillDo\Http\Request $request): void
    {
        $validate = $request->validate([
            'id' => Rule::make('Id chi nhánh')->notEmpty()->integer()->min(1),
        ]);

        if ($validate->fails()) {
            response()->error($validate->errors());
        }

        $id = (int)$request->input('id');

        $object = Branch::widthStop()->whereKey($id)->first();

        if(!have_posts($object)) {
            response()->error('Chi nhánh không tồn tại');
        }

        if($object->isDefault == 1) {
            response()->error('Chi nhánh này đang là chi nhánh mặc định');
        }

        Branch::widthStop()->where('isDefault', 1)->update(['isDefault' => 0]);

        $object->isDefault = 1;

        $object->save();

        response()->success(trans('ajax.update.success'));
    }
}

Ajax::admin('AdminBranchAjax::add');
Ajax::admin('AdminBranchAjax::save');
Ajax::admin('AdminBranchAjax::status');
Ajax::admin('AdminBranchAjax::default');


