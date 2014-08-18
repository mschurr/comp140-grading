@extends('master')

@section('content')
	<div class="section">
		<div class="header">Add Assignment Override</div>

		@if(count($errors) > 0)
			<div class="error">Your submission contains errors; please correct them and try again.</div>
		@endif

		<form action="{{{ URL::to(array($this, 'addOverrideAction'), $a['id']) }}}" method="POST" name="add_overrides">
			<input type="hidden" name="_csrf" value="{{{ CSRF::make() }}}" />
			<table class="form">
				<tr>
					<td class="label">Teaching Assistant</td>
				</tr>
				@if($errors['userid'])
				<tr>
					<td class="error">{{{ $errors['userid'] }}}</td>
				</td>
				@endif
				<tr>
					<td>
						<select name="userid">
							<option value="-1"></option>
							@foreach(GradingSystem::getTeachingAssistants() as $uid)
							<option value="{{{ $uid['userid'] }}}"
								@if(isset($data['userid']) && $data['userid'] == $uid['userid'])
								selected="selected"
								@endif
								>{{{ GradingSystem::getGraderName($uid['userid']) }}}</option>
							@endforeach
						</select>
					</td>
				</tr>
				@if($errors['students'])
				<tr>
					<td class="error">{{{ $errors['students'] }}}</td>
				</td>
				@endif
				<tr>
					<td class="label">Student(s):</td>
				</tr>
				@foreach($students as $s)
				<tr>
					<td><label>
					  <input type="checkbox" 
					         name="students[]" 
					         value="{{{$s['id']}}}"
                             @if(isset($data['students']) && in_array((string)$s['id'], $data['students']))
                             checked="checked" 
                             @endif
					          /> 
					  {{{ GradingSystem::getStudentName($s['id']) }}}
					</label></td>
				</tr>
				@endforeach
				<tr>
					<td><input type="submit" value="Apply Changes" /></td>
				</tr>
			</table>
		</form>
	</div>
@endsection