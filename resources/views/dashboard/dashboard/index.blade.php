@extends('layouts.admin')

@section('title', trans('general.dashboard'))

@section('content')
    <div class="row">
        <!---Income-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>

                <div class="info-box-content">
                    <a href="items/items"><h2 class="text-uppercase text-center text-info">Items</h2></a>
                </div>
            </div>
        </div>

        <!---Expense-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>

                <div class="info-box-content">
                    <a href="incomes/customers"><h2 class="text-uppercase text-center text-danger">Customers</h2></a>
                </div>
            </div>
        </div>

        <!---Profit-->
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <a href="incomes/invoices"><h2 class="text-uppercase text-center text-success">Bills</h2></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/css/daterangepicker.css') }}" />
@endpush

@push('js')
{!! Charts::assets() !!}
<script type="text/javascript" src="{{ asset('public/js/moment/moment.js') }}"></script>
@if (is_file(base_path('public/js/moment/locale/' . strtolower(app()->getLocale()) . '.js')))
<script type="text/javascript" src="{{ asset('public/js/moment/locale/' . strtolower(app()->getLocale()) . '.js') }}"></script>
@elseif (is_file(base_path('public/js/moment/locale/' . language()->getShortCode() . '.js')))
<script type="text/javascript" src="{{ asset('public/js/moment/locale/' . language()->getShortCode() . '.js') }}"></script>
@endif
<script type="text/javascript" src="{{ asset('public/js/daterangepicker/daterangepicker.js') }}"></script>
@endpush

@push('scripts')
<script type="text/javascript">
    $(function() {
        var start = moment().startOf('year');
        var end = moment().endOf('year');

        function cb(start, end) {
            $('#cashflow-range span').html(start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY'));
        }

        $('#cashflow-range').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                '{{ trans("reports.this_year") }}': [moment().startOf('year'), moment().endOf('year')],
                '{{ trans("reports.previous_year") }}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                '{{ trans("reports.this_quarter") }}': [moment().subtract(2, 'months').startOf('month'), moment().endOf('month')],
                '{{ trans("reports.previous_quarter") }}': [moment().subtract(5, 'months').startOf('month'), moment().subtract(3, 'months').endOf('month')],
                '{{ trans("reports.last_12_months") }}': [moment().subtract(11, 'months').startOf('month'), moment().endOf('month')]
            }
        }, cb);

        cb(start, end);
    });

    $(document).ready(function () {
        $('#cashflow-range').on('apply.daterangepicker', function(ev, picker) {
            var period = $('#period').val();

            $.ajax({
                url: '{{ url("dashboard/dashboard/cashflow") }}',
                type: 'get',
                dataType: 'html',
                data: 'period=' + period + '&start=' + picker.startDate.format('YYYY-MM-DD') + '&end=' + picker.endDate.format('YYYY-MM-DD'),
                success: function(data) {
                    $('#cashflow').html(data);
                }
            });
        });

        $('#cashflow-monthly').on('click', function() {
            var picker = $('#cashflow-range').data('daterangepicker');

            $('#period').val('month');

            $.ajax({
                url: '{{ url("dashboard/dashboard/cashflow") }}',
                type: 'get',
                dataType: 'html',
                data: 'period=month&start=' + picker.startDate.format('YYYY-MM-DD') + '&end=' + picker.endDate.format('YYYY-MM-DD'),
                success: function(data) {
                    $('#cashflow').html(data);
                }
            });
        });

        $('#cashflow-quarterly').on('click', function() {
            var picker = $('#cashflow-range').data('daterangepicker');

            $('#period').val('quarter');

            $.ajax({
                url: '{{ url("dashboard/dashboard/cashflow") }}',
                type: 'get',
                dataType: 'html',
                data: 'period=quarter&start=' + picker.startDate.format('YYYY-MM-DD') + '&end=' + picker.endDate.format('YYYY-MM-DD'),
                success: function(data) {
                    $('#cashflow').html(data);
                }
            });
        });
    });
</script>
@endpush