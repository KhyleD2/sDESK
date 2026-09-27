@extends('layouts.app')

@section('title', 'Submit Threat Report - SentryDesk')

@section('page-context-title', 'New Report')

@section('content')
<style>
    .form-container {
        width: 100%;
    }

    /* ── Hero banner — identical to .hero-row on dashboard ── */
    .form-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        background: var(--bg-card);
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        border-left: 4px solid var(--cyan);
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .form-hero {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .form-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: var(--cyan-dim);
        border-radius: 50%;
        pointer-events: none;
    }

    .form-hero-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.4px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-hero-title i { color: var(--cyan); font-size: 20px; }

    .form-hero-subtitle {
        font-size: 14px;
        color: var(--text-dim);
        font-weight: 400;
    }

    /* ── Info banner ── */
    .info-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--cyan-dim);
        border: 1px solid rgba(52,228,214,0.2);
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 20px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--cyan);
    }

    [data-theme="light"] .info-banner {
        border-color: rgba(8,145,178,0.3);
    }

    .info-banner i { font-size: 16px; flex-shrink: 0; }

    /* ── Dashboard card — identical to .dashboard-card ── */
    .form-section-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        margin-bottom: 20px;
    }

    [data-theme="light"] .form-section-card {
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    /* ── Card header — identical to .card-header ── */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line-soft);
    }

    /* ── Card title — identical to .card-title ── */
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: var(--cyan);
        font-size: 14px;
    }

    /* ── Form group ── */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 22px;
    }

    .form-group:last-child { margin-bottom: 0; }

    /* ── Label ── */
    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.7px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .form-label i {
        color: var(--cyan);
        font-size: 11px;
        width: 14px;
        text-align: center;
    }

    .required { color: var(--rose); margin-left: 1px; }

    /* ── Inputs ── */
    .form-select,
    .form-textarea,
    .form-input {
        width: 100%;
        background: var(--bg);
        border: 1.5px solid var(--line-soft);
        border-radius: 10px;
        padding: 14px 18px;
        color: var(--text);
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-select:focus,
    .form-textarea:focus,
    .form-input:focus {
        outline: none;
        border-color: var(--cyan);
        box-shadow: 0 0 0 3px var(--cyan-dim);
    }

    .form-select option { background: var(--bg); color: var(--text); }

    .form-textarea {
        resize: vertical;
        line-height: 1.8;
        min-height: 180px;
    }

    .form-textarea::placeholder,
    .form-input::placeholder { color: var(--text-faint); }

    /* ── Hint / Error ── */
    .form-hint {
        font-size: 12px;
        color: var(--text-faint);
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin-top: 2px;
    }

    .error-message {
        color: var(--rose);
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 2px;
    }

    /* ── File upload ── */
    .file-upload-area {
        background: var(--bg);
        border: 2px dashed var(--line-soft);
        border-radius: 12px;
        padding: 36px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .file-upload-area:hover {
        border-color: var(--cyan);
        background: var(--cyan-dim);
    }

    .file-upload-icon { font-size: 38px; color: var(--text-faint); margin-bottom: 12px; }
    .file-upload-text { color: var(--cyan); font-size: 14px; font-weight: 700; margin-bottom: 6px; }
    .file-upload-hint { color: var(--text-faint); font-size: 12px; }
    .file-input-hidden { display: none; }

    .file-list { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }

    .file-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--success-dim);
        color: var(--success);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    /* ── Action bar ── */
    .form-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 8px;
        padding-top: 24px;
        border-top: 1px solid var(--line-soft);
    }

    /* Primary — matches hero-submit-btn ── */
    .btn-primary {
        background: var(--cyan);
        color: #04211E;
        border: none;
        padding: 13px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(52,228,214,0.3);
        white-space: nowrap;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #5AEEE3;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(52,228,214,0.4);
    }

    .btn-primary:active { transform: translateY(0); }

    .btn-secondary {
        background: transparent;
        color: var(--text-dim);
        border: 1.5px solid var(--line-soft);
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: var(--line-soft);
        color: var(--text);
        border-color: var(--line);
    }

    .btn-tip {
        margin-left: auto;
        font-size: 12px;
        color: var(--text-faint);
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 1;
    }
</style>

<div class="form-container">

    {{-- Hero banner --}}
    <div class="form-hero">
        <div>
            <div class="form-hero-title">
                <i class="fas fa-shield-exclamation"></i>
                Submit Threat Report
            </div>
            <div class="form-hero-subtitle">Fill in the details below to report a suspicious activity or security threat.</div>
        </div>
    </div>

    {{-- Secure channel notice --}}
    <div class="info-banner">
        <i class="fas fa-lock"></i>
        <span>Your report is submitted through a secure, encrypted channel and will be reviewed by our analyst team.</span>
    </div>

    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- 2-COLUMN GRID LAYOUT --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            
            {{-- LEFT COLUMN: Threat Information --}}
            <div class="form-section-card">
                <div class="section-header">
                    <div class="section-title"><i class="fas fa-list-alt"></i> Threat Information</div>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">
                        <i class="fas fa-tag"></i> Threat Category <span class="required">*</span>
                    </label>
                    <select id="category_id" name="category_id" required class="form-select">
                        <option value="">— Select a category —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- RIGHT COLUMN: Attachments --}}
            <div class="form-section-card">
                <div class="section-header">
                    <div class="section-title"><i class="fas fa-paperclip"></i> Attachments</div>
                    <span style="font-size:12px;color:var(--text-faint);font-weight:500;">Optional</span>
                </div>

                <div class="form-group">
                    <div class="file-upload-area" onclick="document.getElementById('attachments').click();">
                        <div class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="file-upload-text">Click to upload files</div>
                        <div class="file-upload-hint">Screenshots, files, logs · Max 10MB</div>
                    </div>
                    <input type="file" id="attachments" name="attachments[]" multiple class="file-input-hidden">
                    <div class="file-list" id="fileList"></div>
                    @error('attachments.*')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>

        {{-- FULL WIDTH: Report Details --}}
        <div class="form-section-card">
            <div class="section-header">
                <div class="section-title"><i class="fas fa-pen-to-square"></i> Report Details</div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">
                    <i class="fas fa-align-left"></i> Description <span class="required">*</span>
                </label>
                <textarea id="description" name="description" rows="8" required class="form-textarea"
                    placeholder="Describe the threat in detail — include URLs, email addresses, timestamps, or any other relevant context (minimum 10 characters)...">{{ old('description') }}</textarea>
                <span class="form-hint"><i class="fas fa-info-circle"></i> The more detail you provide, the faster our analysts can assess the threat.</span>
                @error('description')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
                <a href="{{ route('reports.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <span class="btn-tip"><i class="fas fa-shield-alt"></i> End-to-end encrypted</span>
            </div>
        </div>

    </form>
</div>

<script>
    document.getElementById('attachments').addEventListener('change', function(e) {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const chip = document.createElement('div');
            chip.className = 'file-chip';
            chip.innerHTML = `<i class="fas fa-check-circle"></i> ${file.name} <span style="opacity:.6;font-weight:400;">(${(file.size/1024/1024).toFixed(2)} MB)</span>`;
            fileList.appendChild(chip);
        });
    });
</script>
@endsection
