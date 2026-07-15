<?php
namespace BranchManagement\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use BranchManagement\Models\Branch;
use BranchManagement\Modules\Admin\Branch\BranchTable;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class BranchController extends Controller
{
    function __construct()
    {
        parent::__construct();

        Cms::setData('module', 'branch');
    }

    public function index(Request $request)
    {
        Cms::setData('table', (new BranchTable()));

        return Cms::view('branch-management::admin/index');
    }

    public function add(Request $request)
    {
        Cms::setData('form', FormAdminHelper::getForm('branch'));

        return Cms::view('branch-management::admin/save');
    }

    public function edit(Request $request, $id)
    {
        $object = Branch::find($id);

        if(noItems($object))
        {
            return Admin::pageNotFound();
        }

        Cms::setData('object', $object);

        Cms::setData('form', FormAdminHelper::getForm('branch', $object));

        return Cms::view('branch-management::admin/save');
    }
}