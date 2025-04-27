@extends('layout.app')

@section('title', __('Change History Article ').((!empty($article->internal_article_number)) ? ' #'.$article->internal_article_number : ''))

@section('title_extra')
    <small>{{ $article->name }}</small>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Overview')</a>
    </li>
    <li>
        <a href="{{ route('article.show', $article) }}">@lang('Article Details')</a>
    </li>
    <li class="active">
        <strong>@lang('Change History')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="w-1/2 mr-4">
            <canvas id="chart"></canvas>
        </div>
        <div class="w-1/2">
            <div class="card">
                <div class="card-header flex" style="min-height: 55px">
                    <h5 class="flex-1">@lang('Change History')</h5>
                    <div>
                        <div id="daterange" class="pull-right bg-white cursor-pointer px-4 py-2 border border-gray-300 w-full">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('Type')</th>
                                <th class="text-center">@lang('Change')</th>
                                <th class="text-center">@lang('Stock')</th>
                                <th class="text-center">@lang('Unit')</th>
                                <th>@lang('Timestamp')</th>
                                <th>@lang('Comment')</th>
                                <th>@lang('User')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changelog as $log)
                                <tr>
                                    @if ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_INCOMING)
                                        @include('components.quantity_log.incoming')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING)
                                        @include('components.quantity_log.outgoing')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_CORRECTION)
                                        @include('components.quantity_log.correction')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_COMMENT)
                                        @include('components.quantity_log.comment')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_INVENTORY)
                                        @include('components.quantity_log.inventory')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY)
                                        @include('components.quantity_log.replacement_delivery')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTSOURCING)
                                        @include('components.quantity_log.outsourcing')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES)
                                        @include('components.quantity_log.sale_to_third_parties')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_TRANSFER)
                                        @include('components.quantity_log.transfer')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_RETOURE)
                                        @include('components.quantity_log.retoure')
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6 flex justify-end">
                        {{ $changelog->appends(['start' => $dateStart->format('Y-m-d'), 'end' => $dateEnd->format('Y-m-d')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        var chartData = {
            labels: {!! $chartLabels->toJson() !!},
            datasets: [
                @if(isset($chartValues[1]))
                {
                    type: 'bar',
                    backgroundColor: '#449D44',
                    'label': '@lang('Goods receipt') (Ø {{ $dataDiffInMonths ? round(abs($chartValues[1]->sum() / $dataDiffInMonths), 0) : $chartValues[1]->sum() }} / @lang('Month'))',
                    data: {!! $chartValues[1]->toJson() !!}
                },
                @endif
                @if(isset($chartValues[2]))
                {
                    type: 'bar',
                    backgroundColor: '#ED5565',
                    'label': '@lang('Goods issue') (Ø {{ $dataDiffInMonths ? round(abs($chartValues[2]->sum() / $dataDiffInMonths), 0) : $chartValues[2]->sum() }} / @lang('Month'))',
                    data: {!! $chartValues[2]->toJson() !!}
                }
                @endif
            ]
        };

        $(function() {
            var ctx = document.getElementById("chart").getContext("2d");
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'point',
                        // intersect: true
                    }
                }
            });

            var start = moment('{{ $dateStart->format('Y-m-d') }}');
            var end = moment('{{ $dateEnd->format('Y-m-d') }}');

            function cb(start, end) {
                $('#daterange span').html(start.format('MM.DD.YYYY') + ' - ' + end.format('MM.DD.YYYY'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                opens: 'left',
                ranges: {
                    '@lang('Last 30 Days')': [moment().subtract(29, 'days'), moment()],
                    '@lang('This Month')': [moment().startOf('month'), moment().endOf('month')],
                    '@lang('Last Month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '@lang('Current Year')': [moment().startOf('year'), moment()],
                    '@lang('12 Months')': [moment().subtract(12, 'month').startOf('month'), moment()],
                    '@lang('24 Months')': [moment().subtract(24, 'month').startOf('month'), moment()],
                    '@lang('36 Months')': [moment().subtract(36, 'month').startOf('month'), moment()]
                },
                "locale": {
                    "format": "MM.DD.YYYY",
                    "separator": " - ",
                    "applyLabel": "@lang('Apply')",
                    "cancelLabel": "@lang('Cancel')",
                    "fromLabel": "@lang('From')",
                    "toLabel": "@lang('To')",
                    "customRangeLabel": "@lang('Custom')",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "@lang('Su')",
                        "@lang('Mo')",
                        "@lang('Tu')",
                        "@lang('We')",
                        "@lang('Th')",
                        "@lang('Fr')",
                        "@lang('Sa')"
                    ],
                    "monthNames": [
                        "@lang('January')",
                        "@lang('February')",
                        "@lang('March')",
                        "@lang('April')",
                        "@lang('May')",
                        "@lang('June')",
                        "@lang('July')",
                        "@lang('August')",
                        "@lang('September')",
                        "@lang('October')",
                        "@lang('November')",
                        "@lang('December')"
                    ],
                    "firstDay": 1
                }
            }, cb);

            cb(start, end);

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                window.location.href = '{{ route('article.quantity_changelog', $article) }}?start=' + picker.startDate.format('YYYY-MM-DD') + '&end=' + picker.endDate.format('YYYY-MM-DD');
            });

        });
    </script>
@endpush