<div class="mb-5">
  <h4 class="text-primary fw-bold">Data Perusahaan</h4>
  <hr>
  <div class="row g-2 mb-2">

    <div class="col-xl-4">
      <label for="name" class="form-label">Nama Perusahaan</label>
      <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
        name="name" value="{{ old('name', $company->name) }}" autofocus>

      @error('name')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="company_type_id" class="form-label">Tipe</label>
      <select class='form-select form-select-lg  @error('company_type_id') is-invalid @enderror' name='company_type_id'
        id="company_type_id">
        <option value='' class="d-none"></option>
        @foreach ($companyTypes as $companyType)
          @if ($companyType->id == old('company_type_id', $company->company_type_id))
            <option value='{{ $companyType->id }}' selected>{{ $companyType->name }}</option>
          @else
            <option value='{{ $companyType->id }}'>{{ $companyType->name }}</option>
          @endif
        @endforeach
      </select>

      @error('company_type_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="phone_number" class="form-label">Nomor Telepon</label>
      <input type="text" class="form-control form-control-lg @error('phone_number') is-invalid @enderror"
        id="phone_number" name="phone_number" value="{{ old('phone_number', $company->phone_number) }}">

      @error('phone_number')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="fax" class="form-label">Fax</label>
      <input type="text" class="form-control form-control-lg @error('fax') is-invalid @enderror" id="fax"
        name="fax" value="{{ old('fax', $company->fax) }}">

      @error('fax')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
        name="email" value="{{ old('email', $company->email) }}">

      @error('email')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="website" class="form-label">Website</label>
      <input type="text" class="form-control form-control-lg @error('website') is-invalid @enderror" id="website"
        name="website" value="{{ old('website', $company->website) }}">

      @error('website')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="director" class="form-label">Nama Direktur</label>
      <input type="text" class="form-control form-control-lg @error('director') is-invalid @enderror" id="director"
        name="director" value="{{ old('director', $company->director) }}">

      @error('director')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="npwp" class="form-label">NPWP</label>
      <input type="text" class="form-control form-control-lg @error('npwp') is-invalid @enderror" id="npwp"
        name="npwp" value="{{ old('npwp', $company->npwp) }}">

      @error('npwp')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="city_id" class="form-label">Kota</label>
      <select class='form-select form-select-lg @error('city_id') is-invalid @enderror' name='city_id'>
        <option value='' class="d-none"></option>
        @foreach ($cities as $city)
          @if ($city->id == old('city_id', $company->city_id))
            <option value="{{ $city->id }}" selected>{{ $city->name }}</option>
          @else
            <option value="{{ $city->id }}">{{ $city->name }}</option>
          @endif
        @endforeach
      </select>
      @error('city_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-12">
      <label for="full_address" class="form-label">Alamat Lengkap</label>
      <input type="text" class="form-control form-control-lg @error('full_address') is-invalid @enderror"
        id="full_address" name="full_address" value="{{ old('full_address', $company->full_address) }}">

      @error('full_address')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

  </div>
</div>
<div class="row g-2 mb-5">
  <div class="col-xl-6">
    <h4 class="text-primary fw-bold">SIUP</h4>
    <hr>
    <div class="row g-2">

      <div class="col-xl-6">
        <label for="siup" class="form-label">Nomor SIUP</label>
        <input type="text" class="form-control form-control-lg  @error('siup') is-invalid @enderror"
          id="siup" name="siup" value="{{ old('siup', $siup['number'] ?? null) }}">

        @error('siup')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl-6">
        <label for="siup_expire" class="form-label">Masa Beralaku SIUP</label>
        <input type="date" class="form-control form-control-lg @error('siup_expire') is-invalid @enderror"
          id="siup_expire" name="siup_expire" value="{{ old('siup_expire', $siup['expire'] ?? null) }}">

        @error('siup_expire')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl-12">
        <label for="siup_image" class="form-label">Gambar SIUP</label>
        <input class="form-control form-control-lg @error('siup_image') is-invalid @enderror" type="file"
          accept="image/*" name="siup_image" id="siup_image" onchange="previewImage('siup_image')">

        @error('siup_image')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl mt-5">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center"
          style="background-color: transparent;">
          <img src="{{ asset('storage/' . ($siup['image'] ?? 'default/default.jpg')) }}"
            class="img-fluid rounded zoom mw-100" style="max-height: 200px" id="siup_image-preview" alt=""
            data-action="zoom">
        </div>
      </div>

    </div>
  </div> <!-- EOC SIUP -->
  <div class="col-xl-6">
    <h4 class="text-primary fw-bold">SIPA</h4>
    <hr>
    <div class="row g-2">

      <div class="col-xl-6">
        <label for="sipa" class="form-label">Nomor SIPA</label>
        <input type="text" class="form-control form-control-lg @error('sipa') is-invalid @enderror"
          id="sipa" name="sipa" value="{{ old('sipa', $sipa['number'] ?? null) }}">

        @error('sipa')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl-6">
        <label for="sipa_expire" class="form-label">Masa Beralaku SIPA</label>
        <input type="date" class="form-control form-control-lg @error('sipa_expire') is-invalid @enderror"
          id="sipa_expire" name="sipa_expire" value="{{ old('sipa_expire', $sipa['expire'] ?? null) }}">

        @error('sipa_expire')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl-12">
        <label for="sipa_image" class="form-label">Gambar SIPA</label>
        <input class="form-control form-control-lg @error('sipa_image') is-invalid @enderror" type="file"
          accept="image/*" name="sipa_image" id="sipa_image" onchange="previewImage('sipa_image')">

        @error('sipa_image')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>

      <div class="col-xl mt-5">
        <div class="w-100 h-100 d-flex justify-content-center align-items-center"
          style="background-color: transparent;">
          <img src="{{ asset('storage/' . ($sipa['image'] ?? 'default/default.jpg')) }}"
            class="img-fluid rounded zoom mw-100" style="max-height: 200px" id="sipa_image-preview"
            data-action="zoom" alt="" />
        </div>
      </div>

    </div>
  </div> <!-- EOC SIPA -->
</div>

<div class="form-floating mb-3">
  <textarea class="form-control" placeholder="Leave a comment here" id="note" name="note"
    style="height: 100px">{{ old('note', $company['note']) }}</textarea>
  <label for="note">Comments</label>
</div>
