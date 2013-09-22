<?php

define('PATH', 'upload');
define('THUMBSIZE', 120);
define('MAXSIZE', 2098888); // 2 mb

class ImageUpload
{
// private $db;

private $validFormats = array('jpg', 'png', 'gif', 'bmp', 'jpeg','GIF','JPG','PNG');
private $infoMessages = array(
	'failed' => 'Başarısız Oldu',
	'maxSize' => 'Maksimum resim boyutu 2 mb dir !',
	'invalidFormat' => 'Geçersiz dosya biçimi.',
	'selectImage' => 'Lütfen resim seçin.');

	function __construct()
	{
		// $this->db = new Database;
		
		error_reporting(0);
		
		if(!is_dir(PATH)){
		
			mkdir(PATH,777);
			mkdir(PATH.'/thumb',777);
		}

	}
	
	public function uploadImage($files)
	{
		$name = $files['imgUpload']['name'];
		$size = $files['imgUpload']['size'];
		
		if(strlen($name)){
			$ext = pathinfo($name,PATHINFO_EXTENSION);
			if(in_array($ext,$this->validFormats)){
				if($size < MAXSIZE){
					$imageName = md5(uniqid()).'.'.$ext;
					$tmp = $_FILES['imgUpload']['tmp_name']; 
					
					if(move_uploaded_file($tmp, PATH.'/'.$imageName)){
					
						move_uploaded_file($tmp, PATH.'/'.$imageName);
						
						$thumb = $this->createThumbnail(PATH.'/'.$imageName);
						
						echo "<img src='".$thumb."'  class='preview'> <input type='hidden' name='thumb' id='thumb' value='$thumb' />";
					
					}else{
						echo $this->infoMessages['failed'];
					}
				}else{
					echo $this->infoMessages['maxSize'];					
				}
			}else{
				echo $this->infoMessages['invalidFormat'];	
			}
		}else{		
			echo $this->infoMessages['selectImage'];
		}
	}
	
	public function createThumbnail($source)
	{
		 switch(pathinfo($source,PATHINFO_EXTENSION)){
			case "gif":
				$src = imagecreatefromgif($source);
				break;
			case "jpg":
				$src = imagecreatefromjpeg($source);
				break;
			case "jpeg":
				$src = imagecreatefromjpeg($source);
				break;
			case "png":
				$src = imagecreatefrompng($source);
				break;
		}	
		
		list($width,$height) = getimagesize($source);
		
		$newWidth = THUMBSIZE;
		$newHeight = THUMBSIZE;
		//$newHeight = floor($height *(THUMBSIZE/$width));
		
		$tmp=imagecreatetruecolor($newWidth,$newHeight);
		imagealphablending($tmp, false);
		imagesavealpha($tmp,true);
		$transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
		imagefilledrectangle($tmp, 0, 0, $newWidth, $newHeight, $transparent);
		imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);

		$thumb = PATH.'/thumb/thumb-'. pathinfo($source,PATHINFO_BASENAME);
		
        switch(pathinfo($source,PATHINFO_EXTENSION)){
			case "gif":
				imagegif($tmp,$thumb);
				break;
			case "jpg":
				imagejpeg($tmp,$thumb,100);
				break;
			case "jpeg":
				imagejpeg($tmp,$thumb,100);
				break;
			case "png":
				imagepng($tmp,$thumb);
				break;
		}
		return $thumb;
	}
}

$img = new ImageUpload;
$img->uploadImage($_FILES);
?>
