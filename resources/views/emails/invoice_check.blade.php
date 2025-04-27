<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            <p>@lang('In the order') <a href="{{ route('order.show', $order) }}">{{ $order->internal_order_number }}</a> @lang('an invoice requires checking.')</p>

            <p>@lang('The following message was left:')</p>

            <p style="border: 1px solid black; padding: 10px; background-color: lightgray; color: black">
                {{ $note }}
            </p>

            @if(count($mail_attachments))
            <p>
                @lang('see') {{ trans_choice('plural.attachment', count($mail_attachments)) }}!
            </p>
            @endif
        </td>
    </tr>
</table>