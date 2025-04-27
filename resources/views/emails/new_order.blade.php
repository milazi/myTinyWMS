<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            <p>@lang('Sehr geehrte Damen und Herren,')</p>

            <p>@lang('hiermit bestellen wir folgende Artikel:')</p>
        </td>
    </tr>
    <tr>
        <td align="center">
            <table width="94%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #EEEEEE">
                <tr>
                    <td width="5%" align="left" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-right:0;">
                        <b>#</b>
                    </td>
                    <td width="40%" align="left" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-right:0;">
                        <b>@lang('Article')</b>
                    </td>
                    <td width="20%" align="center" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Order Number')</b>
                    </td>
                    <td width="10%" align="center" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Quantity')</b>
                    </td>
                    <td width="10%" align="center" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Unit')</b>
                    </td>
                    <td width="15%" align="center" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Unit Price Net')</b>
                    </td>
                    @if($order->items->pluck('expected_delivery')->hasNonEmpty())
                    <td width="5%" align="center" bgcolor="#aaaaaaa" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Delivery by')</b>
                    </td>
                    @endif
                </tr>
                @foreach($order->items as $key => $item)
                <tr>
                    <td width="5%" align="left" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-right:0;">
                        {{ $key+1 }}
                    </td>
                    <td width="40%" align="left" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-right:0;">
                        {{ $item->article->name }}
                    </td>
                    <td width="20%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ $item->article->currentSupplierArticle->order_number }}
                    </td>
                    <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ $item->quantity }}
                    </td>
                    <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ optional($item->article->unit)->name }}
                    </td>
                    <td width="15%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {!! formatPrice($item->price) !!}
                    </td>
                    @if($order->items->pluck('expected_delivery')->hasNonEmpty())
                    <td width="15%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ !empty($item->expected_delivery) ? $item->expected_delivery->format('d.m.Y') : '' }}
                    </td>
                    @endif
                </tr>
                @endforeach
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            @lang('Please confirm the order with our order number') "{{ $order->internal_order_number }}".
            <br/><br/><br/>
            {!! html_entity_decode(Auth::user()->signature) !!}
        </td>
    </tr>
</table>