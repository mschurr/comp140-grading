{{--
  This is the master layout for our book exchange application.
  --}}


{{-- Let's load any javascript assets that the layout requires. --}}
@script( URL::asset('js/jquery.min.js') )
@script( URL::asset('grading/master.js') )

{{-- Let's load any style assets that the layout requires. --}}
@style( URL::asset('grading/master.css') )

<div class="wrapper">
	<div class="content_wrapper">
    	<div class="content">
            <div class="left">
                @yield('content')
            </div>
            <div class="right">
                <div class="right_content">
                <div class="title">Options</div>

                @if(($user = App::getSession()->user) !== null)
                	Welcome, {{{ $user }}}!<br />

                    <ul class="side_navigation">

                    	@if($user->hasPrivilege(Privilege::TeachingAssistant))
                    		<li><a href="{{{ URL::to('TeachingAssistantController@showAssignments') }}}">Grade</a></li>
                            <li><a href="{{{ URL::to('Admin.Gradebook') }}}">Gradebook</a></li>
                        @else
                            <li><a href="{{{ URL::to('/') }}}">Home</a></li>
                    	@endif

                        @if($user->hasPrivilege(Privilege::Instructor))
                            <li><a href="{{{ URL::to('Admin.Assignments@get') }}}">Manage Assignments</a></li>
                            <!--<li><a href="">Manage Instructors</a></li>
                            <li><a href="">Manage Graders</a></li>
                            <li><a href="">Manage Grader Assignments</a></li>
                            <li><a href="">Manage Students</a></li>
                            <li><a href="">Manage Grades</a></li>-->
                        @endif

                    	<li><a href="{{{ URL::to('AuthController@logout') }}}">Log out</a></li>
                    </ul>
                @else
                	<ul class="side_navigation">
                        <li><a href="{{{ URL::to('/') }}}">Home</a></li>
                    	<li><a href="{{{ URL::to('AuthController@login') }}}">Log in</a></li>
                    </ul>
                @endif
                </div>
            </div>

    	</div>
    </div>
    <br />
</div>
