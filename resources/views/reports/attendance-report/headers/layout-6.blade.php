{{-- Layout 6: two stacked full-width panels, double outer border with heavy top rule, two pairs per row --}}
@foreach ([$info, $stats] as $panel)
    <table style="width:100%; border:3px double #000; border-top:3px solid #000;">
        <tbody>
            @foreach (array_chunk($panel, 2, true) as $pairs)
                <tr>
                    @foreach ($pairs as $label => $value)
                        <td style="width:22%; text-align: left;">{{ $label }}</td>
                        <th style="width:28%; text-align: left;">{{ $value }}</th>
                    @endforeach
                    @if (count($pairs) < 2)
                        <td style="width:22%;"></td>
                        <th style="width:28%;"></th>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($loop->first)
        <div style="height:5px;"></div>
    @endif
@endforeach
