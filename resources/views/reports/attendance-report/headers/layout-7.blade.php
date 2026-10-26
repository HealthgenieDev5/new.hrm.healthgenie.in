{{-- Layout 7: centred PSP title, worker details on one divided line, summary as accent-topped tiles (value above label) --}}
@php
    $titleLabel = array_key_first($info);
    $titleValue = $info[$titleLabel];
    $details = array_slice($info, 1, null, true);
    $detailWidths = [26, 18, 22, 34];
@endphp
<table style="width:100%;">
    <tbody>
        <tr>
            <td style="text-align: center; padding:0 0 3px 0;">
                <div style="font-size:9px; letter-spacing:1px; text-transform: uppercase; color:#666;">{{ $titleLabel }}</div>
                <div style="font-size:15px; font-weight:bold; margin-top:1px;">{{ $titleValue }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:0; border-top:2px solid #333; border-bottom:0.5px solid #333; height:2px; font-size:1px;">&nbsp;</td>
        </tr>
    </tbody>
</table>
<table style="width:100%;">
    <tbody>
        <tr>
            @foreach ($details as $label => $value)
                <td style="width:{{ $detailWidths[$loop->index] ?? 25 }}%; text-align: center; padding:5px 4px; {{ $loop->last ? '' : 'border-right:1px solid #999;' }}">
                    <span style="color:#555;">{{ $label }}:</span>
                    <strong style="white-space: nowrap;">{{ $value }}</strong>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>
<div style="height:6px;"></div>
<table style="width:100%; border-collapse: separate; border-spacing:5px 0;">
    <tbody>
        <tr>
            @foreach ($stats as $label => $value)
                <td style="text-align: center; vertical-align: top; border:0.5px solid #999; border-top:3px solid #333; padding:4px 2px 5px 2px;">
                    <div style="font-size:16px; font-weight:bold;">{{ $value }}</div>
                    <div style="font-size:9px; color:#555; margin-top:1px;">{{ $label }}</div>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>
