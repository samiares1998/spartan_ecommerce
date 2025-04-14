<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactForm;
use Validator;
use Str;
use File;

class ContactController extends Controller
{
    public function index(){
        $data = [
            'contacts' => ContactForm::all()->sortByDesc('id'),
            'title' => 'Contacto'
        ];

        return view('admin.contact.index', $data);
    }
}