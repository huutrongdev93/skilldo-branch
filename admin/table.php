<?php
namespace Branch;
use Admin;
use Branch;
use SkillDo\Table\Columns\ColumnText;
use SkillDo\Table\Columns\ColumnView;
use SkillDo\Table\SKDObjectTable;
use Url;
use Qr;

class Table extends SKDObjectTable
{
    protected string $module = 'branch';

    protected mixed $model = Branch::class;

    function getColumns()
    {
        $this->_column_headers = [
            'cb' => 'cb',
            'name' => [
                'label' => trans('Tên chi nhánh'),
                'column' => fn($item, $args) => ColumnText::make('name', $item, $args),
            ],
            'phone' => [
                'label' => trans('Số điện thoại'),
                'column' => fn($item, $args) => ColumnText::make('phone', $item, $args),
            ],
            'email' => [
                'label' => trans('Email'),
                'column' => fn($item, $args) => ColumnText::make('email', $item, $args),
            ],
            'address' => [
                'label' => trans('Địa chỉ'),
                'column' => fn($item, $args) => ColumnText::make('address', $item, $args),
            ],
            'isDefault' => [
                'label' => trans('Mặc định'),
                'column' => fn($item, $args) => ColumnView::make('isDefault', $item, $args)->html(function ($column) {
                    echo '<div class="form-check"><input type="radio" class="js_branch_btn_default form-check-input" data-id="'.$column->item->id.'" '.($column->item->isDefault == 1 ? 'checked' : '').'></div>';
                }),
            ],
            'status'   => [
                'label' => trans('user.status'),
                'column' => fn($item, $args) =>
                \SkillDo\Table\Columns\ColumnBadge::make('status', $item, $args)
                    ->color(fn (string $state): string => Status::tryFrom($state)->badge())
                    ->label(fn (string $state): string => Status::tryFrom($state)->label())
                    ->attributes(fn ($item): array => [
                        'data-id' => $item->id,
                        'data-status' => $item->status,
                    ])
                    ->class(['js_branch_btn_status'])
            ],
            'action' => trans('table.action')
        ];

        return apply_filters('manage_branch_columns', $this->_column_headers);
    }

    function actionButton($item, $module, $table): array
    {
        $buttons[] = Admin::button('blue', [
            'href' => Url::route('admin.branch.edit', ['id' => $item->id]),
            'icon' => Admin::icon('edit')
        ]);

        return $buttons;
    }

    function headerButton(): array
    {
        $buttons[] = Admin::button('green', [
            'icon' => Admin::icon('add'),
            'text' => 'Thêm chi nhánh',
            'href' => Url::route('admin.branch.add')
        ]);

        $buttons[] = Admin::button('reload');

        return $buttons;
    }

//    function headerFilter(Form $form, Request $request)
//    {
//        return $form;
//    }
//
//    function headerSearch(Form $form, Request $request): Form
//    {
//        return $form;
//    }
//
    public function queryFilter(Qr $query, \SkillDo\Http\Request $request): Qr
    {
        $query->whereNotNull('status');

        return $query;
    }
//
//    public function queryDisplay(Qr $query, \SkillDo\Http\Request $request, $data = []): Qr
//    {
//        $query = parent::queryDisplay($query, $request, $data);
//
//        return $query;
//    }
}