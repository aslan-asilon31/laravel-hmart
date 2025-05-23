<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use Illuminate\Http\Request;

class SendEmailController extends Controller
{
    public function index()
    {
        return view('emails.sendemail');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        dispatch(new SendMailJob($data));
        return redirect()->route('kirim-email')->with('status', 'Email berhasil dikirim');
    }
}