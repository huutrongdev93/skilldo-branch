<?php

use SkillDo\Http\Request;

class BranchController extends MY_Controller
{
    function __construct()
    {
        add_action('beforeLoad', function () {
            Cms::set('loadWidget', false);
        });

        parent::__construct();

        Cms::setData('module', 'branch');
    }

    public function index(Request $request): void
    {
        Cms::setData('table', (new \Branch\Table()));

        $this->template->setView(BRANCH_NAME . '/views/admin/index', 'plugin');

        $this->template->render();
    }

    public function add(Request $request): void
    {
        Cms::setData('module', 'branch');

        Admin::creatForm('branch');

        $this->template->setView(BRANCH_NAME . '/views/admin/save', 'plugin');

        $this->template->render();
    }

    public function edit(Request $request, $id): void
    {
        $object = Branch::find($id);

        Cms::setData('object', $object);

        Cms::setData('module', 'branch');

        Admin::creatForm('branch', $object);

        $this->template->setView(BRANCH_NAME . '/views/admin/save', 'plugin');

        $this->template->render();
    }
}