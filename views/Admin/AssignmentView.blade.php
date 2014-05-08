@extends('master')

@section('content')
	<div class="section">
		<div class="header">Assignment Information</div>
		<table class="grid">
			<tbody>
				<tr>
					<td width="30%">Description: </td>
					<td>{{{ $assignment['description'] }}}</td>
				</tr>
				<tr>
					<td>Date: </td>
					<td>{{{ $assignment['month'] }}} / {{{ $assignment['day'] }}} / {{{ $assignment['year'] }}}</td>
				</tr>
				<tr>
					<td>Section: </td>
					<td>{{{ GradingConfig::$section[$assignment['section']] }}}</td>
				</tr>
			</tbody>
		</table>
		<a href="{{{ URL::to(array($this, 'edit'), $assignment['id']) }}}" class="form_submit_style">Edit</a>
		<a class="form_submit_style" href="{{{ URL::to('Admin.Assignments@delete', $assignment['id']) }}}">Delete</a>
	</div>

	<div class="section">
		<div class="header">Grader Overrides</div>
		You may assign individual students to a different grader for this assignment.

		<table class="grid midalign">
			<thead>
				<tr>
					<th width="35%">Student</th>
					<th>Grader</th>
					<th width="100">Delete</th>
				</tr>
			</thead>
			<tbody>
				@if(count($overrides) == 0)
					<tr><td colspan="3">There are no overrides to display.</td></tr>
				@endif
				@foreach($overrides as $sid => $uid)
					<tr>
						<td>{{{ GradingSystem::getStudentName($sid) }}}</td>
						<td>{{{ GradingSystem::getGraderName($uid) }}}</td>
						<td>
							<form action="{{{ URL::to([$this, 'deleteOverride'], $assignment['id']) }}}" method="POST">
								<input type="hidden" name="_csrf" value="{{{ CSRF::make() }}}" />
								<input type="hidden" name="sid" value="{{{ $sid }}}" />
								<input type="submit" value="Delete" />
							</form>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<a href="{{{ URL::to([$this, 'addOverride'], $assignment['id']) }}}" class="form_submit_style">Add Override</a>
	</div>

	<div class="section">
		<div class="header">Grades</div>

		@if(count($grades) < $students->records)
			<div class="warning">
				Attention: {{{ ($students->records - count($grades)) }}} 
				@if(($students->records - count($grades)) == 1)
					student has 
				@else
					students have 
				@endif
				not yet received a grade.
			</div>
		@else
			<div class="success">All students have received a grade for this assignment.</div>
		@endif

		<table class="grid">
			<thead>
				<tr>
					<th>Student</th>
					<th width="25%">Grader</th>
					<th width="100">Grade</th>
				</tr>
			</thead>
			<tbody>
				@foreach($students as $s)
					<tr>
						<td>{{{ GradingSystem::getStudentName($s['id']) }}}</td>
						<td>{{{ GradingSystem::getGraderName($graders[$s['id']]) }}}</td>
						<td>{{{ GradingSystem::formatGrade($grades[$s['id']]) }}}</td>
					</tr>
				@endforeach

				@if($students->records == 0)
					<tr><td colspan="3">There are no students in this section.</td></tr>
				@endif
			</tbody>
		</table>
	</div>
@endsection