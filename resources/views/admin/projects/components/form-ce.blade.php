<div>
  <div class="row">
    <div class="col-xl-6">
      <div class="row g-4 mb-2">

        <div class="col-xl-6">
          <label for="name" class="form-label">Project Name</label>
          <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
            name="name" value="{{ old('name', $project['name'] ?? null) }}" required>

          @error('name')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="col-xl-6">
          <label for="company_id" class="form-label">Customer Name</label>
          <select class='form-select form-select-lg  @error('company_id') is-invalid @enderror' name='company_id'
            id="company_id" required>
            <option value="" class="d-none"></option>
            @foreach ($customers as $customer)
              @if ($customer->id == old('company_id', $project['company_id'] ?? null))
                <option value="{{ $customer->id }}" selected>{{ $customer->name }}</option>
              @else
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
              @endif
            @endforeach
          </select>

          @error('company_id')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="col-xl-6">
          <label for="date_start" class="form-label">Project Start</label>
          <input type="date" class="form-control form-control-lg  @error('date_start') is-invalid @enderror"
            id="" name="date_start" value="{{ old('date_start', $project['date_start'] ?? null) }}" required>

          @error('date_start')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="col-xl-6">
          <label for="date_end" class="form-label">Project End</label>
          <input type="date" class="form-control form-control-lg @error('date_end') is-invalid @enderror"
            id="date_end" name="date_end" value="{{ old('date_end', $project['date_end'] ?? null) }}" required>

          @error('date_end')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

      </div>
    </div> <!-- EOC Data Perusahaan -->

    <div class="col-xl-6 d-flex flex-column">
      <label for="catatan" class="form-label">Comments</label>
      <textarea class="form-control form-control-lg flex-grow-1" id="catatan" name="catatan" aria-describedby="commentHelp">{{ old('catatan', $project['catatan'] ?? null) }}</textarea>
      <div id="commentHelp" class="form-text">
        Descriptions/Notes about this project
      </div>
    </div>
  </div>
</div>
