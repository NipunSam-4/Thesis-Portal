@if(!empty($actionUrl))
    <div style="text-align: center; margin: 30px 0 20px 0;">
        <!--[if mso]>
        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $actionUrl }}" style="height:44px;v-text-anchor:middle;width:240px;" arcsize="14%" stroke="f" fillcolor="#2563eb">
        <w:anchorlock/>
        <center style="color:#ffffff;font-family:sans-serif;font-size:14px;font-weight:bold;">{{ $actionText ?? 'View Details' }}</center>
        </v:roundrect>
        <![endif]-->
        <a href="{{ $actionUrl }}" style="background-color: #2563eb; color: #ffffff !important; display: inline-block; font-weight: 600; font-size: 14px; line-height: 44px; text-align: center; text-decoration: none; width: auto; min-width: 200px; padding: 0 24px; border-radius: 6px; -webkit-text-size-adjust: none; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
            {{ $actionText ?? 'View Details' }} &rarr;
        </a>
    </div>
@endif
