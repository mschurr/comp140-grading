@extends('master')

@section('content')
	<div class="section">
		<div class="header">Assignment Deletion Confirmation</div>
		<div class="warning">Once completed, this action cannot be reversed.</div>

		@if($errors['confirm'])
			<div class="error">{{{ $errors['confirm'] }}}</div>
		@endif

		<form action="{{{ URL::to(array($this, 'deleteAction'), $a['id']) }}}" method="POST">
			<input type="hidden" name="_csrf" value="{{{ CSRF::make() }}}" />
			<table class="form">
				<tr>
					<td>Are you sure you want to delete the following assignment?</td>
				</tr>
				<tr>
					<td style="font-weight: bold">{{{ $a['description'] }}} ({{{ $a['month'] }}}/{{{$a['day']}}}/{{{$a['year']}}})</td>
				</tr>
				<tr>
					<td><label><input name="confirm" type="checkbox" /> Yes, I want to delete this assignment.</label></td>
				</tr>
				<tr>
					<td><input type="submit" value="Confirm Deletion" />
					<a class="form_submit_style" href="{{{ URL::to(array($this, 'view'), $a['id']) }}}">Cancel</a>
					</td>
				</tr>
			</table>
		</form>
	</div>
@endsection