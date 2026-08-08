<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Enquiries | Senet Pathshala Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Senet Pathshala Admin</a></div></nav>
<div class="container py-5">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1>Enquiries</h1><p class="text-secondary mb-0">Messages submitted through the website.</p></div><a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Mobile</th><th>Email</th><th>Message</th><th>Date</th><th></th></tr></thead><tbody>@forelse($contacts as $contact)<tr><td><strong>{{ $contact->name }}</strong></td><td>{{ $contact->mobile }}</td><td>{{ $contact->email ?: '—' }}</td><td style="min-width:260px">{{ $contact->message ?: '—' }}</td><td>{{ $contact->created_at?->format('d M Y, h:i A') }}</td><td><form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this enquiry?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">No enquiries received yet.</td></tr>@endforelse</tbody></table></div>{{ $contacts->links() }}</div></div>
</div></body></html>
