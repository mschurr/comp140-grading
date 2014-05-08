@extends('master')

@section('content')
	<div class="section">
		<div class="header">Add Assignment Override</div>

		<form action="{{{ URL::to(array($this, 'addOverrideAction'), $a['id']) }}}" method="POST">
			<input type="hidden" name="_csrf" value="{{{ CSRF::make() }}}" />
			<!-- Select Teaching Assistant -->

			<!-- Select Students -->

			<!-- Confirm -->
		</form>
	</div>
@endsection