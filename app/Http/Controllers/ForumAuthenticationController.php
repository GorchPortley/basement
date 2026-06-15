<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class ForumAuthenticationController extends Controller
{
    public static function getForumToken($event) {
        $user = $event->user;
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . config('services.forum.api_key') . '; userId=1'
            ])->post(config('services.forum.url') . '/api/token', [
                        'identification' => $user->name,
                        'password' => $user->forum_password,
                        'remember' => 1]);
        if ($response->successful()) {
            $user->forum_token = $response->json()['token'];
            $user->forum_id = $response->json()['userId'];
            $user->save();
            $token = $response->json()['token'];
            Cookie::queue('flarum_remember', $token, 60 * 24 * 30, '/', '.sdlabs.cc', true, false); //This cookie is unencrypted, see bootstrap/app.php
        }
        }

    public static function registerUser($event) {
            $user = $event->user;
            $random_password = Str::random(16);
            $user->forum_password = $random_password;
            $user->save();
            $token = config('services.forum.api_key');
            $registration = Http::withHeaders(['Authorization' => 'Token ' . config('services.forum.api_key') . '; userId=1'])->
                    post(config('services.forum.url') . '/api/users', [
                        'data' => [
                            'attributes' => [
                                'username' => $user->name,
                                'email' => $user->email,
                                'password' => $random_password,
                                'isEmailConfirmed' => true
                            ]
            ]]);
        }

    public static function logoutUser($event) {
        $user = $event->user;
    $response = Http::get('http://forumsb.sdlabs.cc/logout?token=' . $user->forum_token);
    Cookie::queue('flarum_remember', 'removed', 60 * 24 * 30, '/', '.sdlabs.cc', true, false);
    Cookie::queue('flarum_session', 'removed', 60 * 24 * 30, '/', '.sdlabs.cc', true, false);
    }
    }
