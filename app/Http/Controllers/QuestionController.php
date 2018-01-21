<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Discussion;
use App\Bookmark;
use App\View;
use App\Upvoteque;
use App\Answer;
use App\Upvoteans;
use App\Subscription;
use App\Notification;

class QuestionController extends Controller
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

    public function addQuestion(Request $request)
    {
         $token = $request->input('token');
         $title = $request->input('title');
         $content = $request->input('content');
         $category = $request->input('category');

         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         if ($uid) {
            $discussion = new Discussion;
            $discussion->uid = $uid;
            $discussion->category = $category;
            $discussion->content = $content;
            $discussion->save();

            $subscription = new Subscription;
            $subscription->uid = $discussion->uid;
            $subscription->qid = $discussion->id;
            $subscription->save();

            $zzz = array('id' => $discussion->id, 'uid' => $discussion->uid, 'category' => $discussion->category, 'content' => $discussion->content, 'upvotes' => 0, 'answers' => 0, 'views' => 0, 'created_at' => $discussion->created_at, 'updated_at' => $discussion->updated_at);

            $data = array('success' => 1, 'msg' => $zzz);
            return json_encode($data);
        }
        else {
            $data = array('success' => 0, 'msg' => 'Token expired');
            return json_encode($data);
        }
    }

    public function getQuestions(Request $request)
    {
        $token = $request->input('token');
        $tab = $request->input('tab');
        $category = $request->input('category');
         
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         if ($uid) {
            $discussions = [];
            if($category == 0) {
                if($tab == '0') {
                    $discussions = Discussion::orderBy('created_at', 'ASC')->get();
                }
                elseif($tab == '1') {
                    $discussions = Discussion::orderBy('views', 'ASC')->get();
                }
                elseif($tab == '2') {
                    $discussions = Discussion::orderBy('answers', 'ASC')->get();
                }
                elseif($tab == '3') {
                    $discussions = Discussion::orderBy('upvotes', 'ASC')->get();
                }
                elseif($tab == '4') {
                    $bookmark = Bookmark::where('uid', $uid)->get();
                    $bookmark_list = Array();
                    foreach($bookmark as $b) {
                        $bookmark_list[] = $b["did"];
                    }
                    $discussions = Discussion::where('id', $bookmark_list)->orderBy('upvotes', 'ASC')->get();
                }
                else {
                    $discussions = Discussion::orderBy('updated_at', 'DESC')->get();
                }
            }
            else {
                if($tab == '0') {
                    $discussions = Discussion::where('category', $category)->orderBy('created_at', 'ASC')->get();
                }
                elseif($tab == '1') {
                    $discussions = Discussion::where('category', $category)->orderBy('views', 'ASC')->get();
                }
                elseif($tab == '2') {
                    $discussions = Discussion::where('category', $category)->orderBy('answers', 'ASC')->get();
                }
                elseif($tab == '3') {
                    $discussions = Discussion::where('category', $category)->orderBy('upvotes', 'ASC')->get();
                }
                elseif($tab == '4') {
                    $bookmark = Bookmark::where('uid', $uid)->get();
                    $bookmark_list = Array();
                    foreach($bookmark as $b) {
                        $bookmark_list[] = $b["did"];
                    }
                    $discussions = Discussion::where('category', $category)->where('id', $bookmark_list)->orderBy('upvotes', 'ASC')->get();
                }
                else {
                    $discussions = Discussion::where('category', $category)->orderBy('updated_at', 'DESC')->get();
                }
                // echo $category, $tab;
            }

            $mydiscussions = [];
            foreach ($discussions as $d) {
                $bookmark = Bookmark::where('did', $d["id"])->where('uid', $uid)->get();
                if (count($bookmark) == 1) {
                    array_push($mydiscussions, ['discussion'=>$d, 'bookmark'=>1]);
                }
                else {
                    array_push($mydiscussions, ['discussion'=>$d, 'bookmark'=>0]);  
                }
            }
            $data = array('success' => 1, 'msg' => $mydiscussions);
            return json_encode($data);
        }
        else {
            $data = array('success' => 0, 'msg' => 'Token expired');
            return 1;//json_encode($data);
        }
    }

    public function bookmarkQuestion(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         // $data = array('success' => 0, 'msg' => 'Token expired');
         // return json_encode($data);

         $entry = Discussion::where('id', $did)->get();
         if(count($entry) == 1) {
            $bookmark = Bookmark::where('did', $did)->where('uid', $uid)->get();
            if(count($bookmark) == 0) {
                $entry = new Bookmark;
                $entry->did = $did;
                $entry->uid = $uid;
                $entry->save();
                $data = array('success' => 1, 'msg' => 'Bookmark added');
                return json_encode($data);
            }
            else {
                Bookmark::where('did', $did)->where('uid', $uid)->delete();
                $data = array('success' => 1, 'msg' => 'Bookmark deleted');
                return json_encode($data);
            }
        }
        else {
            $data = array('success' => 0, 'msg' => 'Discussion not found');
            return json_encode($data);
        }
    }



    public function viewQuestion(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         // $data = array('success' => 0, 'msg' => 'Token expired');
         // return json_encode($data);

         $entry = Discussion::where('id', $did)->get();
         if(count($entry) == 1) {
            $view = View::where('did', $did)->where('uid', $uid)->get();
            if(count($view) == 0) {
                $entry = new View;
                $entry->did = $did;
                $entry->uid = $uid;
                $entry->save();

                $entry2 = Discussion::where('id', $did)->first();
                $entry2->views = $entry[0]["views"]+1;
                $entry2->save();

                $data = array('success' => 1, 'msg' => 'Question viewed');
                return json_encode($data);
            }
            else {
                $data = array('success' => 0, 'msg' => 'Already viewed this question');
                return json_encode($data);
            }
        }
        else {
            $data = array('success' => 0, 'msg' => 'Discussion not found');
            return json_encode($data);
        }
    }





    public function upvoteQuestion(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         // $data = array('success' => 0, 'msg' => 'Token expired');
         // return json_encode($data);

         $entry = Discussion::where('id', $did)->get();
         if(count($entry) == 1) {
            $upvote = Upvoteque::where('did', $did)->where('uid', $uid)->get();
            if(count($upvote) == 0) {
                $entry1 = new Upvoteque;
                $entry1->did = $did;
                $entry1->uid = $uid;
                $entry1->save();

                $entry2 = Discussion::where('id', $did)->first();
                $entry2->upvotes = $entry[0]["upvotes"]+1;
                $entry2->save();

                $subscriptions = Subscription::where('qid', $did)->get();
                foreach($subscriptions as $subscription){
                    $notification = new Notification;
                    $notification->uid = $subscription->uid;
                    $notification->did = $subscription->qid;//change qid to did or otherwise
                    $notification->falana_dhikada = $uid;
                    $notification->type = 2;
                    $notification->save();
                }

                $data = array('success' => 1, 'msg' => 1);//upvote
                return json_encode($data);
            }
            else {
                Upvoteque::where('did', $did)->where('uid', $uid)->delete();

                $entry2 = Discussion::where('id', $did)->first();
                $entry2->upvotes = $entry[0]["upvotes"]-1;
                $entry2->save();

                $subscriptions = Subscription::where('qid', $did)->get();
                foreach($subscriptions as $subscription){
                    $notification = new Notification;
                    $notification->uid = $subscription->uid;
                    $notification->did = $subscription->qid;
                    $notification->falana_dhikada = $uid;
                    $notification->type = 3;
                    $notification->save();
                }

                $data = array('success' => 1, 'msg' => -1);//downvote
                return json_encode($data);
            }
        }
        else {
            $data = array('success' => 0, 'msg' => 'Discussion not found');
            return json_encode($data);
        }
    }



    public function upvoteAnswer(Request $request)
    {
         $token = $request->input('token');
         $aid = $request->input('aid');
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         // $data = array('success' => 0, 'msg' => 'Token expired');
         // return json_encode($data);

         $entry = Answer::where('id', $aid)->get();
         if(count($entry) == 1) {
            $upvote = Upvoteans::where('aid', $aid)->where('uid', $uid)->get();
            if(count($upvote) == 0) {
                $entry1 = new Upvoteans;
                $entry1->aid = $aid;
                $entry1->uid = $uid;
                $entry1->save();

                $entry2 = Answer::where('id', $aid)->first();
                $entry2->upvotes = $entry[0]["upvotes"]+1;
                $entry2->save();

                $data = array('success' => 1, 'msg' => 'Answer upvoted');
                return json_encode($data);
            }
            else {
                Upvoteans::where('aid', $aid)->where('uid', $uid)->delete();

                $entry2 = Answer::where('id', $aid)->first();
                $entry2->upvotes = $entry[0]["upvotes"]-1;
                $entry2->save();

                $data = array('success' => 1, 'msg' => 'Answer downvoted');
                return json_encode($data);
            }
        }
        else {
            $data = array('success' => 0, 'msg' => 'Answer not found');
            return json_encode($data);
        }
    }




    public function answerQuestion(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         $content = $request->input('content');
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         // $data = array('success' => 0, 'msg' => 'Token expired');
         // return json_encode($data);

         $entry = Discussion::where('id', $did)->get();
         if(count($entry) == 1) {
            $answer = new Answer;
            $answer->did = $did;
            $answer->uid = $uid;
            $answer->content = $content;
            $answer->save();

            $entry2 = Discussion::where('id', $did)->first();
            $entry2->answers = $entry[0]["answers"]+1;
            $entry2->save();

            $subscriptions = Subscription::where('qid', $did)->get();

            foreach($subscriptions as $subscription){
                $notification = new Notification;
                $notification->uid = $subscription->uid;
                $notification->did = $subscription->qid;
                $notification->falana_dhikada = $uid;
                $notification->type = 1;
                $notification->save();
            }

            $zzz = array('id' => $answer->id, 'did' => $answer->did, 'uid' => $answer->uid, 'upvotes' => $answer->did, 'content' => $answer->content, 'created_at' => $answer->created_at, 'updated_at' => $answer->updated_at);
            $data = array('success' => 1, 'msg' => $zzz);
            return json_encode($data);
        }
        else {
            $data = array('success' => 0, 'msg' => 'Discussion not found');
            return json_encode($data);
        }
    }

    public function getAnswers(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         
         // $uid = app('db')->table('keys')->where('token', $token)->value('uid');
         $uid = 1;

         if ($uid) {
            $answers = Answer::where('did', $did)->get();
            $data = array('success' => 1, 'msg' => $answers);
            return json_encode($data);
        }
        else {
            $data = array('success' => 0, 'msg' => 'Token expired');
            return 1;//json_encode($data);
        }
    }

    public function delQuestion(Request $request)
    {
         $token = $request->input('token');
         $did = $request->input('did');
         
         $uid = 1;

         $entry = Discussion::where('id', $did)->get();
         if (count($entry) == 1 && $entry[0]["uid"] == $uid) {
            echo "Allowed";
            Discussion::where('id', $did)->delete();
            Answer::where('did', $did)->delete();
            Bookmark::where('did', $did)->delete();
            Upvoteque::where('did', $did)->delete();

            $data = array('success' => 1, 'msg' => 'Discussion, Answer, Bookmark, Upvote deleted');
            return json_encode($data);
         }
         else{
            $data = array('success' => 1, 'msg' => 'Question invalid or you are not author');
            return json_encode($data);;
         }
         $data = array('success' => 0, 'msg' => 'Question invalid or you are not author');
         return $data;
    }

    public function delAnswer(Request $request)
    {
         // make changes 
         $token = $request->input('token');
         $aid = $request->input('aid');
         
         $uid = 1;
         //owner of question can also del answers

         $entry = Answer::where('id', $aid)->get();
         if (count($entry) == 1 && $entry[0]["uid"] == $uid) {
            $did = Answer::where('id', $aid)->first()["did"];
            Answer::where('id', $aid)->delete();
            // echo $did;
            $entry = Discussion::where('id', $did)->first();

            $entry2 = Discussion::where('id', $did)->first();
            $entry2->answers = $entry["answers"]-1;
            $entry2->save();

            $data = array('success' => 1, 'msg' => 'Answer deleted');
            return json_encode($data);
         }
         else{
            $data = array('success' => 0, 'msg' => 'Answer invalid or you are not author');
            return json_encode($data);
         }
    }

    public function seeNotification(Request $request)
    {
         // make changes 
         $token = $request->input('token');
         // return 5;
         $uid = 1;
         // $discussions = Discussion::orderBy('updated_at', 'DESC')->get();
         $notifications = Notification::where('uid', $uid)->where('seen', 0)->orderBy('did', 'DESC')->get();
         $answers = [];
         $upvotes = [];
         $downvotes = [];
         // $notifications_clubbed = [];
         // echo $notifications;
	 if(count($notifications) == 0){
	     $data = array('success' => 1, 'msg' => 'There are no notifications!');
             return json_encode($data);
	 }
         $did = $notifications[0]['did'];//assuming non zero notifications. change it later

         $temp = [];
         $notifications_did = [];
         foreach($notifications as $notification){
            if($notification['did'] == $did){
                $temp[] = $notification;
            }
            else{
                $notifications_did[] = $temp;
                $temp = [];
                $temp[] = $notification;
                $did = $notification['did'];
            }
         }
         $notifications_did[] = $temp;

         // // // // // // // // // // // // // // // // // // // // // // // // // // // //
         $notifications_clubbed = [];
         foreach($notifications_did as $notification_did){
            $temp = [];
            $time = 0;
            foreach($notification_did as $notification){
                if($notification['type'] == 1){
                    $temp[] = $notification;
                    $time = max(strtotime($notification['updated_at']), $time);
                }
            }
            if(count($temp) > 0){
                $notifications_clubbed[] = ['notification'=>$temp, 'time'=>$time];
            }
            
            $temp = [];
            $time = 0;
            foreach($notification_did as $notification){
                if($notification['type'] == 2){
                    $temp[] = $notification;
                    $time = max(strtotime($notification['updated_at']), $time);
                }
            }
            if(count($temp) > 0){
                $notifications_clubbed[] = ['notification'=>$temp, 'time'=>$time];
            }

            $temp = [];
            $time = 0;
            foreach($notification_did as $notification){
                if($notification['type'] == 3){
                    $temp[] = $notification;
                    $time = max(strtotime($notification['updated_at']), $time);
                }
            }
            if(count($temp) > 0){
                $notifications_clubbed[] = ['notification'=>$temp, 'time'=>$time];
            }
         }

         usort($notifications_clubbed, function($a, $b){return $b['time']-$a['time'];});

         // echo count($notifications_array);

         // while(count($notifications_array)>0){
         //    $question = $notifications_array[0];
         //    unset($notifications_array[0]);
         //    // array_slice($notifications_array, 0, 1);
         // }
         // $question = array_values($notifications)[0];
         // $question = $notifications_clubbed[0];
         foreach($notifications as $notification){
            if($notification->type == 1){//change for add answer, delete ans
                $answers[] = $notification;
            }
            if($notification->type == 2){
                $upvotes[] = $notification;
            }
            if($notification->type == 3){
                $downvotes[] = $notification;
            }

         }
         $data = array('success' => 1, 'msg' => array('clubbed'=>$notifications_clubbed, 'answers'=>$answers, 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
         return json_encode($data);
    }
}
