@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.payments', 1)]))

@section('content')
    <!-- Default box -->
    <div class="box box-success">
        {!! Form::open(['url' => 'expenses/payments', 'files' => true, 'role' => 'form']) !!}

        <div class="box-body">
            {{ Form::textGroup('paid_at', trans('general.date'), 'calendar',['id' => 'paid_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy-mm-dd\'', 'data-mask' => ''], Date::now()->toDateString()) }}

            {{ Form::textGroup('amount', trans('general.amount'), 'money', ['required' => 'required', 'autofocus' => 'autofocus']) }}

            {{ Form::selectGroup('account_id', trans_choice('general.accounts', 1), 'university', $accounts, setting('general.default_account')) }}

            <div class="form-group col-md-6 {{ $errors->has('currency_code') ? 'has-error' : ''}}">
                {!! Form::label('currency_code', trans_choice('general.currencies', 1), ['class' => 'control-label']) !!}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-exchange"></i></div>
                    {!! Form::text('currency', $currencies[$account_currency_code], ['id' => 'currency', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) !!}
                    {!! Form::hidden('currency_code', $account_currency_code, ['id' => 'currency_code', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
                {!! $errors->first('currency_code', '<p class="help-block">:message</p>') !!}
            </div>

            {{ Form::textareaGroup('description', trans('general.description')) }}

            {{ Form::selectGroup('category_id', trans_choice('general.categories', 1), 'folder-open-o', $categories) }}

            <div class="form-group col-md-6">
                {!! Form::label('vendor_id', trans_choice('general.vendors', 1), ['class' => 'control-label']) !!}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                    {!! Form::select('vendor_id', $vendors, null, array_merge(['id' => 'vendor_id', 'class' => 'form-control', 'placeholder' => trans('general.form.select.field', ['field' => trans_choice('general.vendors', 1)])])) !!}
                    <span class="input-group-btn">
                    <button type="button" onclick="createVendor();" class="btn btn-primary">{{ trans('general.add_new') }}</button>
                </span>
                </div>
            </div>

            {{ Form::selectGroup('payment_method', trans_choice('general.payment_methods', 1), 'credit-card', $payment_methods, setting('general.default_payment_method')) }}

            {{ Form::textGroup('reference', trans('general.reference'), 'file-text-o',[]) }}

            {{ Form::fileGroup('attachment', trans('general.attachment')) }}
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            {{ Form::saveButtons('expenses/payments') }}
        </div>
        <!-- /.box-footer -->

        {!! Form::close() !!}
    </div>
@endsection

@push('js')
<script src="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('public/js/bootstrap-fancyfile.js') }}"></script>
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/bootstrap-fancyfile.css') }}">
@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        //Date picker
        $('#paid_at').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        $("#account_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.accounts', 1)]) }}"
        });

        $("#category_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.categories', 1)]) }}"
        });

        $("#vendor_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.vendors', 1)]) }}"
        });

        $("#payment_method").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.payment_methods', 1)]) }}"
        });

        $('#attachment').fancyfile({
            text  : '{{ trans('general.form.select.file') }}',
            style : 'btn-default',
            placeholder : '{{ trans('general.form.no_file_selected') }}'
        });

        $(document).on('change', '#account_id', function (e) {
            $.ajax({
                url: '{{ url("settings/currencies/currency") }}',
                type: 'GET',
                dataType: 'JSON',
                data: 'account_id=' + $(this).val(),
                success: function(data) {
                    $('#currency').val(data.currency_name);
                    $('#currency_code').val(data.currency_code);
                }
            });
        });
    });

    function createVendor() {
        $('#modal-create-vendor').remove();

        modal  = '<div class="modal fade" id="modal-create-vendor" style="display: none;">';
        modal += '  <div class="modal-dialog  modal-lg">';
        modal += '      <div class="modal-content">';
        modal += '          <div class="modal-header">';
        modal += '              <h4 class="modal-title">{{ trans('general.title.new', ['type' => trans_choice('general.vendors', 1)]) }}</h4>';
        modal += '          </div>';
        modal += '          <div class="modal-body">';
        modal += '              {!! Form::open(['id' => 'form-create-vendor', 'role' => 'form']) !!}';
        modal += '              <div class="row">';
        modal += '                  <div class="form-group col-md-6 required">';
        modal += '                      <label for="name" class="control-label">{{ trans('general.name') }}</label>';
        modal += '                      <div class="input-group">';
        modal += '                          <div class="input-group-addon"><i class="fa fa-id-card-o"></i></div>';
        modal += '                          <input class="form-control" placeholder="{{ trans('general.name') }}" required="required" name="name" type="text" id="name">';
        modal += '                      </div>';
        modal += '                  </div>';
        modal += '                  <div class="form-group col-md-6 required">';
        modal += '                      <label for="email" class="control-label">{{ trans('general.email') }}</label>';
        modal += '                      <div class="input-group">';
        modal += '                          <div class="input-group-addon"><i class="fa fa-envelope"></i></div>';
        modal += '                          <input class="form-control" placeholder="{{ trans('general.email') }}" required="required" name="email" type="text" id="email">';
        modal += '                      </div>';
        modal += '                  </div>';
        modal += '                  <div class="form-group col-md-6">';
        modal += '                      <label for="tax_number" class="control-label">{{ trans('general.tax_number') }}</label>';
        modal += '                      <div class="input-group">';
        modal += '                          <div class="input-group-addon"><i class="fa fa-percent"></i></div>';
        modal += '                          <input class="form-control" placeholder="{{ trans('general.tax_number') }}" name="tax_number" type="text" id="tax_number">';
        modal += '                      </div>';
        modal += '                  </div>';
        modal += '                  <div class="form-group col-md-6 required">';
        modal += '                      <label for="email" class="control-label">{{ trans_choice('general.currencies', 1) }}</label>';
        modal += '                      <div class="input-group">';
        modal += '                          <div class="input-group-addon"><i class="fa fa-exchange"></i></div>';
        modal += '                          <select class="form-control" required="required" id="currency_code" name="currency_code">';
        modal += '                              <option value="">{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}</option>';
        @foreach($currencies as $currency_code => $currency_name)
                modal += '                              <option value="{{ $currency_code }}" {{ (setting('general.default_currency') == $currency_code) ? 'selected' : '' }}>{{ $currency_name }}</option>';
        @endforeach
                modal += '                          </select>';
        modal += '                      </div>';
        modal += '                  </div>';
        modal += '                  <div class="form-group col-md-12">';
        modal += '                      <label for="address" class="control-label">{{ trans('general.address') }}</label>';
        modal += '                      <textarea class="form-control" placeholder="{{ trans('general.address') }}" rows="3" name="address" cols="50" id="address"></textarea>';
        modal += '                  </div>';
        modal += '                  {!! Form::hidden('enabled', '1', []) !!}';
        modal += '              </div>';
        modal += '              {!! Form::close() !!}';
        modal += '          </div>';
        modal += '          <div class="modal-footer">';
        modal += '              <div class="pull-left">';
        modal += '              {!! Form::button('<span class="fa fa-save"></span> &nbsp;' . trans('general.save'), ['type' => 'button', 'id' =>'button-create-vendor', 'class' => 'btn btn-success']) !!}';
        modal += '              <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times-circle"></span> &nbsp;{{ trans('general.cancel') }}</button>';
        modal += '              </div>';
        modal += '          </div>';
        modal += '      </div>';
        modal += '  </div>';
        modal += '</div>';

        $('body').append(modal);

        $("#modal-create-vendor #currency_code").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
        });

        $('#modal-create-vendor').modal('show');
    }

    $(document).on('click', '#button-create-vendor', function (e) {
        $('#modal-create-vendor .modal-header').before('<span id="span-loading" style="position: absolute; height: 100%; width: 100%; z-index: 99; background: #6da252; opacity: 0.4;"><i class="fa fa-spinner fa-spin" style="font-size: 16em !important;margin-left: 35%;margin-top: 8%;"></i></span>');

        $.ajax({
            url: '{{ url("expenses/vendors/vendor") }}',
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-create-vendor").serialize(),
            beforeSend: function () {
                $(".form-group").removeClass("has-error");
                $(".help-block").remove();
            },
            success: function(data) {
                $('#span-loading').remove();

                $('#modal-create-vendor').modal('hide');

                $("#vendor_id").append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $("#vendor_id").select2('refresh');
            },
            error: function(error, textStatus, errorThrown) {
                $('#span-loading').remove();

                if (error.responseJSON.name) {
                    $("input[name='name']").parent().parent().addClass('has-error');
                    $("input[name='name']").parent().after('<p class="help-block">' + error.responseJSON.name + '</p>');
                }

                if (error.responseJSON.email) {
                    $("input[name='email']").parent().parent().addClass('has-error');
                    $("input[name='email']").parent().after('<p class="help-block">' + error.responseJSON.email + '</p>');
                }

                if (error.responseJSON.currency_code) {
                    $("select[name='currency_code']").parent().parent().addClass('has-error');
                    $("select[name='currency_code']").parent().after('<p class="help-block">' + error.responseJSON.currency_code + '</p>');
                }
            }
        });
    });
</script>
@endpush
