{{-- Layout 8: framed "Label .... : Value" lists, info and summary side by side, no inner borders --}}
<table style="width:100%; border:1px solid #000;">
    <tbody>
        <tr>
            <td style="width: 55%; vertical-align: top; padding:4px 6px;">
                <table style="width:100%;">
                    <tbody>
                        @foreach ($info as $label => $value)
                            <tr>
                                <td style="width:32%; text-align: left; padding:2px 0; border-bottom:1px dotted #777;">{{ $label }}</td>
                                <td style="width:4%; text-align: center; padding:2px 0;">:</td>
                                <th style="text-align: left; padding:2px 0;">{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; padding:4px 6px; border-left:1px solid #000;">
                <table style="width:100%;">
                    <tbody>
                        @foreach ($stats as $label => $value)
                            <tr>
                                <td style="width:66%; text-align: left; padding:2px 0; border-bottom:1px dotted #777;">{{ $label }}</td>
                                <td style="width:6%; text-align: center; padding:2px 0;">:</td>
                                <th style="text-align: left; padding:2px 0;">{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
