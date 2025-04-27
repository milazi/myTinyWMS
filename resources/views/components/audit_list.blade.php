<table class="dataTable table-condensed multiRow text-sm">
    <thead>
        <tr>
            <th>@lang('when') / @lang('who')</th>
            <th>@lang('what')</th>
            <th>@lang('old')</th>
            <th>@lang('new')</th>
        </tr>
    </thead>

    @foreach($audits as $audit)
        <tbody>
            @foreach($audit['modified'] as $data)
                @if(!is_array($data['new']) && (!array_key_exists('old', $data) || !is_array($data['old'])))
                <tr>
                    @if($loop->first)
                    <td rowspan="{{ count($audit['modified']) }}" class="whitespace-no-wrap">
                        {{ $audit['timestamp']->format('d.m.Y H:i') }}
                        <br>
                        <span class="text-xs">{{ $audit['user'] }}</span>
                    </td>
                    @endif

                    <td class="whitespace-no-wrap">
                        {{ $data['name'] }}<br>
                        <span class="text-xs">in {{ $audit['name'] }}</span>
                    </td>
                    <td>{!! (array_key_exists('old', $data) && !empty($data['old'])) ? str_limit($data['old'], 50, ' (...)') : '' !!}</td>
                    <td>{!! !empty($data['new']) ? str_limit($data['new'], 50, ' (...)') : '' !!}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    @endforeach
</table>