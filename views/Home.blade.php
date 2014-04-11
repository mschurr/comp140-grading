@extends('master')

@title('COMP 140 Teaching Assistant Portal')

@section('content')
	
	@if($user === null)
		<div class="warning">You must log in in order to access this system.</div>
	@elseif(!$user->hasPrivilege(Privilege::TeachingAssistant))
		<div class="error">You are not authorized to access this system.</div>
	@else
		<div class="success">You are a registered teaching assistant.</div>
	@endif

	<div class="text">
	
	</div>
	
@endsection