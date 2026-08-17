@if(!empty($details) && is_array($details))
    <div style="background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 0 8px 8px 0; padding: 18px 20px; margin: 24px 0; border-top: 1px solid #edf2f7; border-right: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #334155;">
            @foreach($details as $key => $val)
                <tr>
                    <td style="padding: 6px 0; font-weight: 600; color: #475569; width: 38%; vertical-align: top;">{{ $key }}:</td>
                    <td style="padding: 6px 0 6px 12px; color: #0f172a; vertical-align: top;">{{ $val }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endif
