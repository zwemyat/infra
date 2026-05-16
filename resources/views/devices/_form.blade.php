@csrf
<div class="card mb-3">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <i class="bi bi-tag text-primary"></i><strong>Identification</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $device->item_name ?? '') }}" class="form-control @error('item_name') is-invalid @enderror" @aria('item_name') placeholder="e.g. Logitech MX Master 3" required>
                @error('item_name')<div id="item_name-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="serial_number" class="form-label">Serial Number</label>
                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $device->serial_number ?? '') }}" class="form-control @error('serial_number') is-invalid @enderror" @aria('serial_number') placeholder="e.g. SN-12345">
                @error('serial_number')<div id="serial_number-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="qty" class="form-label">Qty <span class="text-danger">*</span></label>
                <input type="number" min="1" step="1" name="qty" id="qty" value="{{ old('qty', $device->qty ?? 1) }}" class="form-control @error('qty') is-invalid @enderror" @aria('qty') required>
                @error('qty')<div id="qty-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" @aria('status') required>
                    @foreach(\App\Models\Device::STATUSES as $s)
                        <option value="{{ $s }}" @selected(old('status', $device->status ?? 'Free') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                @error('status')<div id="status-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" @aria('description') rows="2" placeholder="Brief description of the device or its purpose">{{ old('description', $device->description ?? '') }}</textarea>
                @error('description')<div id="description-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <i class="bi bi-truck text-primary"></i><strong>Logistics</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" value="{{ old('location', $device->location ?? '') }}" class="form-control @error('location') is-invalid @enderror" @aria('location') placeholder="e.g. Server Room A">
                @error('location')<div id="location-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="delivery_date" class="form-label">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', isset($device->delivery_date) ? $device->delivery_date->format('Y-m-d') : '') }}" class="form-control @error('delivery_date') is-invalid @enderror" @aria('delivery_date')>
                @error('delivery_date')<div id="delivery_date-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="delivery_location" class="form-label">Delivery Location</label>
                <input type="text" name="delivery_location" id="delivery_location" value="{{ old('delivery_location', $device->delivery_location ?? '') }}" class="form-control @error('delivery_location') is-invalid @enderror" @aria('delivery_location')>
                @error('delivery_location')<div id="delivery_location-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <i class="bi bi-calendar-check text-primary"></i><strong>Lifecycle</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="vendor" class="form-label">Vendor</label>
                <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $device->vendor ?? '') }}" class="form-control @error('vendor') is-invalid @enderror" @aria('vendor')>
                @error('vendor')<div id="vendor-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="purchased_date" class="form-label">Purchased Date</label>
                <input type="date" name="purchased_date" id="purchased_date" value="{{ old('purchased_date', isset($device->purchased_date) ? $device->purchased_date->format('Y-m-d') : '') }}" class="form-control @error('purchased_date') is-invalid @enderror" @aria('purchased_date')>
                @error('purchased_date')<div id="purchased_date-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="warranty" class="form-label">Warranty</label>
                <input type="text" name="warranty" id="warranty" value="{{ old('warranty', $device->warranty ?? '') }}" class="form-control @error('warranty') is-invalid @enderror" @aria('warranty') placeholder="e.g. 2 years">
                @error('warranty')<div id="warranty-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="remark" class="form-label">Remark</label>
                <textarea name="remark" id="remark" class="form-control @error('remark') is-invalid @enderror" @aria('remark') rows="3">{{ old('remark', $device->remark ?? '') }}</textarea>
                @error('remark')<div id="remark-error" class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-check2"></i> Save</button>
    <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
