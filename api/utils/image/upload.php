<?php

class upload extends api
{
  private $base_prefix = "/img/uploaded/";

  protected function FromDataURI($name)
  {
    $datauri = $_POST[$name];
    preg_match("/data:(.*?);(.*?),(.*)/", $datauri, $matches);

    phoxy_protected_assert(count($matches) == 4, "Image upload: Failure at data uri parsing");
    list($garbage, $type, $encoded, $data) = $matches;
    phoxy_protected_assert($ext = $this->IsTypeSupported($type), "Image upload: Unsupported filetype");
    phoxy_protected_assert($encoded == 'base64', "Image upload: Only Base64 encoding supported");

    $encodedData = str_replace(' ','+', $data);
    $decodedData = base64_decode($encodedData);

    $gd = imagecreatefromstring($decodedData);
    return $this->SaveFromGD($gd, $ext);
  }

    
  protected function Reserve( $name )
  {
    global $_FILES;
    if (!$_FILES[$name])
      return false;

    $file = $_FILES[$name];
    if($file['error']) 
      return false;

    if (false === $ext = $this->CheckExtension($file))
      return false;

    $gd = $this->CreateGD($ext, $file['tmp_name']);

    return $this->SaveFromGD($gd, $ext);
  }

  private function SaveFromGD($gd, $ext)
  {
   // phoxy_protected_assert(is_writable($this->base_prefix), "Upload subsytem cant initiate, target directory isnt writeable");

    if (!$gd)
      return false;

    $tran = db::Begin();
    $name = $this->AllocImageName($ext);
      
    $fileloc = $this->base_prefix.$name.".".$ext;

    $save_res = $this->SaveTo($gd, $ext, $fileloc);
    $res = $tran->Finish($save_res);
    @imagedestroy($gd);

    if ($res)
      return $fileloc;
    return NULL;
  }

  private function CheckExtension( $file )
  {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $this->IsTypeSupported($finfo->file($file['tmp_name']));
  }

  private function IsTypeSupported( $type )
  {
    $ext = array_search
    (
      $type,
      [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
      ]
    );
    return $ext;
  }

  private function CreateGD( $ext, $filename )
  {
    if ($ext == 'jpg')
      return @imagecreatefromjpeg($filename);
    if ($ext == 'png')
      return @imagecreatefrompng($filename);
    return false;        
  }

  private function SaveTo( $gd, $ext, $filename )
  {
    $file = $_SERVER['DOCUMENT_ROOT'].$filename;
    if ($ext == 'jpg')
      return imagejpeg($gd, $file);
    if ($ext == 'png')
      return imagepng($gd, $file);
    return false;
  }

  private function AllocImageName( $ext )
  {
    $uid = $this('api', 'auth')->get_uid();
    $res = db::Query("INSERT INTO utils.images(author, ext) VALUES ($1, $2) RETURNING name", [$uid, $ext], true);
    return $res['name'];
  }

  public function LocationByName( $name )
  {
    $res = $this->info($name);
    return $this->base_prefix.$res['name'].".".$res['ext'];
  }

  public function info( $name )
  {
    return db::Query("SELECT * FROM utils.images WHERE name=$1", [$name], true);
  }
}