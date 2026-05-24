<?php

use App\Transaction\Constants\NotifactionTypeConstant;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

function convertMoneyInt($money)
{
  return preg_replace("/[^0-9]/", "", $money);
}

function getAllPath($image, $mainIdentifier, $timestamp, $type)
{
  // POTENTIAL BUG
  // $tempPath = $image->getPathName();
  $tempPath = $image->getRealPath();
  $extension = $image->extension();
  $filePath = "{$type}-images/{$type}-{$mainIdentifier}-{$timestamp}.{$extension}";
  $fullPath = "storage/{$filePath}";

  return [$filePath, $fullPath, $tempPath];
}

function uploadImages($images, $mainIdentifier, $timestamp)
{
  $mainIdentifier = str_replace(' ', '_', strtolower(trim($mainIdentifier)));

  $arayOfPath = [];
  foreach ($images as $key => $image) {
    $type = Str::before($key, '_image');

    [$filePath, $fullPath, $tempPath] = getAllPath($image, $mainIdentifier, $timestamp, $type);

    // create folder if not exists
    if (!File::isDirectory("storage/{$type}-images")) {
      File::makeDirectory("storage/{$type}-images", 0777, true, true);
    }


    // compress & saving image
    $img = Image::make($tempPath);
    $img->save($fullPath, env('IMG_COMPRESS_PERCENTAGE'));

    $arayOfPath[$key] = $filePath;
  }
  return $arayOfPath;
}

function uploadImage($image, $type, $mainIdentifier, $timestamp)
{
  $type = Str::before($type, '_image');

  [$filePath, $fullPath, $tempPath] = getAllPath($image, $mainIdentifier, $timestamp, $type);

  // compress & saving image
  $img = Image::make($tempPath);

  // create folder if not exists
  if (!File::isDirectory("storage/{$type}-images")) {
    File::makeDirectory("storage/{$type}-images", 0777, true, true);
  }

  $img->resize(
    1280,
    null,
    function ($constraint) {
      $constraint->aspectRatio();
      $constraint->upsize();
    }
  );

  $img->save($fullPath, env('IMG_COMPRESS_PERCENTAGE'));

  return $filePath;
}

function login_ngt()
{
  $login_data = json_encode(array(
    "username" => env('USERNAME_NGT'),
    "password" => env('PASSWORD_NGT')
  ));

  $url = "https://api.ngt.systems/dx/api/account/login";

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $login_data);

  $json_response = curl_exec($curl);
  curl_close($curl);

  $value = json_decode($json_response, true);

  try {
    $token = $value['body']['token'];
  } catch (Exception $e) {
    return "";
  }

  return $token;
}

function get_location_ngt($plate_number)
{

  date_default_timezone_set("Asia/Jakarta");
  $d = strtotime("-1 minutes");
  $date_from = date("Y-m-d H:i:s", $d);
  $date_to = date("Y-m-d H:i:s");

  $token = login_ngt();

  if (empty($token))
    return [
      "lat" => "No Data",
      "lon" => "No Data",
      "loc" => "No Data"
    ];

  $auth = "Authorization: Bearer {$token}";

  $location_data = json_encode(array(
    "reg_no" => $plate_number,
    "start_time" => $date_from,
    "end_time" => $date_to,
    "page" => "1"
  ));

  $url = "https://api.ngt.systems/dx/api/asset/getAssetHistory";

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $auth));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $location_data);

  $json_response = curl_exec($curl);
  curl_close($curl);

  $value = json_decode($json_response, true);

  if ($value['status'] == 2 || ($value['status'] == 1 && !isset($value['data']))) {
    return [
      "lat" => "No Data",
      "lon" => "No Data",
      "loc" => "No Data"
    ];
  }

  return $value['data'][0];
}

function genereateNotifaction(string $notifType, string $subject = "", string $action = ""): array
{
  $message = NotifactionTypeConstant::NOTIFICATIONS[$notifType];

  if ($message) {
    $message = Str::wordCount($action) > 0 ?
      Str::replaceArray('?', [ucfirst($subject), ucfirst($action)], $message) :
      $subject;
  }

  return [
    'toastr-message' => $message,
    'toastr-type' => $notifType,
  ];
}

function getPercentage(mixed $portion, mixed $total)
{
  return round(((int) $portion / (int) $total) * 100);
}

function filterArrayByLabel(array $original, array $labels)
{
  return array_intersect_key($original, array_flip($labels));
}

function days_to_seconds($days)
{
  if (is_numeric($days)) {
    return 86400 * $days;
  } else {
    return 'string must be numeric';
  }
}

function decideTextColorByDay($date, $rules, $fallbackColor, $defaultColor = 'text-white')
{
  if (empty($date)) {
    return $defaultColor;
  }

  $date = Carbon\Carbon::parse($date);
  $diff = $date->timestamp - now()->timestamp;

  foreach ($rules as $key => $value) {
    if ($diff <= days_to_seconds($key))
      return $value;
  }

  return $fallbackColor;
}

function decideTextColorByTwoNumber($int, $compareInt, $failColor, $fallbackColor, $defaultColor = 'text-white')
{
  if (empty($compareInt)) {
    return $defaultColor;
  }

  if ((int) $compareInt <= (int) $int)
    return $failColor;

  return $fallbackColor;
}
