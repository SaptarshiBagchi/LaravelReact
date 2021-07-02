<?php

namespace App\Http\Controllers;


use App\Http\Resources\UsersSearchResource;
use App\Models\FriendsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SocialMediaAPI extends Controller
{

    /**access own profile */
    public function ownProfile(Request $request)
    {
        $authenticated_user = $request->user();
        $pending_requests = FriendsModel::where('to_user_id', $authenticated_user->id)->where('status', 1)->with('toUser')->get();
        $friends = FriendsModel::where('to_user_id', $authenticated_user->id)->where('status', 2)->with('fromUser')->get();
        return response()->json([
            'profile_data' => $authenticated_user,
            'pending_friend_requests' => $pending_requests,
            'friends' => $friends
        ], 200);
    }

    public function userProfile(Request $request)
    {
        /**This validation check ensures no one can try get a resource that is not present */
        $validator = Validator::make($request->route()->parameters(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $authenticated_user = $request->user();
        //same profile
        if ($authenticated_user->id == $request->user_id) {
            return response()->json([

                'user_data' => $authenticated_user,
                'isFriend' => false,
                'statusText' => 'Own Profile'
            ], 200);
        }
        //now different profile
        $thisUser = User::where('id', $request->user_id)->select('id', 'name', 'email')->first();
        $is_Friend_status = FriendsModel::where('from_user_id', $authenticated_user->id)->where('to_user_id', $thisUser->id)->with('statusText')->first();
        $has_sent_request = FriendsModel::where('to_user_id', $authenticated_user->id)->where('from_user_id', $thisUser->id)->where('status', 1)->with('statusText')->first();

        //just a simple intersect clause will do it
        $mutual_friends = DB::select('(SELECT to_user_id, users.name, users.email from friends JOIN users ON users.id = friends.to_user_id where friends.from_user_id = ? AND status = 2)
        INTERSECT
        (SELECT to_user_id, users.name, users.email from friends JOIN users ON users.id = friends.to_user_id where friends.from_user_id = ? AND status = 2)', [$authenticated_user->id, $request->user_id]);

        return response()->json([

            'user_data' => $thisUser,
            'isFriend' => empty($is_Friend_status) ? false : $is_Friend_status,
            'mutual_friends' => $mutual_friends,
            'has_sent_request_to_you' => empty($has_sent_request) ? false : $has_sent_request->statusText->status_text,
        ], 200);
    }
    /**
     * The search endpoint
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->route()->parameters(), [
            'search_param' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        //we have converted the name into a upper case format anyway
        $joined_search_param = '%' . strtoupper($request->search_param) . '%';
        $users = User::where('name_search', 'LIKE', $joined_search_param)->orWhere('email', 'LIKE', $joined_search_param)->get();
        $users_resource = UsersSearchResource::collection($users);
        return response()->json([
            'users_matched' => $users_resource
        ], 200);
    }

    /**The send friend request endpoint */
    public function sendFriendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        //the validation has succeeded
        $authenticated_user = $request->user();
        $send_request_to = FriendsModel::where('to_user_id', $request->friend_id)->where('from_user_id', $authenticated_user->id)->first();
        $have_you_received = FriendsModel::where('from_user_id', $request->friend_id)->where('to_user_id', $authenticated_user->id)->first();

        /**
         * Two possibilities
         * 1.) The you have already sent the request or are friends.
         * 2.) He has sent you a request
         */

        //either friends or request pending
        if (!empty($send_request_to)) {

            if ($send_request_to->status == 1) {
                $info = 'Friend request pending.';
            } else {
                $info = 'You are already friends.';
            }

            return response()->json([
                'error' => $info
            ], 401);
        }
        //you have received the request
        if (!empty($have_you_received)) {
            if ($have_you_received->status == 1) {
                $info = 'You have received a friend request from this person. You cannot send a request if you have one pending from the other side';
                return response()->json([
                    'error' => $info
                ], 401);
            }
        }
        //Send this request to this individual
        $newRequest = new FriendsModel();
        $newRequest->from_user_id = $authenticated_user->id;
        $newRequest->to_user_id = $request->friend_id;
        //$newRequest->status = 1;

        $newRequest->save();
        return response()->json([
            'error' => null,
            'msg' => 'Request sent successfully'
        ], 200);
    }
    /**
     * Accept request method
     */
    public function acceptrequest(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'friendship_id' => 'required|exists:friends,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $authenticated_user = $request->user();
        $friendship = FriendsModel::where('id', $request->friendship_id)->where('to_user_id', $authenticated_user->id)->first();

        //if this user can accept this request
        if (!empty($friendship)) {
            if ($friendship->status == 2) { //already friends
                return response()->json([
                    'error' => 'You have already accepted this request',
                ], 400);
            } else {
                $friendship->status = 2;
                $friendship->save();
                //and also adding that the reverse case is true. There must be scope of improvement

                $oppositeFriendShip = new FriendsModel();
                $oppositeFriendShip->from_user_id = $authenticated_user->id;
                $oppositeFriendShip->to_user_id = $friendship->from_user_id;
                $oppositeFriendShip->status = 2;
                $oppositeFriendShip->save();
                return response()->json([
                    'error' => null,
                    'message' => 'Friendship made successfully'
                ], 200);
            }
        }
        //this user cannot accept another user's friend-request
        return response()->json([
            'error' => 'You cannot accept another request',
        ], 403);
    }
}
