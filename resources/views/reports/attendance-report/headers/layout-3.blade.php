{{-- Layout 3: single full-width grid, info pairs left and summary pairs right, shaded label cells --}}
@php
    $infoRows = array_map(null, array_keys($info), array_values($info));
    $statRows = array_map(null, array_keys($stats), array_values($stats));
    $rowCount = max(count($infoRows), count($statRows));
@endphp
<table style="width:100%;" border="1">
    <tbody>
        @for ($i = 0; $i < $rowCount; $i++)
            <tr>
                <td style="width:17%; text-align: left; {{ isset($infoRows[$i]) ? 'background:#e6e6e6;' : '' }}">{{ $infoRows[$i][0] ?? '' }}</td>
                <th style="width:35%; text-align: left;">{{ $infoRows[$i][1] ?? '' }}</th>
                <td style="width:26%; text-align: left; {{ isset($statRows[$i]) ? 'background:#e6e6e6;' : '' }}">{{ $statRows[$i][0] ?? '' }}</td>
                <th style="width:22%; text-align: center;">{{ $statRows[$i][1] ?? '' }}</th>
            </tr>
        @endfor
    </tbody>
</table>
