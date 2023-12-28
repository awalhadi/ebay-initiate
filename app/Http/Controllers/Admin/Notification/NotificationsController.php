<?php

namespace App\Http\Controllers\Admin\Notification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\NotificationDataTable;

class NotificationsController extends Controller
{
    
    public function index(NotificationDataTable $dataTable)
    {
        Auth::user()->unreadNotifications->markAsRead();

        set_page_meta('Notifications');
        return $dataTable->render('admin.notifications.index');
    }

    // HANDLE API CALL
    public function getUnreadJson()
    {
        $notifications = Auth::user()->unreadNotifications->take(5);

        $data = [];

        foreach ($notifications as $n){
            $data[] = $n->data;
        }

        return response()->json($data);
    }

    public function getUnreadCountJson()
    {
        $notifications = Auth::user()->unreadNotifications;

        return response()->json(count($notifications));
    }
}
