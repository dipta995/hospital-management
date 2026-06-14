{{--
    Inner form for pharmacy master-data pages.
    Required: $pageHeader, $formTitle, $formSubtitle, $formIcon, $formAction
    Optional: $formMethod ('PUT'), $edited (model), $showDescription (bool)
--}}
<div class="crud-page pharm-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => $formTitle,
        'formSubtitle' => $formSubtitle,
        'formIcon' => $formIcon,
    ])

    <div class="crud-card">
        @include('backend.layouts.partials.message')

        <form action="{{ $formAction }}" method="POST">
            @csrf
            @if(!empty($formMethod))
                @method($formMethod)
            @endif

            <div class="crud-form-section">
                <div class="crud-form-section-header"><i class="fas fa-tag"></i> Details</div>
                <div class="crud-form-section-body">
                    <div class="row crud-form-grid g-3">
                        <div class="col-md-{{ !empty($showDescription) ? '6' : '12' }}">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $edited->name ?? '') }}" required>
                            @error('name')<small class="text-danger d-block">{{ $message }}</small>@enderror
                        </div>
                        @if(!empty($showDescription))
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $edited->description ?? '') }}</textarea>
                                @error('description')<small class="text-danger d-block">{{ $message }}</small>@enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="crud-form-actions">
                <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                <button type="submit" class="btn btn-crud-submit">{{ isset($edited) ? 'Update' : 'Save' }}</button>
            </div>
        </form>
    </div>
</div>
