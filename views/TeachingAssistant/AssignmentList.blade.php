@extends('master')

@section('content')
	<div class="section">
		<div class="header">Assignments</div>

		@include('PageControl')

		<table class="grid midalign">
			<thead>
				<tr>
					<th width="50">Section</th>
					<th width="110">Date</th>
					<th>Description</th>
					<th width="100">Grade</th>
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
						<td><a class="form_submit_style" href="{{{ URL::to('TeachingAssistantController@showAssignment', $a['id']) }}}">Grade</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		@include('PageControl')

	</div>
@endsection