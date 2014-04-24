@extends('master')
@script(URL::asset('js/jquery.masking.min.js'))

@head
	<script type="text/javascript">
		$(document).ready(function(){
   			$("#assignment_edit input[name=date]").mask("99 / 99 / 9999",{placeholder:"_"});
		});
	</script>
@endhead

@section('content')
	<div class="section">
		<div class="header">
			@if($id)
				Assignments: Editing Assignment
			@else
				Assignments: Create New Assignment
			@endif
		</div>

		@if(count($errors) > 0)
			<div class="error">Your submission contains errors; please correct them and try again.</div>
		@endif

		<form action="{{{ $target }}}" method="POST" id="assignment_edit">
			<input type="hidden" name="id" value="{{{ $id }}}" />
			<input type="hidden" name="_csrf" value="{{{ CSRF::make() }}}" />

			<table class="form">
				<tr>
					<td class="label">Description</td>
				</tr>
				@if($errors['description'])
				<tr>
					<td class="error">{{{ $errors['description'] }}}</td>
				</td>
				@endif
				<tr>
					<td><input type="text" name="description" value="{{{ $data['description'] }}}" /></td>
				</tr>
				<tr>
					<td class="label">Date</td>
				</tr>
				@if($errors['date'])
				<tr>
					<td class="error">{{{ $errors['date'] }}}</td>
				</td>
				@endif
				<tr>
					<td><input type="text" name="date" value="{{{ $data['date'] }}}" /></td>
				</tr>
				<tr>
					<td class="label">Section</td>
				</tr>
				@if($errors['section'])
				<tr>
					<td class="error">{{{ $errors['section'] }}}</td>
				</td>
				@endif
				<tr>
					<td>
						<select name="section">
							<option value="-1"></option>
							@foreach(GradingConfig::$section as $sid => $sd)
								<option value="{{{ $sid }}}"
									@if(!is_eq_null($data['section']) && $data['section'] == $sid)
									selected="selected"
									@endif
									>{{{ $sd }}}</option>
							@endforeach
						</select>
					</td>
				</tr>
				<tr>
					<td><input type="submit" value="Save" /></td>
				</tr>
			</table>
		</form>
	</div>
@endsection