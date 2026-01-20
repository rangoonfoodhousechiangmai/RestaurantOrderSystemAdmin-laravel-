@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Order Details - {{ $order->order_code }}</h4>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <strong>Table:</strong> {{ $order->table->slug ?? 'N/A' }}
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <strong>Order Type:</strong> {{ ucfirst($order->order_type) }}
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'preparing' ? 'info' : ($order->status == 'delivered' ? 'primary' : ($order->status == 'completed' ? 'success' : 'danger'))) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <strong>Total Price:</strong> <span id="total-price">{{ number_format($order->total_price, 2) }}</span> THB
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" style="background-color: #f8f9fa;">
                        <thead>
                            <tr>
                                <th>Menu Item</th>
                                <th>Quantity</th>
                                <th>Modifiers</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderItems as $item)
                            <tr data-item-id="{{ $item->id }}">
                                <td>{{ $item->menu->eng_name }}</td>
                                <td>
                                    <input type="number" class="form-control quantity-input" value="{{ $item->quantity }}" min="1" style="width: 80px;">
                                </td>
                                <td>
                                    @php
                                        $menuModifiers = $item->menu->modifiers ?? collect();
                                        $proteinModifiers = $menuModifiers->where('type', 'protein');
                                        $flavorModifiers = $menuModifiers->where('type', 'flavor');
                                        $addonModifiers = $menuModifiers->where('type', 'addon');
                                    @endphp

                                    @if($proteinModifiers->isNotEmpty())
                                    <div class="mb-2">
                                        <div class="mb-2"><strong>Protein:</strong></div>
                                        <select class="form-control protein-select" style="width: 100%;">
                                            <option value="" disabled>Select Protein</option>
                                            @foreach($proteinModifiers as $modifier)
                                            <option value="{{ $modifier->id }}" {{ $item->orderItemModifiers->where('modifier_id', $modifier->id)->isNotEmpty() ? 'selected' : '' }}>
                                                {{ $modifier->name }} (+{{ number_format($modifier->price, 2) }} THB)
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    @if($flavorModifiers->isNotEmpty())
                                    <div class="mb-2">
                                        <div class="mb-2"><strong>Flavor:</strong></div>
                                        <select class="form-control flavor-select" style="width: 100%;">
                                            <option value="">Select Flavor</option>
                                            @foreach($flavorModifiers as $modifier)
                                            <option value="{{ $modifier->id }}" {{ $item->orderItemModifiers->where('modifier_id', $modifier->id)->isNotEmpty() ? 'selected' : '' }}>
                                                {{ $modifier->name }} (+{{ number_format($modifier->price, 2) }} THB)
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    @if($addonModifiers->isNotEmpty())
                                    <div>
                                        <strong>Add-ons:</strong>
                                        @foreach($addonModifiers as $modifier)
                                        <div class="form-check">
                                            <input class="form-check-input addon-checkbox" type="checkbox" value="{{ $modifier->id }}" id="addon-{{ $modifier->id }}-{{ $item->id }}" {{ $item->orderItemModifiers->where('modifier_id', $modifier->id)->isNotEmpty() ? 'checked' : '' }}>
                                            <label class="form-check-label" for="addon-{{ $modifier->id }}-{{ $item->id }}">
                                                {{ $modifier->name }} (+{{ number_format($modifier->price, 2) }} THB)
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </td>
                                <td>{{ number_format($item->price, 2) }} THB</td>
                                <td class="item-total">{{ number_format($item->total_price, 2) }} THB</td>
                                <td>
                                    <button class="btn btn-primary btn-sm update-item" data-item-id="{{ $item->id }}">Update</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No order items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.update-item').on('click', function() {
        var itemId = $(this).data('item-id');
        var row = $('tr[data-item-id="' + itemId + '"]');
        var quantity = row.find('.quantity-input').val();
        var proteinId = row.find('.protein-select').val();
        var flavorId = row.find('.flavor-select').val();
        var addonIds = [];
        row.find('.addon-checkbox:checked').each(function() {
            addonIds.push($(this).val());
        });

        var url = "{{ route('orders.update', ['order' => $order->id, 'itemId' => ':itemId']) }}".replace(':itemId', itemId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                quantity: quantity,
                protein_id: proteinId,
                flavor_id: flavorId,
                addon_ids: addonIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update the item total and overall total
                row.find('.item-total').text(response.item_total + ' THB');
                $('#total-price').text(response.order_total);
                alert('Item updated successfully!');
            },
            error: function(xhr) {
                alert('Error updating item: ' + xhr.responseJSON.error);
            }
        });
    });
});
</script>
@endsection
