
<div class="ui-title-bar__group box mb-3">
    <div class="box-content d-flex justify-content-between">
        <div>
            <h1 class="ui-title-bar__title mb-2">{!! trans('branch.list') !!}</h1>
            <div class="ui-title-bar__des mt-1">
                {!! trans('branch.description') !!}, v.v...
            </div>
        </div>
        <div>
            @if(Auth::hasCap('branch_add'))
                {!! Admin::button('green', [
                    'class' => 'js_stock_branch__add',
                    'icon' => Admin::icon('add'),
                    'text' => trans('branch.button.add'),
                    'type' => 'button'
                ]) !!}
            @endif
        </div>
    </div>
</div>
<div class="row">
    @foreach ($branches as $key => $branch)
        <div class="col-md-6">
            <div class="box pick-address">
                <div class="box-header">
                    <h4 class="box-title d-flex justify-content-between">
                        <div>{{trans('branch.title')}} <b>#{!! $branch->id !!}</b></div>
                        @if($branch->status == 'working')
                            <input type="hidden" name="branch[id]" class="form-control" value="{!! $branch->id !!}">
                            <div class="radio">
                                <label for="branch_{{ $branch->id }}_default">
                                    <input id="branch_{{ $branch->id }}_default" name="branch[default]" type="radio" value="1" class="icheck" {{ ($branch->default == 1) ? 'checked' : '' }}>
                                    {!! trans('branch.button.default') !!}
                                </label>
                            </div>
                        @else
                            <div class="radio" style="height: 22px;">
                                <label style="color:var(--red); font-weight: bold">{{trans('branch.button.stop')}}</label>
                            </div>
                        @endif
                    </h4>
                </div>
                <div class="box-content">
                    <div class="row m-1">
                        {!! $branch->form->html() !!}
                    </div>
                </div>
                <div class="box-footer text-right">
                    @if(Auth::hasCap('branch_edit'))
                        @if($branch->status == 'working')
                            {!!
                                 Admin::btnConfirm('red', [
                                     'text'         => trans('branch.button.stop'),
                                     'ajax'         => 'AdminBranchAjax::stop',
                                     'heading'      => trans('branch.button.stop'),
                                     'description'  => trans('branch.button.stop.confirm'),
                                     'id'           => $branch->id
                                ])
                            !!}
                            <button type="button" class="btn btn-blue js_stock_branch__sale_area" data-code="{{ $branch->id }}" data-area="{!! htmlspecialchars(json_encode((!empty($branch->area) && Str::isSerialized($branch->area)) ? unserialize($branch->area) : [])) !!}">
                                {{trans('branch.button.area')}}
                            </button>
                            <button type="button" class="btn btn-blue js_stock_branch__save">{{trans('branch.button.save')}}</button>
                        @else
                            {!!
                                 Admin::btnConfirm('green', [
                                     'text'         => trans('branch.button.start'),
                                     'ajax'         => 'AdminBranchAjax::start',
                                     'heading'      => trans('branch.button.start'),
                                     'description'  => trans('branch.button.start.confirm'),
                                     'id'           => $branch->id
                                ])
                            !!}
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="js_stock_branch__model_add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{trans('branch.modal.add.title')}}</h4>
            </div>
            <div class="modal-body">
                <div class="clearfix"></div>
                <div class="box-content pick-address row">
                    {!! $form->html(); !!}
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{!! trans('button.close') !!}</button>
                <button type="button" class="btn btn-blue js_stock_branch__btn_add">{!! trans('button.save') !!}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="js_stock_branch__model_area">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{trans('branch.modal.area.title')}}</h4>
            </div>
            <div class="modal-body">
                <div class="box-content">
                    <div class="form-group row">
                        @foreach ($provinces as $key => $name)
                            <div class="col-md-3">
                                <label>
                                    <input type="checkbox" name="pick_sale_area[]" value="{{$key}}">
                                    {!! $name !!}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-blue js_stock_branch__sale_area_save">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    .radio label, .checkbox label {
        padding-left:0;
    }
    .select2-container { width: 100%!important; }
    .form-group { overflow: hidden; margin-bottom: 10px;}
    .select2-container--default .select2-selection--single {
        border: 1px solid #ccc;
        height: 40px;
        line-height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
    }
</style>

<script type="text/javascript" defer>
    $(function() {

        let branch_id = 0;

        let inputCity = $('.stock-locations-city');

        $('.js_stock_branch__add').click(function(){
            $('#js_stock_branch__model_add').modal('show');
        });

	    inputCity.each(function (index, value) {

            let pick = $(this).closest('.pick-address');

            //Load district
            let data = {
                province_id : $(this).val(),
                district_id : pick.find('.stock-locations-district').attr('data-id'),
                action      : 'Cart_Ajax::loadDistricts'
            };

            request.post(ajax , data).then(function(response) {
                if(response.status === 'success') {
                    pick.find('.stock-locations-district').html(response.data);
                }
            });

            //Load ward
            data = {
                district_id : pick.find('.stock-locations-district').attr('data-id'),
                ward_id     : pick.find('.stock-locations-ward').attr('data-id'),
                action      : 'Cart_Ajax::loadWard'
            };

            request.post(ajax , data).then(function(response) {
                if(response.status === 'success') {
                    pick.find('.stock-locations-ward').html(response.data);
                }
            });
        });

	    inputCity.change(function() {

            let pick = $(this).closest('.pick-address');

            let data = {
                province_id : $(this).val(),
                action: 'Cart_Ajax::loadDistricts'
            };

            request.post(ajax , data).then(function(response) {
                if(response.status === 'success') {
                    pick.find('.stock-locations-district').html(response.data);
                    pick.find('.stock-locations-ward').html('<option value="">Chọn phường xã</option>');
                }
            });
        });

        $('.stock-locations-district').change(function() {
            let pick = $(this).closest('.pick-address');
            let data = {
                district_id : $(this).val(),
                action: 'Cart_Ajax::loadWard'
            };
            request.post(ajax , data).then(function(response) {
                if(response.status === 'success') {
                    pick.find('.stock-locations-ward').html(response.data);
                }
            });
        });

        $('.js_stock_branch__btn_add').click(function() {

            let box = $(this).closest('#js_stock_branch__model_add');

            let data = $( ':input', box).serializeJSON();

            data.action  = 'AdminBranchAjax::add';

	        request.post(ajax , data).then(function(response) {

                SkilldoMessage.response(response);

                if(response.status === 'success') {

                    $('#js_stock_branch__model_add').modal('hide');

                    location.reload();
                }
            });

            return false;
        });

        $('.js_stock_branch__sale_area').click(function(){

            branch_id = $(this).attr('data-code');

            let pick_area = JSON.parse($(this).attr('data-area'));

            $('#js_stock_branch__model_area input[type="checkbox"]').each(function (index) {

                let val = $(this).val();

                if($.inArray( val, pick_area ) !== -1) $(this).prop('checked', true);
                else $(this).prop('checked', false);
            });

            $('#js_stock_branch__model_area').modal('show');
        });

        $('.js_stock_branch__sale_area_save').click(function() {

            let box = $(this).closest('#js_stock_branch__model_area');

            let data = $( ':input', box).serializeJSON();

            data.action   = 'AdminBranchAjax::areaSave';

            data.id       = branch_id;

            request.post(ajax , data).then(function( response ) {

                SkilldoMessage.response(response);

                if(response.status === 'success') {

                    $('.js_stock_pick_sale_area[data-code="'+branch_id+'"]').attr('data-area', JSON.stringify(response.data));

                    $('#js_stock_branch__model_area').modal('hide');
                }
            });

            return false;
        });

        $('.js_stock_branch__save').click(function() {

            let box = $(this).closest('.pick-address');

            let data = $( ':input', box).serializeJSON();

            data.action     = 'AdminBranchAjax::save';

            request.post(ajax , data).then(function(response) {
                SkilldoMessage.response(response);
            });

            return false;
        });
    });
</script>