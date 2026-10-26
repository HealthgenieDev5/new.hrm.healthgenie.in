{{-- Layout 4: horizontal strips — labels row with values below, info strip then summary strip --}}
<table style="width:100%; border:1px solid #000;">
    <tbody>
        <tr>
            @foreach ($info as $label => $value)
                <td style="text-align: center; font-size:10px; background:#f0f0f0; border:1px solid #000;">{{ $label }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach ($info as $label => $value)
                <th style="text-align: center; border:1px solid #000;">{{ $value }}</th>
            @endforeach
        </tr>
    </tbody>
</table>
<div style="height:6px;"></div>
<table style="width:100%; border:1px solid #000;">
    <tbody>
        <tr>
            @foreach ($stats as $label => $value)
                <td style="text-align: center; font-size:10px; background:#f0f0f0; border:1px solid #000;">{{ $label }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach ($stats as $label => $value)
                <th style="text-align: center; border:1px solid #000;">{{ $value }}</th>
            @endforeach
        </tr>
    </tbody>
</table>
