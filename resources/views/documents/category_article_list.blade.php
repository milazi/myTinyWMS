<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            }

            .break-before {
                page-break-before: always;
            }

            .break-after {
                page-break-after: always;
            }

            h1 {
                font-size: 30px;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            .table {
                border-collapse: collapse;
                background: #000;
                width: 100%;
                page-break-before: avoid !important;
            }

            td, th {
                border: 1px solid black;
                background: #fff;
                text-align: left;
                padding: 5px;
            }

            tr, td, th, thead, tfoot {
                page-break-inside: avoid !important;
            }
        </style>
    </head>

    <body class="white-bg">
        @foreach($categories as $category)
        <h1>{{ $category->name }}</h1>
        <table class="table @if (!$loop->last) break-after @endif" cellpadding="0" cellspacing="1">
            <thead>
                <tr>
                    <th style="width: 25px">@lang('Number')</th>
                    <th style="width: 200px">@lang('Name')</th>
                    <th style="width: 100px">@lang('Withdrawal')</th>
                    <th>@lang('Issuance')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category->articles as $article)
                <tr>
                    <td style="vertical-align: top">{{ $article->internal_article_number }}</td>
                    <td style="vertical-align: top">{{ $article->name }}</td>
                    <td style="vertical-align: top">{{ $article->issue_quantity }} {{ optional($article->unit)->name }}</td>
                    <td>
                        @for($i = 0; $i<($article->free_lines_in_printed_list ?? 1); $i++)
                            <br>
                        @endfor
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </body>
</html>