<?php

use Intervention\Image\Facades\Image;

function convertMoneyInt($money)
{
  return preg_replace("/[^0-9]/", "", $money);
}

function getAllPath($image, $doNumber, $timestamp, $type)
{
  $tempPath = $image->getPathName();
  $extension = $image->extension();
  $filePath = "{$type}-images/{$type}-{$doNumber}-{$timestamp}.{$extension}";
  $fullPath = "storage/{$filePath}";

  return [$filePath, $fullPath, $tempPath];
}

function uploadImages($images, $doNumber, $timestamp)
{
  $arayOfPath = [];
  foreach ($images as $key => $image) {
    $type = explode("_image", (string) $key)[0];

    [$filePath, $fullPath, $tempPath] = getAllPath($image, $doNumber, $timestamp, $type);

    // compress & saving image
    $img = Image::make($tempPath);
    $img->save($fullPath, env('IMG_COMPRESS_PERCENTAGE'));

    $arayOfPath[$key] = $filePath;
  }
  return $arayOfPath;
}

function login_ngt()
{
  $login_data = json_encode(array(
    "username" => "naturagas.api",
    "password" => "pdk329mNc@52Mncu"
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
  $token = $value['body']['token'];

  return $token;
}

function get_location_ngt($plate_number)
{

  date_default_timezone_set("Asia/Jakarta");
  $d = strtotime("-1 minutes");
  $date_from = date("Y-m-d H:i:s", $d);
  $date_to = date("Y-m-d H:i:s");

  $token = login_ngt();

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