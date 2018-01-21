<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transcript;

class EnglishController extends Controller
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

    public function saveTranscript(Request $request)
    {
	$audio_id = $request->input('audio_id');
	$trans = $request->input('transcript');
	$transcript = new Transcript;
	$transcript->audio_id = $audio_id;
	$transcript->transcript = $trans;
	$transcript->save();

	$file = fopen(getcwd() . "/../storage/app/transcripts/transcript_" . $audio_id . ".txt","w");
	fwrite($file, $trans);
	fclose($file);
	return 1;
    }
}
