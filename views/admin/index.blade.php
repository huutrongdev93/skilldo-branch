{!!
Admin::partial('components/page-default/page-index', [
    'name'      => 'Danh sách chi nhánh',
    'table'     => $table,
]);
!!}

<div class="modal fade" id="js_branch_modal_status" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thay đổi trạng thái chi nhánh</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! form()->select2('branchStatus', \Branch\Status::options()->pluck('label', 'value')->toArray(), [
                    'label' => 'Trạng thái'
                ])->html() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">{{trans('button.close')}}</button>
                <button type="button" class="btn btn-blue js_branch_btn_status_save">{{trans('button.save')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {

        class BranchIndex
        {
            constructor() {
                this.elements = {
                    status: {
                        modal: $('#js_branch_modal_status'),
                        modalAction: new bootstrap.Modal('#js_branch_modal_status', {backdrop: "static", keyboard: false}),
                        inputStatus: $('#js_branch_modal_status select[name="branchStatus"]')
                    }
                }

                this.data = {
                    status: {
                        id: 0
                    }
                }
            }

            clickStatus(element) {

                this.data.status.id = element.attr('data-id');

                let status = element.attr('data-status');

                this.elements.status.inputStatus.val(status).trigger('change')

                this.elements.status.modalAction.show();
            }

            clickSaveStatus(element) {

                let loading = SkilldoUtil.buttonLoading(element)

                let data = {
                    action: 'AdminBranchAjax::status',
                    status: this.elements.status.inputStatus.val(),
                    id: this.data.status.id
                }

                loading.start()

                request
                    .post(ajax, data)
                    .then(function (response) {

                        SkilldoMessage.response(response);

                        loading.stop();

                        if(response.status === 'success') {

                            $('.tr_' + this.data.status.id).find('.column-status').html(response.data);

                            this.elements.status.modalAction.hide();
                        }
                    }.bind(this))
                    .catch(function (error) {
                        loading.stop();
                    });

                return false;
            }

            clickDefault(element) {

                $('input.js_branch_btn_default').prop('checked', false);

                element.prop('checked', true);

                let id = element.attr('data-id');

                let data = {
                    action: 'AdminBranchAjax::default',
                    id: id
                }

                request
                    .post(ajax, data)
                    .then(function (response) {
                        SkilldoMessage.response(response);
                    }.bind(this));
            }

            events()
            {
                let handler = this;

                $(document)
                    .on('click', '.js_branch_btn_status', function () {
                        handler.clickStatus($(this))
                        return false;
                    })
                    .on('click', '.js_branch_btn_status_save', function () {
                        handler.clickSaveStatus($(this))
                        return false;
                    })
                    .on('change', '.js_branch_btn_default', function () {
                        handler.clickDefault($(this))
                    })
            }
        }
        
        const handler = new BranchIndex()

        handler.events();
    })
</script>