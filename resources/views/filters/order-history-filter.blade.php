<div class="mb-3">
    <form action="{{ route('orders.history') }}" method="GET">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="start_date">Start Date</label>
                <input type="date" value="{{ request('start_date') }}" class="form-control form-control-sm"
                    id="start_date" name="start_date">
            </div>
            <div class="col-md-4 mb-3">
                <label for="end_date">End Date</label>
                <input type="date" value="{{ request('end_date') }}" class="form-control form-control-sm"
                    id="end_date" name="end_date">
            </div>
            <div class="col-md-4">
                <div class="mt-3">
                <a href="{{ route('orders.history') }}" class="btn btn-danger text-white">Reset</a>
                <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>
    </form>
</div>

