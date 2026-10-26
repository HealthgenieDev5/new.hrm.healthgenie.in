{{-- Layout 2: worker details in one horizontal accent-barred strip, summary split into two zebra-striped columns --}}
@php
    $statColumns = array_chunk($stats, (int) ceil(count($stats) / 2), true);
    $infoWidths = [24, 20, 15, 16, 25];
@endphp
<table style="width:100%; border-left:5px solid #333; border-top:1px solid #333; border-right:1px solid #333; border-bottom:1px solid #333;">
    <tbody>
        <tr>
            @foreach ($info as $label => $value)
                <td style="width:{{ $infoWidths[$loop->index] ?? 20 }}%; text-align: left; padding:4px 8px 1px 8px; font-size:10px; color:#555;">{{ $label }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach ($info as $label => $value)
                <th style="text-align: left; padding:1px 8px 5px 8px;">{{ $value }}</th>
            @endforeach
        </tr>
    </tbody>
</table>
<div style="height:6px;"></div>
<table style="width:100%;">
    <tbody>
        <tr>
            @foreach ($statColumns as $column)
                <td style="width: 48%; vertical-align: top; padding:0px;">
                    <table style="width:100%; border:1px solid #333;">
                        <tbody>
                            @foreach ($column as $label => $value)
                                <tr style="{{ $loop->odd ? 'background:#efefef;' : '' }}">
                                    <td style="width:72%; text-align: left; padding:4px 8px;">{{ $label }}</td>
                                    <th style="text-align: right; padding:4px 10px; border-left:1px solid #bbb;">{{ $value }}</th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                @if (!$loop->last)
                    <td style="width: 4%; padding:0px;"></td>
                @endif
            @endforeach
        </tr>
    </tbody>
</table>
