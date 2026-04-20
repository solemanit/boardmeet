{{-- views/agendas/create.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Create Agenda')
@section('title', 'Create New Agenda')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Create Agenda</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agendas.index') }}">Agendas</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back
        </a>
    </div>

    <form action="{{ route('agendas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-6">

            {{-- Main Details --}}
            <div class="col-lg-8">
                <div class="pure-card rounded-custom card-bg shadow-custom mb-5">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Agenda Details</h3>
                    </div>
                    <div class="pure-card-body">

                        {{-- Agenda Number (read-only) --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Agenda Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3M7 3C7 1 12 1 17 1 17 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control bg-light fw-semibold text-primary"
                                    value="{{ $agendaNumber }}" readonly>
                            </div>
                            <small class="text-custom-paragraph mt-1 d-block">Auto-generated — cannot be changed</small>
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <label for="title" class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}"
                                placeholder="Enter agenda title...">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Detailed description of the agenda item...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Priority & Category --}}
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
                                <select id="priority" name="priority"
                                    class="form-select @error('priority') is-invalid @enderror">
                                    @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                                        <option value="{{ $p }}" @selected(old('priority', 'medium') === $p)>
                                            {{ ucfirst($p) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-medium">Category</label>
                                <select id="category" name="category"
                                    class="form-select @error('category') is-invalid @enderror">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected(old('category') === $cat)>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Assigned To & Meeting Date --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label fw-medium">Assign To</label>
                                <select id="assigned_to" name="assigned_to"
                                    class="form-select @error('assigned_to') is-invalid @enderror">
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                            {{ $user->name }} ({{ $user->role?->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="meeting_date" class="form-label fw-medium">Meeting Date</label>
                                <input type="date" id="meeting_date" name="meeting_date"
                                    class="form-control @error('meeting_date') is-invalid @enderror"
                                    value="{{ old('meeting_date') }}">
                                @error('meeting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documents Card --}}
                <div class="pure-card rounded-custom card-bg shadow-custom">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Documents</h3>
                    </div>
                    <div class="pure-card-body">

                        {{-- Main Document --}}
                        <div class="mb-5">
                            <label class="form-label fw-medium d-flex align-items-center gap-2">
                                <span class="badge badge-label-primary">Main</span>
                                Agenda Document (PDF/DOC)
                            </label>
                            <div class="upload-zone border rounded-2 p-4 text-center" id="mainDropZone">
                                <input type="file" id="main_document" name="main_document"
                                    class="d-none @error('main_document') is-invalid @enderror">
                                <label for="main_document" class="cursor-pointer d-block">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted mb-2">
                                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 2V8H20M12 18V12M9 15L12 12L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-1 fw-medium">Click to upload or drag and drop</p>
                                    <small class="text-custom-paragraph">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max 10MB)</small>
                                </label>
                                <div id="mainFilePreview" class="mt-3 d-none">
                                    <div class="d-flex align-items-center justify-content-center gap-2 text-success">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span id="mainFileName" class="fw-medium small"></span>
                                    </div>
                                </div>
                            </div>
                            @error('main_document')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Supporting Documents --}}
                        <div>
                            <label class="form-label fw-medium d-flex align-items-center gap-2">
                                <span class="badge badge-label-info">Supporting</span>
                                Supporting Documents
                            </label>
                            <div class="upload-zone border rounded-2 p-4 text-center">
                                <input type="file" id="supporting_docs" name="supporting_docs[]"
                                    class="d-none" multiple>
                                <label for="supporting_docs" class="cursor-pointer d-block">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted mb-2">
                                        <path d="M21.44 11.05L12.25 20.24C11.1242 21.3658 9.59723 21.9983 8.005 21.9983C6.41277 21.9983 4.88584 21.3658 3.76 20.24C2.63416 19.1142 2.00166 17.5872 2.00166 15.995C2.00166 14.4028 2.63416 12.8758 3.76 11.75L12.95 2.56C13.7006 1.80944 14.7185 1.38778 15.78 1.38778C16.8415 1.38778 17.8594 1.80944 18.61 2.56C19.3606 3.31056 19.7822 4.32855 19.7822 5.39C19.7822 6.45145 19.3606 7.46944 18.61 8.22L9.41 17.41C9.03494 17.7851 8.52606 17.9958 7.995 17.9958C7.46394 17.9958 6.95506 17.7851 6.58 17.41C6.20494 17.0349 5.99421 16.5261 5.99421 15.995C5.99421 15.4639 6.20494 14.9551 6.58 14.58L15.07 6.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-1 fw-medium">Click to upload multiple files</p>
                                    <small class="text-custom-paragraph">PDF, DOC, DOCX, XLS, XLSX (Max 10MB each)</small>
                                </label>
                                <div id="supportingFilesPreview" class="mt-3"></div>
                            </div>
                            @error('supporting_docs.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="pure-card rounded-custom card-bg shadow-custom sticky-top" style="top: 80px;">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title m-0">Workflow</h3>
                    </div>
                    <div class="pure-card-body">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start gap-3 p-3 rounded-2 bg-light">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:24px;height:24px;font-size:11px;font-weight:700;">1</span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium small">Create Draft</p>
                                    <small class="text-custom-paragraph">Fill in details & upload documents</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3 p-3 rounded-2 bg-light">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:24px;height:24px;font-size:11px;font-weight:700;">2</span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium small">Submit for Review</p>
                                    <small class="text-custom-paragraph">Send to admin/MD for approval</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3 p-3 rounded-2 bg-light">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:24px;height:24px;font-size:11px;font-weight:700;">3</span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium small">Approval / Rejection</p>
                                    <small class="text-custom-paragraph">Approved or sent back for revision</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pure-card-footer d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Save as Draft
                        </button>
                        <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>

        </div>
    </form>

    @push('scripts')
    <script>
        // Main document preview
        document.getElementById('main_document').addEventListener('change', function () {
            const preview = document.getElementById('mainFilePreview');
            const nameEl  = document.getElementById('mainFileName');
            if (this.files.length) {
                nameEl.textContent = this.files[0].name;
                preview.classList.remove('d-none');
            }
        });

        // Supporting documents preview
        document.getElementById('supporting_docs').addEventListener('change', function () {
            const preview = document.getElementById('supportingFilesPreview');
            preview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center gap-2 text-success small mt-1';
                div.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> ${file.name}`;
                preview.appendChild(div);
            });
        });

        // Drag & drop for main document
        const dropZone = document.getElementById('mainDropZone');
        ['dragover', 'dragenter'].forEach(e => dropZone.addEventListener(e, (ev) => {
            ev.preventDefault();
            dropZone.classList.add('border-primary');
        }));
        ['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, () => {
            dropZone.classList.remove('border-primary');
        }));
        dropZone.addEventListener('drop', (ev) => {
            ev.preventDefault();
            const input = document.getElementById('main_document');
            input.files = ev.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        });
    </script>
    @endpush
@endsection
