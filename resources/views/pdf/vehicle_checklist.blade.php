<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
      * {
         box-sizing: border-box;
         margin: 0;
         padding: 0;
      }

      body {
         width: 100vw;
         min-height: 100vh;
         position: relative;
         padding: 2.2rem;
         font-size: 10px;
         font-family: helvetica, sans-serif;
      }

      h1 {
         text-transform: uppercase;
         font-size: 1.3rem;
      }

      h2 {
         font-size: 12px;
         margin-bottom: 6px;
      }

      header {
         border-bottom: 2px solid #333;
         padding-bottom: 10px;
         margin-bottom: 15px;
      }

      .subtitle {
         color: #555;
         font-size: 10px;
         margin-top: 2px;
      }

      table {
         border: 1px solid #999;
         border-collapse: collapse;
         margin: 10px 0 20px 0;
         width: 100%;
      }

      th,
      td {
         border: 1px solid #999;
         padding: 4px 6px;
      }

      th {
         background: #eee;
         text-align: left;
      }

      .info-table td:first-child {
         width: 22%;
         font-weight: bold;
         background: #f5f5f5;
      }

      .summary-box {
         display: table;
         width: 100%;
         margin-bottom: 15px;
      }

      .summary-box .col {
         display: table-cell;
         width: 33.33%;
         text-align: center;
         border: 1px solid #999;
         padding: 8px;
      }

      .summary-box .col .label {
         font-size: 9px;
         color: #555;
         text-transform: uppercase;
      }

      .summary-box .col .value {
         font-size: 16px;
         font-weight: bold;
      }

      .checklist-grid {
         width: 100%;
         border: none;
         border-collapse: collapse;
         margin: 0;
      }

      .checklist-grid td {
         border: none;
         padding: 0;
         width: 50%;
         vertical-align: top;
      }

      .checklist-grid td.checklist-cell--left {
         padding-right: 10px;
      }

      .checklist-grid td.checklist-cell--right {
         padding-left: 10px;
      }

      .checklist-section {
         page-break-inside: avoid;
         margin-bottom: 12px;
      }

      .checklist-section table {
         margin: 4px 0 6px 0;
         font-size: 9px;
         table-layout: fixed;
         width: 100%;
      }

      .checklist-section th:first-child,
      .checklist-section td:first-child {
         width: 85%;
      }

      .checklist-section .status-col {
         width: 15%;
         text-align: right
      }

      .checklist-section th,
      .checklist-section td {
         padding: 3px 5px;
      }

      .status-col {
         text-align: right;
         border-left: 2px solid #000;
      }

      .status-ok {
         color: #1a7a1a;
         font-weight: bold;
      }

      .status-broken {
         color: #c0392b;
         font-weight: bold;
      }

      .notes {
         font-style: italic;
         color: #333;
         margin-bottom: 8px;
      }

      .images-section {
         page-break-inside: avoid;
      }

      .image-card {
         width: 100%;
         margin: 0 0 12px 0;
         border: 1px solid #999;
         padding: 6px;
         box-sizing: border-box;
      }

      .image-card img {
         width: 100%;
         height: 380px;
         object-fit: cover;
         display: block;
         margin-bottom: 5px;
      }

      .image-card .caption {
         font-size: 9px;
         color: #333;
      }

      footer {
         margin-top: 30px;
         font-size: 8px;
         color: #777;
         text-align: right;
      }
   </style>
   <title>Vehicle Checklist - {{ $vehicle->license_plate }}</title>
</head>

<body>
   @php
      function checklistPdfStatus($checklist)
      {
          if (gettype($checklist) !== 'integer') {
              return ['status-warning', 'ERROR'];
          }
          return $checklist == 1 ? ['status-broken', 'BROKEN'] : ['status-ok', 'OK'];
      }

      function checklistPdfLabel($itemKey)
      {
          $shortLabels = [
              'lampu_besar' => 'Besar',
              'lampu_kota' => 'Kota',
              'lampu_rem' => 'Rem',
              'lampu_sein' => 'Sein',
              'lampu_mundur' => 'Mundur',
              'lampu_kabin' => 'Kabin',
              'oli_mesin' => 'Mesin',
              'minyak_rem' => 'Rem',
              'minyak_kopling' => 'Kopling',
              'oli_hidraulic' => 'Hidraulic',
              'exhaust_brake' => 'Exhaust Brake',
              'ban_depan' => 'Depan',
              'ban_belakang_dalam' => 'Belakang Dalam',
              'ban_belakang_luar' => 'Belakang Luar',
              'ban_serep' => 'Serep',
              'velg_ban_depan' => 'Depan',
              'velg_ban_belakang_dalam' => 'Belakang Dalam',
              'velg_ban_belakang_luar' => 'Belakang Luar',
              'velg_ban_serep' => 'Serep',
              'tekanan_angin_ban_depan' => 'Depan',
              'tekanan_angin_ban_belakang_dalam' => 'Belakang Dalam',
              'tekanan_angin_ban_belakang_luar' => 'Belakang Luar',
              'tekanan_angin_ban_serep' => 'Serep',
              'kaca_depan' => 'Depan',
              'kaca_belakang' => 'Belakang',
              'kaca_samping' => 'Samping',
              'accu' => 'Accu',
              'tutup_radiator' => 'Tutup Radiator',
              'tangki_bahan_bakar' => 'Tangki BBM',
              'tutup_tangki_bahan_bakar' => 'Tutup Tangki BBM',
              'spion' => 'Spion',
              'wiper' => 'Wiper',
              'klakson' => 'Klakson',
              'panel_speedometer' => 'Speedometer',
              'panel_bahan_bakar' => 'Panel BBM',
              'sunvisor' => 'Sunvisor',
              'jok' => 'Jok',
          ];

          return $shortLabels[$itemKey] ?? ucwords(str_replace('_', ' ', $itemKey));
      }
   @endphp

   <header>
      <h1>Vehicle Checklist Report</h1>
      <div class="subtitle">Generated {{ now()->toFormattedDateString() }} {{ now()->format('H:i') }}</div>
   </header>

   <main>
      <h2>Vehicle Information</h2>
      <table class="info-table">
         <tr>
            <td>License Plate</td>
            <td>{{ $vehicle->license_plate }}</td>
            <td>Odo</td>
            <td>{{ $vehicleChecklist__ori->odo }}</td>
         </tr>
         <tr>
            <td>Location</td>
            <td>{{ $vehicle->address->name ?? '-' }}</td>
            <td>Checklist Date</td>
            <td>{{ $vehicleChecklist__ori->created_at->format('Y-m-d H:i') }}</td>
         </tr>
         <tr>
            <td>Driver</td>
            <td colspan="3">{{ $vehicleChecklist__ori->user->person->name ?? '-' }}</td>
         </tr>
      </table>

      <div class="summary-box">
         <div class="col">
            <div class="label">Vehicle Condition</div>
            <div class="value">{{ $okItemPercentage == 100 ? 'Good' : 'Critical' }}</div>
         </div>
         <div class="col">
            <div class="label">Overall Score</div>
            <div class="value">{{ $okItemPercentage }}%</div>
         </div>
         <div class="col">
            <div class="label">OK / Broken Items</div>
            <div class="value">{{ $totalOk }} / {{ $totalBroken }}</div>
         </div>
      </div>

      @php
         $checklistPairs = array_chunk($vehicleChecklist, 2, true);
      @endphp

      @foreach ($checklistPairs as $pair)
         @php
            $pairKeys = array_keys($pair);
         @endphp
         <table class="checklist-grid">
            <tr>
               @foreach ($pairKeys as $pairIndex => $key)
                  @php
                     $checklistConf = $pair[$key];
                     $summary = $checklistConf['summary'];
                  @endphp
                  <td class="{{ $pairIndex === 0 ? 'checklist-cell--left' : 'checklist-cell--right' }}">
                     <div class="checklist-section">
                        <h2>{{ $key }} ({{ getPercentage($summary['ok'], $summary['total']) }}% OK)</h2>
                        <table>
                           <thead>
                              <tr>
                                 <th>Item</th>
                                 <th class="status-col" style="width: 10%">Status</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach ($checklistConf['items'] as $itemKey => $item)
                                 @php
                                    $statusVal = checklistPdfStatus($item);
                                 @endphp
                                 <tr>
                                    <td>{{ checklistPdfLabel($itemKey) }}</td>
                                    <td class="status-col {{ $statusVal[0] }}">{{ $statusVal[1] }}</td>
                                 </tr>
                              @endforeach
                           </tbody>
                        </table>
                        @if (!empty($checklistConf['notes']))
                           <div class="notes">Notes: {{ $checklistConf['notes'] }}</div>
                        @endif
                     </div>
                  </td>
               @endforeach
               @if (count($pairKeys) === 1)
                  <td class="checklist-cell--right"></td>
               @endif
            </tr>
         </table>
      @endforeach

      @if ($vehicleChecklist__ori->vehicleChecklistImage->isNotEmpty())
         <div class="images-section">
            <h2>Attached Images</h2>
            @foreach ($vehicleChecklist__ori->vehicleChecklistImage as $checklistImage)
               @php
                  $imagePath = storage_path('app/public/' . $checklistImage->image);
               @endphp
               <div class="image-card">
                  @if (file_exists($imagePath))
                     <img src="{{ $imagePath }}" alt="">
                  @endif
                  @if ($checklistImage->description)
                     <div class="caption">{{ $checklistImage->description }}</div>
                  @endif
               </div>
            @endforeach
         </div>
      @endif
   </main>

   <footer>
      Sanlogistic - Vehicle Checklist #{{ $vehicleChecklist__ori->id }}
   </footer>
</body>

</html>
