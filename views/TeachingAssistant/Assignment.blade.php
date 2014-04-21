@extends('master')

@section('content')
	<div class="section">
		<div class="header">
			{{{ $a['month'] }}}/{{{ $a['day'] }}}/{{{ $a['year'] }}}: 
			{{{ $a['description'] }}} 
			(Section {{{ $a['section'] }}})
		</div>

		@if(count($errors) > 0)
			<div class="error">Your submission contained errors; please correct them and try again.</div>
		@endif

		@if($saved)
			<div class="success">Your changes have been saved.</div>
		@endif
		{{--<div class="notice">You have not yet finished grading this assignment.</div>
		
		--}}

		<form action="{{{ URL::to(array($this, 'gradeAssignment'), $a['id']) }}}" 
		      method="POST">
			<input type="hidden" 
			       name="_csrf" 
			       value="{{{ CSRF::make('grade') }}}" />

			<table class="grid midalign">
				<thead>
					<tr>
						<th>Student</th>
						<th width="70">Grade</th>
					</tr>
				</thead>
				<tbody>
					@foreach($students as $sid => $s)
					<tr>
						<td>{{{ $s['last_name'] }}} {{{ $s['first_name'] }}} ({{{ $s['netid'] }}})</td>
						<td><input type="text" 
						           name="g{{{$sid}}}" 
						           value="{{{$grade[$sid]}}}"
						           style="width: 50px; text-align: center; 
						           @if(isset($errors['g'.$sid]))
						            border: 1px solid #FF0000;
						           @endif
						           " /></td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<input type="submit" value="Save" />
		</form>
	</div>
@endsection