<div class="">
    <form action="{{ route('orders.index') }}" method="GET">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="order_code">Order Code</label>
                <input type="text" value="{{ request('order_code') }}" class="form-control form-control-sm"
                    id="order_code" name="order_code" placeholder="Search Order Code">
            </div>
            <div class="col-md-6 mb-3">
                <label for="table_slug">Table</label>
                <select class="form-control form-control-sm" id="table_slug" name="table_slug">
                    <option value="">Choose Table</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->slug }}"
                            {{ request('table_slug') == $table->slug ? 'selected' : '' }}>{{ $table->slug }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="order_type">Order Type</label>
                <select class="form-control form-control-sm" id="order_type" name="order_type">
                    <option value="">Order Type</option>
                    <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                    <option value="take_away" {{ request('order_type') == 'take_away' ? 'selected' : '' }}>Take Away
                    </option>
                </select>
            </div>
            <input type="hidden" name="status" value="{{ request('status') }}">
            {{-- @if (!request('status') || request('status') == 'all')
                <div class="col-md-6 mb-3">
                    <label for="status">Order Status</label>
                    <select class="form-control form-control-sm" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing
                        </option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>
            @endif --}}
            {{-- @if (!request('status') || request('status') == 'all')
                <div class="col-md-6 mb-3">
                    <label for="date_from">From</label>
                    <input type="date" value="{{ request('date_from') }}"
                        class="form-control form-control-sm date-picker" placeholder="choose date" id="date_from"
                        name="date_from">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="date_to">To</label>
                    <input type="date" value="{{ request('date_to') }}"
                        class="form-control form-control-sm date-picker" placeholder="choose date" id="date_to"
                        name="date_to">
                </div>
            @endif --}}
        </div>
        <div class="mt-3">
            <a href="{{ route('orders.index') }}" class="btn btn-danger text-white">Reset</a>
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
</div>
