<?php

namespace App\Interfaces;

interface VehicleChecklistInterface
{
	public const CHECKLIST_TYPES = [
		'lampu_besar',
		'lampu_kota',
		'lampu_rem',
		'lampu_sein',
		'lampu_mundur',
		'lampu_kabin',
		'lampu_senter',
		'kaca_depan',
		'kaca_samping',
		'kaca_belakang',
		'ban_depan',
		'ban_belakang_dalam',
		'ban_belakang_luar',
		'ban_serep',
		'tekanan_angin_ban_depan',
		'tekanan_angin_ban_belakang_dalam',
		'tekanan_angin_ban_belakang_luar',
		'tekanan_angin_ban_serep',
		'velg_ban_depan',
		'velg_ban_belakang_dalam',
		'velg_ban_belakang_luar',
		'velg_ban_serep',
		'ganjal_ban',
		'dongkrak',
		'kunci_roda',
		'stang_kunci_roda',
		'pipa_bantu',
		'kotak_p3k',
		'apar',
		'emergency_triangle',
		'tool_kit',
		'seragam',
		'safety_shoes',
		'driver_license',
		'kartu_keur',
		'stnk',
		'helmet',
		'tatakan_menulis',
		'ballpoint',
		'straples',
		'exhaust_brake',
		'spion',
		'wiper',
		'tangki_bahan_bakar',
		'tutup_tangki_bahan_bakar',
		'tutup_radiator',
		'accu',
		'oli_mesin',
		'minyak_rem',
		'minyak_kopling',
		'oli_hidraulic',
		'klakson',
		'panel_speedometer',
		'panel_bahan_bakar',
		'sunvisor',
		'jok',
		'air_conditioner',
	];
}
