{{-- Layout 5: borderless columns with thin row rules, small uppercase labels, bold right-aligned values --}}
<table style="width:100%;">
    <tbody>
        <tr>
            <td style="width: 52%; vertical-align: top; padding:0px;">
                <table style="width:100%;">
                    <tbody>
                        @foreach ($info as $label => $value)
                            <tr>
                                <td style="width:34%; text-align: left; font-size:9px; text-transform: uppercase; letter-spacing:0.5px; color:#555; border-bottom:0.5px solid #999;">{{ $label }}</td>
                                <th style="text-align: right; border-bottom:0.5px solid #999;">{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 8%; padding:0px;"></td>
            <td style="width: 40%; vertical-align: top; padding:0px;">
                <table style="width:100%;">
                    <tbody>
                        @foreach ($stats as $label => $value)
                            <tr>
                                <td style="width:72%; text-align: left; font-size:9px; text-transform: uppercase; letter-spacing:0.5px; color:#555; border-bottom:0.5px solid #999;">{{ $label }}</td>
                                <th style="text-align: right; border-bottom:0.5px solid #999;">{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
