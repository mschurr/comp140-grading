@extends('master')

@section('content')
	<div>
		{{{ $a['month'] }}}/
		{{{ $a['day'] }}}/
		{{{ $a['year'] }}} : 
		{{{ $a['description'] }}} 
		(Section {{{ $a['section'] }}})
	</div>

	<div>Grading</div>
	You are eligble to enter grades for the following students:

	@foreach($students as $s)
		{{{ $s }}}<br />
	@endforeach
@endsection