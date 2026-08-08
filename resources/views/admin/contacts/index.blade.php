@extends('admin.layouts.app')
@section('title', 'Enquiries')
@section('heading', 'Enquiries')
@section('content')
<div class="mb-4"><h1 class="fw-bold">Enquiries</h1><p class="text-secondary mb-0">Messages submitted through the website.</p></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Mobile</th><th>Email</th><th>Message</th><th>Date</th><th></th></tr></thead><tbody>@forelse($contacts as $contact)<tr><td><strong>{{ $contact->name }}</strong></td><td>{{ $contact->mobile }}</td><td>{{ $contact->email ?: '—' }}</td><td style="min-width:260px">{{ $contact->message ?: '—' }}</td><td>{{ $contact->created_at?->format('d M Y, h:i A') }}</td><td><form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this enquiry?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">No enquiries received yet.</td></tr>@endforelse</tbody></table></div>{{ $contacts->links() }}</div></div>
@endsection
