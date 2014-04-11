@extends('master')

@section('content')
	<div class="section">
		<div class="header">Assignments</div>

		<table class="grid">
			<thead>
				<tr>
					<th>Section</th>
					<th>Date</th>
					<th>Description</th>
					<th>Grade</th>
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
						<td><a href="{{{ URL::to('TeachingAssistantController@showAssignment', $a['id']) }}}">Grade</a></td>
				@endforeach
			</tbody>
		</table>

	</div>
@endsection