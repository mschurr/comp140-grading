@extends('master')

@section('content')
	<div class="section">
		<div class="header">Manage Assignments</div>

		<a href="{{{ URL::to(array($this, 'add')) }}}" class="form_submit_style">Add</a>

		@include('PageControl')

		<table class="grid midalign">
			<thead>
				<tr>
					<th>Section</th>
					<th>Date</th>
					<th>Description</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				@if(len($assignments) == 0)
					<tr><td colspan="5">There are no assignments to display.</td></tr>
				@endif
				@foreach($assignments as $a)
					<tr>
						<td>{{{$a['section']}}}</td>
						<td>{{{$a['month']}}}/{{{$a['day']}}}/{{{$a['year']}}}</td>
						<td>{{{$a['description']}}}</td>
						<td><a class="form_submit_style" href="{{{ URL::to('Admin.Assignments@edit', $a['id']) }}}">Edit</a></td>
						<td><a class="form_submit_style" href="{{{ URL::to('Admin.Assignments@delete', $a['id']) }}}">Delete</a></td>
				@endforeach
			</tbody>
		</table>

		@include('PageControl')

	</div>
@endsection