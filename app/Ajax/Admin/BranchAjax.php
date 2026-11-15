<?php
namespace BranchManagement\Ajax\Admin;
use BranchManagement\Enums\BranchStatus;
use BranchManagement\Models\Branch;
use SkillDo\Cms\Table\Columns\ColumnBadge;
use SkillDo\Support\Auth;
use Illuminate\Support\Str;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

class BranchAjax
{
    static function add(Request $request): void
    {
        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_add'))
            {
                response()->error(trans('branch-management::ajax.error.permission'));
            }

            $validate = $request->validate([
                'branch.name' => Rule::make(trans('branch-management::system.field.name'))->notEmpty(),
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

            $error = Branch::create($branch);

            if(is_skd_error($error))
            {
                response()->error($error);
            }

            response()->success(trans('ajax.add.success'));
        }

        response()->error(trans('ajax.add.error'));
    }

    static function save(Request $request): void
    {
        if(!Auth::hasCap('branch_edit'))
        {
            response()->error(trans('branch-management::ajax.error.permission'));
        }

        $validate = $request->validate([
            'branch.id' => Rule::make('id chi nhánh')->notEmpty()->integer()->min(1),
            'branch.name' => Rule::make(trans('branch-management::system.field.name'))->notEmpty(),
            'branch.address' => Rule::make(trans('general.address'))->notEmpty(),
            'branch.phone' => Rule::make(trans('general.phone'))->notEmpty()->phone(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $id = $request->input('branch.id');

        $branch = Branch::find($id);

        if(!hasItems($branch))
        {
            response()->error(trans('branch-management::ajax.error.notFound'));
        }

        $error = apply_filters('admin_branch_save_validation', [], $branch);

        if(is_skd_error($error))
        {
            response()->error($error);
        }

        $branch->name = $request->input('branch.name');

        $branch->address = $request->input('branch.address');

        $branch->phone = $request->input('branch.phone');

        // Form gửi thêm email / tỉnh / phường: chỉ ghi khi có gửi để không xoá nhầm dữ liệu cũ
        if($request->has('branch.email'))
        {
            $branch->email = Str::clear($request->input('branch.email'));
        }

        if($request->has('branch.city'))
        {
            $branch->city = (int)$request->input('branch.city');
        }

        if($request->has('branch.ward'))
        {
            $branch->ward = (int)$request->input('branch.ward');
        }

        $makeDefault = $request->has('branch.isDefault') && $request->input('branch.isDefault') == 1;

        // Chi nhánh ngừng hoạt động không được làm mặc định (luôn phải có đúng 1 mặc định đang chạy)
        if($makeDefault && $branch->status != BranchStatus::WORKING->value)
        {
            response()->error('Không thể đặt chi nhánh đã ngưng sử dụng làm mặc định');
        }

        if($makeDefault)
        {
            $branch->isDefault = 1;
        }

        // Gộp đổi mặc định + lưu vào một transaction để không bị 0 hoặc 2 chi nhánh mặc định
        \Illuminate\Support\Facades\DB::transaction(function () use ($branch, $id, $makeDefault) {
            if($makeDefault)
            {
                Branch::where('id', '<>', $id)->update(['isDefault' => 0]);
            }

            $branch->save();
        });

        response()->success(trans('ajax.save.success'));
    }

    static function status(Request $request): void
    {
        if(!Auth::hasCap('branch_edit'))
        {
            response()->error(trans('branch-management::ajax.error.permission'));
        }

        $validate = $request->validate([
            'id' => Rule::make('Id chi nhánh')->notEmpty()->integer()->min(1),
            'status' => Rule::make('Trạng thái')->notEmpty()->in(array_column(BranchStatus::cases(), 'value')),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $id = (int)$request->input('id');

        // Không dùng widthStop(): scope đó chỉ trả chi nhánh đang chạy nên chi nhánh đã ngưng sẽ không bật lại được
        $object = Branch::whereKey($id)->first();

        if(!hasItems($object))
        {
            response()->error('Chi nhánh không tồn tại');
        }

        $status = Str::clear($request->input('status'));

        if($status == $object->status)
        {
            response()->error('Trạng thái Chi nhánh không thay đổi');
        }

        if($status == BranchStatus::STOP->value && $object->isDefault == 1)
        {
            response()->error('Không thể ngừng sử dụng chi nhánh mặc định');
        }

        $object->status = $status;

        $object->save();

        response()->success(trans('ajax.update.success'), ColumnBadge::make('status', [], [])
            ->value($object->status)
            ->color(fn (string $state): string => BranchStatus::tryFrom($state)->badge())
            ->label(fn (string $state): string => BranchStatus::tryFrom($state)->label())
            ->attributes(fn ($item): array => [
                'data-id' => $object->id,
                'data-status' => $object->status,
            ])
            ->class(['js_branch_btn_status'])->view());
    }

    static function default(\SkillDo\Http\Request $request): void
    {
        if(!Auth::hasCap('branch_edit'))
        {
            response()->error(trans('branch-management::ajax.error.permission'));
        }

        $validate = $request->validate([
            'id' => Rule::make('Id chi nhánh')->notEmpty()->integer()->min(1),
        ]);

        if ($validate->fails()) {
            response()->error($validate->errors());
        }

        $id = (int)$request->input('id');

        $object = Branch::whereKey($id)->first();

        if(!hasItems($object))
        {
            response()->error('Chi nhánh không tồn tại');
        }

        if($object->isDefault == 1)
        {
            response()->error('Chi nhánh này đang là chi nhánh mặc định');
        }

        // Chi nhánh ngưng sử dụng không được làm mặc định
        if($object->status != BranchStatus::WORKING->value)
        {
            response()->error('Không thể đặt chi nhánh đã ngưng sử dụng làm mặc định');
        }

        // Bỏ mặc định ở MỌI chi nhánh (không lọc theo status) rồi đặt chi nhánh này, trong một transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($object, $id) {
            Branch::where('id', '<>', $id)->update(['isDefault' => 0]);

            $object->isDefault = 1;

            $object->save();
        });

        response()->success(trans('ajax.update.success'));
    }
}




