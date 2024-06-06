<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

class AdminBranchAjax {
    #[NoReturn]
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

    #[NoReturn]
    static function save(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_edit')) {
                response()->error(trans('dmin.branch.ajax.error.permission'));
            }

            $validate = $request->validate([
                'branch.id' => Rule::make('id chi nhánh')->notEmpty(),
                'branch.name' => Rule::make(trans('branch.field.name'))->notEmpty(),
                'branch.address' => Rule::make(trans('address'))->notEmpty(),
                'branch.phone' => Rule::make(trans('phone'))->notEmpty(),
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

            if(isset($branch['default']) && $branch['default'] == 1) {

                Branch::where('id', '<>', $error)->update(['default' => 0]);
            }

            response()->success(trans('ajax.save.success'));
        }

        response()->error(trans('ajax.save.error'));
    }

    #[NoReturn]
    static function areaSave(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_edit')) {
                response()->error(trans('branch.ajax.error.permission'));
            }

            $pick_sale_area = $request->input('pick_sale_area');

            $id      = (int)$request->input('id');

            $branch = branch::get($id);

            if(!have_posts($branch)) {
                response()->error(trans('branch.ajax.error.notFound'));
            }

            if(is_array($pick_sale_area)) {

                if(have_posts($pick_sale_area)) {
                    foreach ($pick_sale_area as $key => $area) {
                        $pick_sale_area[$key] = trim(Str::clear($area));
                    }
                }

                $branch->area = $pick_sale_area;

                Branch::insert(['id' => $id, 'area' => $pick_sale_area], $branch);

                response()->success(trans('ajax.save.success'), $pick_sale_area);
            }
        }

        response()->error(trans('ajax.save.error'));
    }

    #[NoReturn]
    static function stop(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_edit')) {
                response()->error(trans('branch.ajax.error.permission'));
            }

            $branch_id = $request->input('id');

            $branch = Branch::get($branch_id);

            if(have_posts($branch)) {

                if($branch->default ==  1) {

                    response()->error(trans('branch.ajax.error.stopDefault'));
                }

                $result = Branch::where('id', $branch->id)->update(['status' => 'stop']);

                if($result) {

                    response()->success(trans('ajax.update.success'));
                }
            }
        }

        response()->error(trans('ajax.update.error'));
    }

    #[NoReturn]
    static function start(Request $request, $model): void
    {

        if($request->isMethod('post')) {

            if(!Auth::hasCap('branch_edit')) {
                response()->error(trans('branch.ajax.error.permission'));
            }

            $branch_id = $request->input('id');

            $branch = Branch::get($branch_id);

            if(have_posts($branch)) {

                if($branch->status ==  'working') {

                    response()->error(trans('branch.ajax.error.start'));
                }

                $result = Branch::where('id', $branch->id)->update(['status' => 'working']);

                if($result) {

                    response()->success(trans('ajax.update.success'));
                }
            }
        }

        response()->error(trans('ajax.update.error'));
    }
}

Ajax::admin('AdminBranchAjax::add');
Ajax::admin('AdminBranchAjax::save');
Ajax::admin('AdminBranchAjax::areaSave');
Ajax::admin('AdminBranchAjax::stop');
Ajax::admin('AdminBranchAjax::start');


