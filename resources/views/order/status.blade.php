@if ($status == \Mss\Models\Order::STATUS_NEW)
    <span class="text-gray-600 font-semibold">@lang('new')</span>
@elseif ($status == \Mss\Models\Order::STATUS_ORDERED)
    <span class="w-3 h-3 inline-block align-middle rounded-full bg-indigo-500 mr-1"></span>
    <span class="text-indigo-600 font-semibold align-top">@lang('ordered')</span>
@elseif($status == \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED)
    <span class="w-3 h-3 inline-block align-middle rounded-full bg-orange-500 mr-1"></span>
    <span class="text-orange-600 font-semibold align-top">@lang('partially delivered')</span>
@elseif($status == \Mss\Models\Order::STATUS_DELIVERED)
    <span class="w-3 h-3 inline-block align-middle rounded-full bg-purple-500 mr-1"></span>
    <span class="text-purple-600 font-semibold align-top">@lang('delivered')</span>
@elseif($status == \Mss\Models\Order::STATUS_CANCELLED)
    <span class="w-3 h-3 inline-block align-middle rounded-full bg-gray-500 mr-1"></span>
    <span class="text-gray-600 font-semibold align-top">@lang('cancelled')</span>
@elseif($status == \Mss\Models\Order::STATUS_PAID)
    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500 mr-1"></span>
    <span class="text-green-600 font-semibold align-top">@lang('paid')</span>
@endif