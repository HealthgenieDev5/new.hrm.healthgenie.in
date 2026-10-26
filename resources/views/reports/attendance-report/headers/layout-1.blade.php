{{-- Layout 1 (default): info table left, summary table right --}}
<table style="width:100%;">
    <tbody>
        <tr>
            <td style="width: 40%; vertical-align: top; padding:0px;">
                <table style="max-width: max-content; margin-right: auto;" border="1">
                    <tbody>
                        @foreach ($info as $label => $value)
                            <tr>
                                <td style="text-align: left">{{ $label }}</td>
                                <th style="text-align: left">{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 20%; padding:0px;"></td>
            <td style="width: 40%; vertical-align: top; padding:0px;">
                <table style="max-width: max-content; text-align: right; margin-left: auto;" border="1">
                    <tbody>
                        @foreach ($stats as $label => $value)
                            <tr>
                                <td>{{ $label }}</td>
                                <th>{{ $value }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
