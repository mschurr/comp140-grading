@extends('master')

@section('content')
	<div class="section">
		<div class="header">Manage Assignments</div>

		<a href="{{{ URL::to(array($this, 'add')) }}}" class="form_submit_style">Add</a>

		@include('PageControl')

		<table class="grid midalign">
			<thead>
				<tr>
					<th width="50">Section</th>
					<th width="110">Date</th>
					<th>Description</th>
					<th width="75">View</th>
				</tr>
			</thead>
			<tbody>
				@if(len($assignments) == 0)
					<tr><td colspan="4">There are no assignments to display.</td></tr>
				@endif
				@foreach($assignments as $a)
					<tr>
						<td>{{{$a['section']}}}</td>
						<td>{{{$a['month']}}}/{{{$a['day']}}}/{{{$a['year']}}}</td>
						<td>{{{$a['description']}}}</td>
						<td><a class="form_submit_style" href="{{{ URL::to('Admin.Assignments@view', $a['id']) }}}">View</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		@include('PageControl')

		<a href="{{{ URL::to(array($this, 'add')) }}}" class="form_submit_style">Add</a>

	</div>
@endsection