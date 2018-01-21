<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Audio;

class FilehandleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function saveAudio(Request $request)
    {
	$file = $request->file('audio');
	$uploads_dir = '/var/www/html/explore_app/storage/app/public';
	$random_name = md5(uniqid(rand(), true));
	move_uploaded_file($file, "$uploads_dir/$random_name");
	$audio = new Audio;
	$audio->user_id = 1;//dummy
	$audio->save();

	$audio_id = $audio->id;

	exec("ffmpeg -i " . "$uploads_dir/$random_name" . " " . "$uploads_dir/audio_$audio_id.flac");
	return $audio_id;
    }
}
