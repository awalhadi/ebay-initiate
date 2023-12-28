<?php

namespace App\Http\Controllers\Client\Contact;

use App\Http\Controllers\Controller;
use App\Repositories\SystemContact\SystemContactRepositoryInterface;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public $systemContactRepository;

    public function __construct(SystemContactRepositoryInterface $systemContactRepository)
    {
        $this->systemContactRepository = $systemContactRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Contacts'));
    }

    public function index()
    {
        $contacts = $this->systemContactRepository->getActive(null, ['country', 'state', 'city']);

        set_page_meta('System Contact');
        return view('client.contacts.index', compact('contacts'));
    }
}
